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
*/ ?>
<?
	$certid = 0; if(array_key_exists('cert',$_REQUEST)) $certid=intval($_REQUEST['cert']);

	$query = "select * from `emailcerts` where `id`='$certid' and `memid`='".intval($_SESSION['profile']['id'])."'";
	$query = "select UNIX_TIMESTAMP(`emailcerts`.`created`) as `created`,
			UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`emailcerts`.`expire`) as `expired`,
			`emailcerts`.`expire` as `expires`, 
			`emailcerts`.`revoked` as `revoke`,
			UNIX_TIMESTAMP(`emailcerts`.`revoked`) as `revoked`, 
			`emailcerts`.`id`,
			`emailcerts`.`CN`,
			`emailcerts`.`serial`,
			emailcerts.disablelogin as `disablelogin`,
			`emailcerts`.`crt_name`,
			`emailcerts`.`keytype`,
			`emailcerts`.`description`
			from `emailcerts`
			where `emailcerts`.`id`='$certid' and `emailcerts`.`memid`='".intval($_SESSION['profile']['id'])."'";


	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
	{
		showheader(_("My CAcert.org Account!"));
		echo _("No such certificate attached to your account.");
		showfooter();
		exit;
	}
	$row = mysql_fetch_assoc($res);

	$crtname=escapeshellarg($row['crt_name']);
	$cert = `/usr/bin/openssl x509 -in $crtname`;

	if($row['keytype'] == "NS")
	{
		if(array_key_exists('install',$_REQUEST) && $_REQUEST['install'] == 1)
		{
			header("Content-Type: application/x-x509-user-cert");
			header("Content-Length: ".strlen($cert));
			$fname=sanitizeFilename($row['CN']);
			if($fname=="") $fname="certificate";
			header('Content-Disposition: inline; filename="'.$fname.'.crt"');
			echo $cert;
			exit;
		} else {
			showheader(_("My CAcert.org Account!"));
			echo "<h3>"._("Installing your certificate")."</h3>\n";
			echo "<p>"._("You are about to install a certificate, if you are using mozilla/netscape based browsers you will not be informed that the certificate was installed successfully, you can go into the options dialog box, security and manage certificates to view if it was installed correctly however.")."</p>\n";
			echo "<p><a href='account.php?id=6&amp;cert=$certid&amp;install=1'>"._("Click here")."</a> "._("to install your certificate.")."</p>\n";
			showfooter();
			exit;
		}
	} else {
		showheader(_("My CAcert.org Account!"));
?>
<h3><?=_("Installing your certificate")?></h3>

<p><?=_("Hit the 'Install your Certificate' button below to install the certificate into MS IE 5.x and above.")?>

<OBJECT classid="clsid:127698e4-e730-4e5c-a2b1-21490a70c8a1" codebase="/xenroll.cab#Version=5,131,3659,0" id="cec">
<?=_("You must enable ActiveX for this to work.")?>
</OBJECT>
<FORM >
<INPUT TYPE=BUTTON NAME="CertInst" VALUE="<?=_("Install Your Certificate")?>">
</FORM>

</P>

<SCRIPT LANGUAGE=VBS>
   Sub CertInst_OnClick
      certchain  = _
<?
	$lines = explode("\n", $cert);
	if(is_array($lines))
	foreach($lines as $line)
	{
		$line = trim($line);
		if($line != "-----END CERTIFICATE-----")
			echo "\"$line\" & _\n";
		else {
			echo "\"$line\"\n";
			break;
		}
	}
?>

      On Error Resume Next

      Dim obj
      Set obj=CreateObject("X509Enrollment.CX509Enrollment")
      If IsObject(obj) Then
        obj.Initialize(1)
        obj.InstallResponse 0,certchain,0,""
        if err.number<>0 then
          msgbox err.Description
	else
	  msgbox "<?=_("Certificate installed successfully. Please don't forget to backup now")?>"
	end if
      else




      cec.DeleteRequestCert = FALSE
      err.clear

      cec.WriteCertToCSP = TRUE
      cec.acceptPKCS7(certchain)
      if err.number <> 0 Then
         cec.WriteCertToCSP = FALSE
      end if
      err.clear
      cec.acceptPKCS7(certchain)
      if err.number <> 0 then
         errorMsg = "<?=_("Certificate installation failed!")?>" & chr(13) & chr(10) & _
                        "(Error code " & err.number & ")"
         msgRes   = MsgBox(errorMsg, 0, "<?=_("Certificate Installation Error")?>")
      else
         okMsg    = "<?=_("Personal Certificate Installed.")?>" & chr(13) & chr(10) & _
                        "See Tools->Internet Options->Content->Certificates"
         msgRes   = MsgBox(okMsg, 0, "<?=_("Certificate Installation Complete!")?>")
      end if
     End If
   End Sub
</SCRIPT>

<p><?=_("Your certificate:")?></p>
<pre><?=$cert?></pre>
<?
 
		showfooter();
		exit;
	}
?>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Information about the certificte")?></td>
  </tr>
<?
	$res = mysql_query($query);
	if(mysql_num_rows($res) > 0)
	{
	while($row = mysql_fetch_assoc($res))
	{
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
    <td class="DataTD"><?=_("Renew/Revoke/Delete")?></td>
<? if($verified != _("Pending") && $verified != _("Revoked")) { ?>
    <td class="DataTD"><input type="checkbox" name="revokeid[]" value="<?=$row['id']?>"></td>
<? } else if($verified != _("Revoked")) { ?>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?=$row['id']?>"></td>
<? } else { ?>
    <td class="DataTD">&nbsp;</td>
<? } ?>    
  </tr>
  <tr>
    <td class="DataTD"><?=_("Status")?></td>
    <td class="DataTD"><?=$verified?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Email Address")?></td>
    <td class="DataTD"><?=(trim($row['CN'])=="" ? _("empty") : $row['CN'])?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("SerialNumber")?></td>
    <td class="DataTD"><?=$row['serial']?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Revoked")?></td>
    <td class="DataTD"><?=$row['revoke']?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Expires")?></td>
    <td class="DataTD"><?=$row['revoke']?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Login")?></td>
    <td class="DataTD">
      <input type="checkbox" name="disablelogin" value="1" <?=$row['disablelogin']?"":"checked='checked'"?>/>
    </td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Comment")?></td>
    <td class="DataTD"><input type="text" name="description" maxlength="100" size=100 value="<?=htmlspecialchars($row['description'])?>"></td>
  </tr>
    <? } ?>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="change" value="<?=_("Change settings")?>"> </td>

  </tr>
<? } ?>
</table>
<input type="hidden" name="oldid" value="6">
<input type="hidden" name="certid" value="<?=$certid?>">
</form>
