<?php /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2011  CAcert Inc.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

// failWithId()
require_once 'general.php';


/**
* Checks whether the given CSR contains a vulnerable key
*
* @param $csr string
* 		The CSR to be checked
* @param $encoding string [optional]
* 		The encoding the CSR is in (for the "-inform" parameter of OpenSSL,
* 		currently only "PEM" (default) or "DER" allowed)
* @return string containing the reason if the key is considered weak,
* 		empty string otherwise
*/
function checkWeakKeyCSR($csr, $encoding = "PEM")
{
	$encoding = escapeshellarg($encoding);
	$status = runCommand("openssl req -inform $encoding -text -noout",
	                     $csr, $csrText);
	if ($status === true) {
		return failWithId("checkWeakKeyCSR(): Failed to start OpenSSL");
	}
	
	if ($status !== 0 || $csrText === "") {
		return _("I didn't receive a valid Certificate Request. Hit ".
			"the back button and try again.");
	}
	
	return checkWeakKeyText($csrText);
}

/**
 * Checks whether the given X509 certificate contains a vulnerable key
 *
 * @param $cert string
 * 		The X509 certificate to be checked
 * @param $encoding string [optional]
 * 		The encoding the certificate is in (for the "-inform" parameter of
 * 		OpenSSL, currently only "PEM" (default), "DER" or "NET" allowed)
 * @return string containing the reason if the key is considered weak,
 * 		empty string otherwise
 */
function checkWeakKeyX509($cert, $encoding = "PEM")
{
	$encoding = escapeshellarg($encoding);
	$status = runCommand("openssl x509 -inform $encoding -text -noout",
	                     $cert, $certText);
	if ($status === true) {
		return failWithId("checkWeakKeyCSR(): Failed to start OpenSSL");
	}
	
	if ($status !== 0 || $certText === "") {
		return _("I didn't receive a valid Certificate Request. Hit ".
			"the back button and try again.");
	}
	
	return checkWeakKeyText($certText);
}

/**
 * Checks whether the given SPKAC contains a vulnerable key
 *
 * @param $spkac string
 * 		The SPKAC to be checked
 * @param $spkacname string [optional]
 * 		The name of the variable that contains the SPKAC. The default is
 * 		"SPKAC"
 * @return string containing the reason if the key is considered weak,
 * 		empty string otherwise
 */
function checkWeakKeySPKAC($spkac, $spkacname = "SPKAC")
{
	$spkacname = escapeshellarg($spkacname);
	$status = runCommand("openssl spkac -spkac $spkacname", $spkac, $spkacText);
	if ($status === true) {
		return failWithId("checkWeakKeyCSR(): Failed to start OpenSSL");
	}
	
	if ($status !== 0 || $spkacText === "") {
		return _("I didn't receive a valid Certificate Request. Hit the ".
			"back button and try again.");
	}
	
	return checkWeakKeyText($spkacText);
}

/**
 * Checks whether the given text representation of a CSR or a SPKAC contains
 * a weak key
 *
 * @param $text string
 * 		The text representation of a key as output by the
 * 		"openssl <foo> -text -noout" commands
 * @return string containing the reason if the key is considered weak,
 * 		empty string otherwise
 */
function checkWeakKeyText($text)
{
	/* Which public key algorithm? */
	if (!preg_match('/^\s*Public Key Algorithm: ([^\s]+)$/m', $text,
	$algorithm))
	{
		return failWithId("checkWeakKeyText(): Couldn't extract the ".
					"public key algorithm used");
	} else {
		$algorithm = $algorithm[1];
	}


	if ($algorithm === "rsaEncryption")
	{
		if (!preg_match('/^\s*RSA Public Key: \((\d+) bit\)$/m', $text,
		$keysize))
		{
			return failWithId("checkWeakKeyText(): Couldn't parse the RSA ".
						"key size");
		} else {
			$keysize = intval($keysize[1]);
		}
			
		if ($keysize < 1024)
		{
			return sprintf(_("The keys that you use are very small ".
						"and therefore insecure. Please generate stronger ".
						"keys. More information about this issue can be ".
						"found in %sthe wiki%s"),
					"<a href='//wiki.cacert.org/WeakKeys#SmallKey'>",
					"</a>");
		} elseif ($keysize < 2048) {
			// not critical but log so we have some statistics about
			// affected users
			trigger_error("checkWeakKeyText(): Certificate for small ".
						"key (< 2048 bit) requested", E_USER_NOTICE);
		}
			
			
		$debianVuln = checkDebianVulnerability($text, $keysize);
		if ($debianVuln === true)
		{
			return sprintf(_("The keys you use have very likely been ".
						"generated with a vulnerable version of OpenSSL which ".
						"was distributed by debian. Please generate new keys. ".
						"More information about this issue can be found in ".
						"%sthe wiki%s"),
					"<a href='//wiki.cacert.org/WeakKeys#DebianVulnerability'>",
					"</a>");
		} elseif ($debianVuln === false) {
			// not vulnerable => do nothing
		} else {
			return failWithId("checkWeakKeyText(): Something went wrong in".
					"checkDebianVulnerability()");
		}
			
		if (!preg_match('/^\s*Exponent: (\d+) \(0x[0-9a-fA-F]+\)$/m', $text,
		$exponent))
		{
			return failWithId("checkWeakKeyText(): Couldn't parse the RSA ".
						"exponent");
		} else {
			$exponent = $exponent[1]; // exponent might be very big =>
			//handle as string using bc*()

			if (bccomp($exponent, "3") === 0)
			{
				return sprintf(_("The keys you use might be insecure. ".
							"Although there is currently no known attack for ".
							"reasonable encryption schemes, we're being ".
							"cautious and don't allow certificates for such ".
							"keys. Please generate stronger keys. More ".
							"information about this issue can be found in ".
							"%sthe wiki%s"),
						"<a href='//wiki.cacert.org/WeakKeys#SmallExponent'>",
						"</a>");
			} elseif (!(bccomp($exponent, "65537") >= 0 &&
			(bccomp($exponent, "100000") === -1 ||
			// speed things up if way smaller than 2^256
			bccomp($exponent, bcpow("2", "256")) === -1) )) {
				// 65537 <= exponent < 2^256 recommended by NIST
				// not critical but log so we have some statistics about
				// affected users
				trigger_error("checkWeakKeyText(): Certificate for ".
							"unsuitable exponent '$exponent' requested",
				E_USER_NOTICE);
			}
		}
	}

	/* No weakness found */
	return "";
}

