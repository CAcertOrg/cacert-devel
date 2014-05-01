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

$query = "select UNIX_TIMESTAMP(`emailcerts`.`created`) as `created`,
			UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`emailcerts`.`expire`) as `expired`,
			`emailcerts`.`expire`,
			`emailcerts`.`revoked` as `revoke`,
			UNIX_TIMESTAMP(`emailcerts`.`revoked`) as `revoked`,
			`emailcerts`.`id`,
			`emailcerts`.`CN`,
			`emailcerts`.`serial`,
			`emailcerts`.`disablelogin` as `disablelogin`,
			`emailcerts`.`crt_name`,
			`emailcerts`.`keytype`,
			`emailcerts`.`description`
		from `emailcerts`
		where `emailcerts`.`id`='$certid' and
			`emailcerts`.`memid`='".intval($_SESSION['profile']['id'])."'";

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
?>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
	<tr>
		<td colspan="2" class="title"><?=_("Information about the certificate")?></td>
	</tr>
<?
	if($row['timeleft'] > 0)
		$verified = _("Valid");
	if($row['timeleft'] < 0)
		$verified = _("Expired");
	if($row['expired'] == 0)
		$verified = _("Pending");
	if($row['revoked'] > 0)
		$verified = _("Revoked");
	if($row['revoked'] == 0)
		$row['revoke'] = _("Not Revoked");
?>
	<tr>
		<td class="DataTD"><?=_("Status")?></td>
		<td class="DataTD"><?=$verified?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Email Address")?></td>
		<td class="DataTD"><?=(trim($row['CN'])=="" ? _("empty") : sanitizeHTML($row['CN']))?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("SerialNumber")?></td>
		<td class="DataTD"><?=sanitizeHTML($row['serial'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Revoked")?></td>
		<td class="DataTD"><?=$row['revoke']?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Expires")?></td>
		<td class="DataTD"><?=$row['expire']?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Login")?></td>
		<td class="DataTD">
			<input type="checkbox" name="disablelogin" disabled="disabled" value="1" <?=$row['disablelogin']?"":"checked='checked'"?>/>
		</td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Comment")?></td>
		<td class="DataTD"><?=htmlspecialchars($row['description'])?></td>
	</tr>
</table>
<?
	showfooter();
	exit;
}
