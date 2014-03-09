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
		return failWithId("checkWeakKeyX509(): Failed to start OpenSSL");
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
		return failWithId("checkWeakKeySPKAC(): Failed to start OpenSSL");
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
					"public key algorithm used.\nData:\n$text");
	} else {
		$algorithm = $algorithm[1];
	}


	if ($algorithm === "rsaEncryption")
	{
		if (!preg_match('/^\s*RSA Public Key: \((\d+) bit\)$/m', $text, $keysize))
		{
			return failWithId("checkWeakKeyText(): Couldn't parse the RSA ".
						"key size.\nData:\n$text");
		} else {
			$keysize = intval($keysize[1]);
		}

		if ($keysize < 2048)
		{
			return sprintf(_("The keys that you use are very small ".
						"and therefore insecure. Please generate stronger ".
						"keys. More information about this issue can be ".
						"found in %sthe wiki%s"),
					"<a href='//wiki.cacert.org/WeakKeys#SmallKey'>",
					"</a>");
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
					"checkDebianVulnerability().\nKeysize: $keysize\n".
					"Data:\n$text");
		}

		if (!preg_match('/^\s*Exponent: (\d+) \(0x[0-9a-fA-F]+\)$/m', $text,
		$exponent))
		{
			return failWithId("checkWeakKeyText(): Couldn't parse the RSA ".
						"exponent.\nData:\n$text");
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

		// No weakness found
		return "";
	} // End RSA

/*
//Fails to work due to outdated OpenSSL 0.9.8o
//For this to work OpenSSL 1.0.1f or newer is required
//which is currently unavailable on the systems
//If DSA2048 or longer is used the CSR hangs pending on the signer.
	if ($algorithm ===  "dsaEncryption")
	{
		if (!preg_match('/^\s*Public Key Algorithm:\s+dsaEncryption\s+pub:\s+([0-9a-fA-F:\s]+)\s+P:\s+([0-9a-fA-F:\s]+)\s+Q:\s+([0-9a-fA-F:\s]+)\s+G:\s+([0-9a-fA-F:\s]+)\s+$/sm', $text, $keydetail))
		{
			return failWithId("checkWeakKeyText(): Couldn't parse the DSA ".
					"key size.\nData:\n$text");
		}

		$key_pub = strtr(preg_replace("/[^0-9a-fA-F]/", "", $keydetail[1]), "ABCDEF", "abcdef");
		$key_P = strtr(preg_replace("/[^0-9a-fA-F]/", "", $keydetail[2]), "ABCDEF", "abcdef");
		$key_Q = strtr(preg_replace("/[^0-9a-fA-F]/", "", $keydetail[3]), "ABCDEF", "abcdef");
		$key_G = strtr(preg_replace("/[^0-9a-fA-F]/", "", $keydetail[4]), "ABCDEF", "abcdef");

		//Verify the numbers provided by the client
		$num_pub = @gmp_init($key_pub, 16);
		$num_P = @gmp_init($key_P, 16);
		$num_Q = @gmp_init($key_Q, 16);
		$num_G = @gmp_init($key_G, 16);

		$bit_P = ltrim(gmp_strval($num_P, 2), "0");
		$keysize = strlen($bit_P);

		if ($keysize < 2048) {
			return sprintf(_("The keys that you use are very small ".
						"and therefore insecure. Please generate stronger ".
						"keys. More information about this issue can be ".
						"found in %sthe wiki%s"),
					"<a href='//wiki.cacert.org/WeakKeys#SmallKey'>",
					"</a>");
		}

		//Following checks based on description of key generation in Wikipedia
		//These checks do not ensure a strong key, but at least check for enough sanity in the key material
		// cf. https://en.wikipedia.org/wiki/Digital_Signature_Algorithm#Key_generation

		//Check that P is prime
		if(!gmp_testprime($num_P)) {
			return failWithId("checkWeakKeyText(): The supplied DSA ".
					"key does seem to have a non-prime public modulus.\nData:\n$text");
		}

		//Check that Q is prime
		if(!gmp_testprime($num_Q)) {
			return failWithId("checkWeakKeyText(): The supplied DSA ".
					"key does seem to have a non-prime Q-value.\nData:\n$text");
		}

		//Check if P-1 is diviseable by Q
		if(0 !== gmp_cmp("1", gmp_mod($num_P, $num_Q))) {
			return failWithId("checkWeakKeyText(): The supplied DSA ".
					"key does seem to have P mod Q === 1 (i.e. P-1 is not diviseable by Q).\nData:\n$text");
		}

		//Check the numbers are all less than the public modulus P
		if(0 <= gmp_cmp($num_Q, $num_P) || 0 <= gmp_cmp($num_G, $num_P) || 0 <= gmp_cmp($num_pub, $num_P)) {
			return failWithId("checkWeakKeyText(): The supplied DSA ".
					"key does seem to be normalized to have Q < P, G < P and pub < P.\nData:\n$text");
		}

		// No weakness found
		return "";
	} // End DSA
*/


	return _("The keys you supplied use an unrecognized algorithm. ".
			"For security reasons these keys can not be signed by CAcert.");
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
					"the public key algorithm used.\nData:\n$text",
					E_USER_WARNING);
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
					"RSA key size.\nData:\n$text", E_USER_WARNING);
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
				"RSA modulus.\nData:\n$text", E_USER_WARNING);
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
