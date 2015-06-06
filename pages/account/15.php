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

$certid = 0; if(array_key_exists('cert',$_REQUEST)) $certid=intval($_REQUEST['cert']);

$query = "select * from `domaincerts`,`domains` where `domaincerts`.`id`='$certid' and
			`domains`.`memid`='".intval($_SESSION['profile']['id'])."' and
			`domains`.`id`=`domaincerts`.`domid`";
$res = mysql_query($query);
if(mysql_num_rows($res) <= 0)
{
	echo _("No such certificate attached to your account.");
	showfooter();
	exit;
}
$row = mysql_fetch_assoc($res);

$crtname=escapeshellarg($row['crt_name']);
$cert = shell_exec("/usr/bin/openssl x509 -in $crtname");

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
	$cert = shell_exec("/usr/bin/openssl x509 -in $crtname $outform");

	header("Content-Type: application/pkix-cert");
	header("Content-Length: ".strlen($cert));

	$fname = sanitizeFilename($row['CN']);
	if ($fname=="") $fname="certificate";
	header("Content-Disposition: attachment; filename=\"${fname}.${extension}\"");

	echo $cert;
	exit;
} else {
	echo "<h3>".
		_("Below is your Server Certificate")."</h3>";
	echo "<pre>$cert</pre>";
	echo '<ul class="no_indent">';

	echo "<li><a href='account.php?id=$id&amp;cert=$certid&amp;format=pem'>".
		_("Download the certificate in PEM format")."</a></li>\n";
	echo "<li><a href='account.php?id=$id&amp;cert=$certid&amp;format=der'>".
		_("Download the certificate in DER format")."</a></li>\n";
	echo '</ul>';
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
		<td class="DataTD"><?=_("Comment")?></td>
		<td class="DataTD"><?=htmlspecialchars($row['description'])?></td>
	</tr>
</table>
<?
	exit;
}
?>