/**
 * Reimplement the functionality of the openssl-vulnkey tool
 *
 * @param $text string
 * 		The text representation of a key as output by the
 * 		"openssl <foo> -text -noout" commands
 * @param $keysize int [optional]
 * 		If the key size is already known it can be provided so it doesn't
 * 		have to be parsed again. This also skips the check whether the key
 * 		is an RSA key => use wisely
 * @return TRUE if key is vulnerable, FALSE otherwise, NULL in case of error
 */
function checkDebianVulnerability($text, $keysize = 0)
{
	$keysize = intval($keysize);

	if ($keysize === 0)
	{
		/* Which public key algorithm? */
		if (!preg_match('/^\s*Public Key Algorithm: ([^\s]+)$/m', $text,
		$algorithm))
		{
			trigger_error("checkDebianVulnerability(): Couldn't extract ".
					"the public key algorithm used", E_USER_WARNING);
			return null;
		} else {
			$algorithm = $algorithm[1];
		}
			
		if ($algorithm !== "rsaEncryption") return false;
			
		/* Extract public key size */
		if (!preg_match('/^\s*RSA Public Key: \((\d+) bit\)$/m', $text,
		$keysize))
		{
			trigger_error("checkDebianVulnerability(): Couldn't parse the ".
					"RSA key size", E_USER_WARNING);
			return null;
		} else {
			$keysize = intval($keysize[1]);
		}
	}

	// $keysize has been made sure to contain an int
	$blacklist = "/usr/share/openssl-blacklist/blacklist.RSA-$keysize";
	if (!(is_file($blacklist) && is_readable($blacklist)))
	{
		if (in_array($keysize, array(512, 1024, 2048, 4096)))
		{
			trigger_error("checkDebianVulnerability(): Blacklist for ".
						"$keysize bit keys not accessible. Expected at ".
						"$blacklist", E_USER_ERROR);
			return null;
		}
			
		trigger_error("checkDebianVulnerability(): $blacklist is not ".
				"readable. Unsupported key size?", E_USER_WARNING);
		return false;
	}


	/* Extract RSA modulus */
	if (!preg_match('/^\s*Modulus \(\d+ bit\):\n'.
				'((?:\s*[0-9a-f][0-9a-f]:(?:\n)?)+[0-9a-f][0-9a-f])$/m',
	$text, $modulus))
	{
		trigger_error("checkDebianVulnerability(): Couldn't extract the ".
				"RSA modulus", E_USER_WARNING);
		return null;
	} else {
		$modulus = $modulus[1];
		// strip whitespace and colon leftovers
		$modulus = str_replace(array(" ", "\t", "\n", ":"), "", $modulus);
			
		// when using "openssl xxx -text" first byte was 00 in all my test
		// cases but 00 not present in the "openssl xxx -modulus" output
		if ($modulus[0] === "0" && $modulus[1] === "0")
		{
			$modulus = substr($modulus, 2);
		} else {
			trigger_error("checkDebianVulnerability(): First byte is not ".
					"zero", E_USER_NOTICE);
		}
			
		$modulus = strtoupper($modulus);
	}


	/* calculate checksum and look it up in the blacklist */
	$checksum = substr(sha1("Modulus=$modulus\n"), 20);

	// $checksum and $blacklist should be safe, but just to make sure
	$checksum = escapeshellarg($checksum);
	$blacklist = escapeshellarg($blacklist);
	$debianVuln = runCommand("grep $checksum $blacklist");
	if ($debianVuln === 0) // grep returned something => it is on the list
	{
		return true;
	} elseif ($debianVuln === 1) {
		// grep returned nothing
		return false;
	} else {
		trigger_error("checkDebianVulnerability(): Something went wrong ".
				"when looking up the key with checksum $checksum in the ".
				"blacklist $blacklist", E_USER_ERROR);
		return null;
	}

	// Should not get here
	return null;
}
