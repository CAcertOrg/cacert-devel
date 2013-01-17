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

require_once($_SESSION['_config']['filepath'].'/includes/lib/l10n.php');

if(!function_exists("showheader"))
{
	function showbodycontent($title = "CAcert.org", $title2 = "")
	{
?> <div id="pagecell1">
  <div id="pageName"><br>
    <div id="pageLogo"><a href="http://<?=$_SESSION['_config']['normalhostname']?>"><img src="/images/cacert4.png" border="0" alt="CAcert.org logo"></a></div>
<div id="googlead"><? if(!array_key_exists('HTTPS',$_SERVER) || $_SERVER['HTTPS'] != "on") { ?><script type="text/javascript">
<!--
google_ad_client = "pub-0959373285729680";
google_alternate_ad_url = "http://www.cacert.org/";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_color_link = "000000";
google_color_url = "000000";
google_color_text = "000000";
google_color_border = "FFFFFF";
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script><? } else {
?><h2><?=_("Free digital certificates!")?></h2><? } ?></div>
  </div>
  <div id="pageNav">
    <div class="relatedLinks">
      <h3><?=_("Join CAcert.org")?></h3>
      <? if(array_key_exists('mconn',$_SESSION) && $_SESSION['mconn']) { ?>
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=1"><?=_("Join")?></a>
      <? } ?>
      <a href="/policy/CAcertCommunityAgreement.html"><?=_("Community Agreement")?></a>
      <a href="/index.php?id=3"><?=_("Root Certificate")?></a>
    </div>
    <? if(array_key_exists('mconn',$_SESSION) && $_SESSION['mconn']) { ?>
    <div class="relatedLinks">
      <h3 class="pointer"><?=_("My Account")?></h3>
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=4"><?=_("Password Login")?></a>
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=5"><?=_("Lost Password")?></a>
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=4&amp;noauto=1"><?=_("Net Cafe Login")?></a>
      <a href="https://<?=$_SESSION['_config']['securehostname']?>/index.php?id=4"><?=_("Certificate Login")?></a>
    </div>
    <? } ?>
    <? include("about_menu.php"); ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('trans')">+ <?=_("Translations")?></h3>
      <ul class="menu" id="trans"><? foreach(L10n::$translations as $key => $val) { ?><li><a href="<?=$_SERVER['SCRIPT_NAME']?>?id=<?=intval(array_key_exists('id',$_REQUEST)?$_REQUEST['id']:0)?>&amp;lang=<?=$key?>"><?=$val?></a></li><? } ?></ul>
    </div>
    <? if(array_key_exists('mconn',$_SESSION) && $_SESSION['mconn']) { ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('recom')"><?=_("Advertising")?></h3>
      <ul class="menu" id="recom"><?
	$query = "select * from `advertising` where `expires`>NOW() and `active`=1";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
		echo "<li><a href='$row[link]' target='_blank'>$row[title]</a></li>";
?></ul>
    </div>
    <? } ?>
  </div>
  <div id="content">
    <div class="story">
<? if($title2!="") echo "<h3>$title2</h3>"; ?>
<? if($_SESSION['_config']['errmsg'] != "") { ?>
<p><font color="#ff0000" size="+2"><? echo $_SESSION['_config']['errmsg']; $_SESSION['_config']['errmsg'] = ""; ?> </font></p>
<? } ?>
<?

	}

	function showheader($title = "CAcert.org", $title2 = "")
	{
		global $id;

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=$title?></title>
<? if(array_key_exists("header",$_SESSION['_config']) && $_SESSION['_config']['header'] != "") { ?><?=$_SESSION['_config']['header']?><? } ?>
<link rel="stylesheet" href="/styles/default.css" type="text/css">
<link href="http://blog.CAcert.org/feed/" rel="alternate" type="application/rss+xml" title="rss">
<script language="JavaScript" type="text/javascript">
function explode(e) {
    if (document.getElementById(e).style.display == 'none') {
        document.getElementById(e).style.display = 'block';
    } else {
        document.getElementById(e).style.display = 'none';
    }
}

function hideall() {
        var Nodes = document.getElementsByTagName('ul')
        var max = Nodes.length
        for(var i = 0;i < max;i++) {
                var nodeObj = Nodes.item(i)
		if (nodeObj.className == "menu" && nodeObj.id != "recom") {
	                nodeObj.style.display = 'none';
		}
        }
}
</script>
</head>
<body onload="hideall();">
<?
		showbodycontent($title,$title2);
	}
}

if(!function_exists("showfooter"))
{
	function showfooter()
	{
?>
      </div>
    </div>
  <? include("sponsorinfo.php") ?>
  <div id="siteInfo">
	<a href="//wiki.cacert.org/FAQ/AboutUs"><?=_("About Us")?></a> | <a href="/index.php?id=13"><?=_("Donations")?></a> | <a href="http://wiki.cacert.org/wiki/CAcertIncorporated"><?=_("Association Membership")?></a> |
        <a href="/policy/PrivacyPolicy.html"><?=_("Privacy Policy")?></a> |
        <a href="/index.php?id=51"><?=_("Mission Statement")?></a> | <a href="/index.php?id=11"><?=_("Contact Us")?></a> |
	&copy;2002-<?=date("Y")?> <?=_("by CAcert")?></div>
</div>
</body>
</html><?
	}
}
?>
