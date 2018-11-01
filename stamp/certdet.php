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
	$org = $invalid = 0;
	$tz = intval($_REQUEST['tz']);
	$now = date("Y-m-d", gmmktime("U") + ($tz * 3600));

	$arr = explode("//", mysqli_real_escape_string($_SESSION['mconn'], trim($_REQUEST['refer'])), 2);
	$arr = explode("/", $arr['1'], 2);
	$ref = $arr['0'];

        $arr = explode("//", mysqli_real_escape_string($_SESSION['mconn'], trim($_SERVER['HTTP_REFERER'])), 2);
        $arr = explode("/", $arr['1'], 2);
        $siteref = $arr['0'];

	if($siteref != "")
		$siterefer = $_SERVER['HTTP_REFERER'];
	else
		$siterefer = $_REQUEST['refer'];

        if($ref == "" || ($ref != $siteref && $siteref != ""))
        {
		$invalid = 2;
        } else {
		if($_SESSION['_stamp']['ref'] == "")
			$_SESSION['_stamp']['ref'] = $siterefer;
		list($invalid, $info) = checkhostname($ref);
	}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>CAcert.org Certificate Details!</title>
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
      <h3>SSL Certificate Details for <?=$ref?></h3>
<? if($invalid == 0) { ?>
      <p>
	Status: Valid<br />
	Valid From: <?=$info['issued']?> GMT<br />
	Valid To: <?=$info['expire']?> GMT<br />
	Subject: <a href="#" title="<?=$info['subject']?>" onClick="return false;"><?=substr($info['subject'],0,80)?></a><br />
	Organisation: <? if($info['org'] == 0) { ?>N/A<? } else { echo $info['O'].", ".$info['L']." ".$info['ST']." ".$info['C']; } ?><br />
	Verification: <? if($info['points'] >= 50) { echo "Person had been assured at time of issue with at least 50 points."; } 
		else if($info['org'] == 1) { ?>This organisation was assured at the time the certificate was issued.<? } ?></p>
<? } else { ?>
	<p style="color:red">This site has potentially abused CAcert logos and Copyrights, please report it so we may further investigate.</p>
<? } ?>
	<p><a href="report.php">Problem with this site? Please report it</a></p>
    </div>
   </div>
</body>
</html>
