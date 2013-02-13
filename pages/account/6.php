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
	
	showheader(_("My CAcert.org Account!"));
	echo "<h3>"._("Install your certificate")."</h3>\n";
	
	echo "<p><a href='account.php?id=6&amp;cert=$certid&amp;install'>".
		_("Install the certificate into your browser").
		"</a></p>\n";
	
	echo "<p><a href='account.php?id=6&amp;cert=$certid&amp;format=pem'>".
		_("Download the certificate in PEM format")."</a></p>\n";
	
	echo "<p><a href='account.php?id=6&amp;cert=$certid&amp;format=der'>".
		_("Download the certificate in DER format")."</a></p>\n";
	
	showfooter();
	exit;
}


?>
<!-- to be converted to JavaScript -->
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

