<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2008  CAcert Inc.

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

// Get certificate information
$certid = 0;
if(array_key_exists('cert',$_REQUEST)) {
	$certid = intval($_REQUEST['cert']);
}

$query = "select * from `emailcerts`
			where `id`='$certid'
			and `memid`='".intval($_SESSION['profile']['id'])."'";
$res = mysql_query($query);
if(mysql_num_rows($res) <= 0) {
	showheader(_("My CAcert.org Account!"));
	echo _("No such certificate attached to your account.");
	showfooter();
	exit;
}
$row = mysql_fetch_assoc($res);


if (array_key_exists('format', $_REQUEST)) {
	// Which output format?
	if ($_REQUEST['format'] === 'der') {
		$outform = '-outform DER';
		$extension = 'cer';
	} else {
		$outform = '-outform PEM';
		$extension = 'crt';
	}

	$crtname=escapeshellarg($row['crt_name']);
	$cert = `/usr/bin/openssl x509 -in $crtname $outform`;

	header("Content-Type: application/pkix-cert");
	header("Content-Length: ".strlen($cert));

	$fname = sanitizeFilename($row['CN']);
	if ($fname=="") $fname="certificate";
	header("Content-Disposition: attachment; filename=\"${fname}.${extension}\"");

	echo $cert;
	exit;

} elseif (array_key_exists('install', $_REQUEST)) {
	if (array_key_exists('HTTP_USER_AGENT',$_SERVER) &&
			strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {

		// Handle IE
		//TODO

	} else {
		// All other browsers
		$crtname=escapeshellarg($row['crt_name']);
		$cert = `/usr/bin/openssl x509 -in $crtname -outform DER`;

		header("Content-Type: application/x-x509-user-cert");
		header("Content-Length: ".strlen($cert));

		$fname = sanitizeFilename($row['CN']);
		if ($fname=="") $fname="certificate";
		header("Content-Disposition: inline; filename=\"${fname}.cer\"");

		echo $cert;
		exit;
	}

} else {
	showheader(_("My CAcert.org Account!"), _("Install your certificate"));
	echo '<ul class="no_indent">';
	echo "<li><a href='account.php?id=$id&amp;cert=$certid&amp;install'>".
		_("Install the certificate into your browser").
		"</a></li>\n";

	echo "<li><a href='account.php?id=$id&amp;cert=$certid&amp;format=pem'>".
		_("Download the certificate in PEM format")."</a></li>\n";

	echo "<li><a href='account.php?id=$id&amp;cert=$certid&amp;format=der'>".
		_("Download the certificate in DER format")."</a></li>\n";
	echo '</ul>';

	// Allow to directly copy and paste the cert in PEM format
	$crtname=escapeshellarg($row['crt_name']);
	$cert = `/usr/bin/openssl x509 -in $crtname -outform PEM`;
	echo "<pre>$cert</pre>";

	showfooter();
	exit;
}
