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

	$query = "select * from `orgemailcerts`,`org` where `orgemailcerts`.`id`='".intval($certid)."' and
			`org`.`memid`='".intval($_SESSION['profile']['id'])."' and
			`org`.`orgid`=`orgemailcerts`.`orgid`";
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
	$cert = shell_exec("/usr/bin/openssl x509 -in $crtname");

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
			echo "<p><a href='account.php?id=19&amp;cert=$certid&amp;install=1'>"._("Click here")."</a> "._("to install your certificate.")."</p>\n";
			showfooter();
			exit;
		}
	} else if($row['keytype'] == "VI"){
		showheader(_("My CAcert.org Account!"));
		echo "<pre>".$cert."</pre>";
		showfooter();
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
   End Sub
</SCRIPT>

<? 
		showfooter();
		exit;
	}
?>
