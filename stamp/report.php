<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2020  CAcert Inc.

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
	$arr = explode("//", $db_conn->real_escape_string(trim($_SESSION['_stamp']['ref'])), 2);
	$arr = explode("/", $arr['1'], 2);
	$ref = $arr['0'];

	$refer = $db_conn->real_escape_string(strip_tags(trim($_SESSION['_stamp']['ref'])));
	$name = clean('name');
	$email = clean('email');
	$comment = clean('comment');
	$reason = clean('reason');
	$process = clean('process');

	if($process != "" && ($_POST['pagehash'] != $_SESSION['_stamp']['pagehash'] || $_SESSION['_stamp']['pagehash'] == ""))
	{
		$errmsg = "Your report seemed to be posted is a suspicious manner, please try to re-submit it, or contact support for further help.";
		$process = "";
	}

	if($process != "" && ($name == "" || $email == "" || $comment == "" || $reason == ""))
	{
		$errmsg = "You must supply your name, a valid email address and comment.";
		$process = "";
	}

	if($process != "")
	{
		$checkemail = checkEmail($email);
		if($checkemail != "OK")
		{
			$errmsg = $checkemail;
			$process = "";
		}
	} else {
		$_SESSION['_stamp']['pagehash'] = $pagehash = md5(date("U").$ref);
	}

	if($process != "")
	{
		$IP = $db_conn->real_escape_string(trim($_SERVER['REMOTE_ADDR']));
		$iplong = ip2long($IP);
		$db_conn->query("insert into `abusereports` set `when`=NOW(), `IP`='$iplong', `url`='$refer', `name`='$name', `email`='$email',
				`comment`='$comment', `reason`='$reason'");
		$id = $db_conn->insert_id;

		$body  = "New Abuse Report has been lodged via the the Stamp Interface:\n\n";
		$body .= "Reported ID: $id\n";
		$body .= "Reported IP: $IP\n";
		$body .= "From: $name <$email>\n";
		$body .= "URL: $refer\n";
		$body .= "Reason: $reason\n";
		$body .= "Comment: $comment\n";

		sendmail("cacert-abuse@lists.cacert.org", "[CAcert.org] Abuse Report.", $body, "website@cacert.org", "", "", "CAcert Website");
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>CAcert.org Abuse Report!</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
 <div id="pagecell1">
  <div id="pageName"><br>
    <h2><a href="http<? if($_SERVER['HTTPS']=="on") { echo "s"; } ?>://www.cacert.org">
	<img src="http<? if($_SERVER['HTTPS']=="on") { echo "s"; } ?>://www.cacert.org/images/cacert3.png" border="0" alt="CAcert.org logo"></a></h2>
<? if($_SERVER['HTTPS']!="on") { ?>
<div id="googlead"><br><script type="text/javascript"><!--
google_ad_client = "pub-0959373285729680";
google_alternate_color = "ffffff";
google_ad_width = 234;
google_ad_height = 60;
google_ad_format = "234x60_as";
google_ad_type = "text";
google_ad_channel = "";
//--></script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script></div>
<? } ?>
  </div>
  <div id="content">
    <div class="story">
      <h3>Report abuse for <?=$ref?></h3>
<? if($process == "") { ?>
<? if($errmsg != "") { ?><p style="color:red"><?=$errmsg?></p><? } else { ?><br /><? } ?>
      <form method="post" action="report.php">
	<label for="refer">URL: </label><input type="text" name="refer" value="<?=$refer?>" readonly="1" /><br />
	<label for="name">Name: </label><input type="text" name="name" value="<?=$name?>" /><br />
	<label for="email">Email: </label><input type="text" name="email" value="<?=$email?>" /><br />
	<label for="reason">Reason: </label><select name="reason">
		<option value='invalid'<? if($reason == "invalid") { echo " selected"; } ?>>Invalid Domain</option>
		<option value='phishing'<? if($reason == "phishing") { echo " selected"; } ?>>Phishing Site</option>
		<option value='spam'<? if($reason == "spam") { echo " selected"; } ?>>Spam</option>
		<option value='other'<? if($reason == "other") { echo " selected"; } ?>>Other</option>
		</select><br />
	<label for="comment">Comment/Other: </label><input type="text" name="comment" value="<?=$comment?>" /><br /><br />
	<label for="sub">&nbsp;</label><input type="submit" name="process" value="Report Site"><br />
	<input type="hidden" name="pagehash" value="<?=$pagehash?>">
      </form>
<? } else { ?>
      <p>We thank you for your attention to detail, your report has been accepted and we will tend to your report as soon as humanly possible.</p>
<? } ?>
    </div>
   </div>
</body>
</html>
