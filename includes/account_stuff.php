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

	$id = 0; if(array_key_exists("id",$_REQUEST)) $id=intval($_REQUEST['id']);
	$expand="";

	function showheader($title = "CAcert.org", $title2 = "")
	{
		global $id, $PHP_SELF;
	$expand="";
	$tmpid = $id;
	if($PHP_SELF == "/wot.php")
		$tmpid = $id + 500;
	if($PHP_SELF == "/gpg.php")
		$tmpid = $id + 1000;
	if($PHP_SELF == "/disputes.php")
		$tmpid = $id + 1500;
	if($PHP_SELF == "/advertising.php")
		$tmpid = $id + 2000;

	switch($tmpid)
	{
		case 1:
		case 2: $expand = " explode('emailacc');"; break;
		case 3:
		case 4:
		case 5:
		case 6: $expand = " explode('clicerts');"; break;
		case 7:
		case 8:
		case 9: $expand = " explode('domains');"; break;
		case 10:
		case 11:
		case 12:
		case 15: $expand = " explode('servercert');"; break;
		case 13:
		case 14:
		case 36:
		case 41:
		case 507:
		case 508:
		case 513: $expand = " explode('mydetails');"; break;
		case 16:
		case 17:
		case 18:
		case 19: $expand = " explode('clientorg');"; break;
		case 20:
		case 21:
		case 22:
		case 23: $expand = " explode('serverorg');"; break;
		case 24:
		case 25:
		case 26:
		case 27:
		case 28:
		case 29:
		case 30:
		case 31:
		case 32:
		case 33:
		case 34:
		case 35: $expand = " explode('orgadmin');"; break;
		case 42:
		case 43:
		case 44:
		case 45:
		case 46:
		case 47:
		case 48:
		case 49:
		case 50:
		case 54:
		case 53: $expand = " explode('sysadmin');"; break;
		case 500:
		case 501:
		case 502:
		case 503:
		case 504:
		case 505:
		case 506:
		case 509:
		case 510:
		case 511:
		case 512: $expand = " explode('WoT');"; break;
		case 1000:
		case 1001:
		case 1002:
		case 1003:
		case 1004:
		case 1005:
		case 1006:
		case 1007:
		case 1008:
		case 1009:
		case 1010: $expand = " explode('gpg');"; break;
		case 1500:
		case 1501:
		case 1502:
		case 1503:
		case 1504:
		case 1505:
		case 1506:
		case 1507:
		case 1508:
		case 1509:
		case 1510: $expand = " explode('disputes');"; break;
		case 2000:
		case 2001:
		case 2002:
		case 2003:
		case 2004:
		case 2005:
		case 2006:
		case 2007:
		case 2008:
		case 2009: $expand = " explode('advertising');"; break;
	}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=$title?></title>
<? if(array_key_exists('header',$_SESSION) && $_SESSION['_config']['header'] != "") { ?><?=$_SESSION['_config']['header']?><? } ?>
<link rel="stylesheet" href="/styles/default.css" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
		if (nodeObj.className == "menu") {
	                nodeObj.style.display = 'none';
		}
        }
}
</script>
</head>
<body onload="hideall(); explode('home');<?=$expand?>">
 <div id="pagecell1">
  <div id="pageName"><br>
    <div id="pageLogo"><a href="http://<?=$_SESSION['_config']['normalhostname']?>"><img src="/images/cacert4.png" border="0" alt="CAcert.org logo"></a></div>
    <div id="googlead"><h2><?=_("Free digital certificates!")?></h2></div>
  </div>
  <div id="pageNav">
    <div class="relatedLinks">
      <h3>CAcert.org</h3>
      <ul class="menu" id="home"><li><a href="/index.php"><?=_("Go Home")?></a></li><li><a href="account.php?id=logout"><?=_("Logout")?></a></li></ul>
    </div>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('mydetails')">+ <?=_("My Details")?></h3>
      <ul class="menu" id="mydetails"><li><a href="account.php?id=13"><?=_("Edit")?></a></li><li><a href="account.php?id=14"><?=_("Change Password")?></a></li><li><a href="account.php?id=41"><?=_("Default Language")?></a></li><li><a href="wot.php?id=8"><?=_("My Listing")?></a></li><li><a href="wot.php?id=13"><?=_("My Location")?></a></li><li><a href="account.php?id=36"><?=_("My Alert Settings")?></a></li><li><a href="wot.php?id=10"><?=_("My Points")?></a></li><?
	if($_SESSION['profile']['id'] == 1 || $_SESSION['profile']['id'] == 5897)
		echo "<li><a href='sqldump.php'>SQL Dump</a></li>";
	?></ul>
    </div>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('emailacc')">+ <?=_("Email Accounts")?></h3>
      <ul class="menu" id="emailacc"><li><a href="account.php?id=1"><?=_("Add")?></a></li><li><a href="account.php?id=2"><?=_("View")?></a></li></ul>
    </div>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('clicerts')">+ <?=_("Client Certificates")?></h3>
      <ul class="menu" id="clicerts"><li><a href="account.php?id=3"><?=_("New")?></a></li><li><a href="account.php?id=5"><?=_("View")?></a></li></ul>
    </div>
<? if($_SESSION['profile']['points'] >= 50) { ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('gpg')">+ <?=_("GPG/PGP Keys")?></h3>
      <ul class="menu" id="gpg"><li><a href="gpg.php?id=0"><?=_("New")?></a></li><li><a href="gpg.php?id=2"><?=_("View")?></a></li></ul>
    </div>
<? } ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('domains')">+ <?=_("Domains")?></h3>
      <ul class="menu" id="domains"><li><a href="account.php?id=7"><?=_("Add")?></a></li><li><a href="account.php?id=9"><?=_("View")?></a></li></ul>
    </div>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('servercert')">+ <?=_("Server Certificates")?></h3>
      <ul class="menu" id="servercert"><li><a href="account.php?id=10"><?=_("New")?></a></li><li><a href="account.php?id=12"><?=_("View")?></a></li></ul>
    </div>
<? if(mysql_num_rows(mysql_query("select * from `org` where `memid`='".intval($_SESSION['profile']['id'])."'")) > 0 || $_SESSION['profile']['orgadmin'] == 1) { ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('clientorg')">+ <?=_("Org Client Certs")?></h3>
      <ul class="menu" id="clientorg"><li><a href="account.php?id=16"><?=_("New")?></a></li><li><a href="account.php?id=18"><?=_("View")?></a></li></ul>
    </div>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('serverorg')">+ <?=_("Org Server Certs")?></h3>
      <ul class="menu" id="serverorg"><li><a href="account.php?id=20"><?=_("New")?></a></li><li><a href="account.php?id=22"><?=_("View")?></a></li></ul>
    </div>
<? } ?>
<? if(mysql_num_rows(mysql_query("select * from `org` where `memid`='".intval($_SESSION['profile']['id'])."' and `masteracc`='1'")) > 0 || $_SESSION['profile']['orgadmin'] == 1) { ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('orgadmin')">+ <?=_("Org Admin")?></h3>
      <ul class="menu" id="orgadmin"><? if($_SESSION['profile']['orgadmin'] == 1) { ?><li><a href="account.php?id=24"><?=_("New Organisation")?></a></li><li><a href="account.php?id=25"><?=_("View Organisations")?></a></li><? } ?><li><a href="account.php?id=35"><?=_("View")?></a></li></ul>
    </div>
<? } ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('WoT')">+ <?=_("CAcert Web of Trust")?></h3>
      <ul class="menu" id="WoT"><li><a href="wot.php?id=0"><?=_("About")?></a></li><li><a href="wot.php?id=12"><?=_("Find an Assurer")?></a></li><li><a href="wot.php?id=3"><?=_("Rules")?></a></li><li><? if($_SESSION['profile']['assurer'] != 1) { ?><a href="wot.php?id=2"><?=_("Becoming an Assurer")?></a><? } else { ?><a href="wot.php?id=5"><?=_("Assure Someone")?></a><? } ?></li><li><a href="wot.php?id=4"><?=_("Trusted ThirdParties")?></a></li><? if($_SESSION['profile']['points'] >= 500) { ?><li><a href="wot.php?id=11"><div style="white-space:nowrap"><?=_("Organisation Assurance")?></div></a></li><? } ?><li><a href="account.php?id=55"><?=_("Training")?></a></li></ul>
    </div>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('WoTForms')">+ <?=_("CAP Forms")?></h3><?
        $name = $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname']." ".$_SESSION['profile']['lname']." ".$_SESSION['profile']['suffix'];
        while(strstr($name, "  "))
                $name = str_replace("  ", " ", $name);
        $extra = "?name=".urlencode($name);
	$extra .= "&amp;dob=".urlencode($_SESSION['profile']['dob']);
        $extra .= "&amp;email=".urlencode($_SESSION['profile']['email']);

	$extra2 = "?assurer=".urlencode($name)."&amp;date=now&amp;maxpoints=".maxpoints();
?>
      <ul class="menu" id="WoTForms">
         <li><a href="/cap.php<?=$extra?>">A4 - <?=_("WoT Form")?></a></li>
	 <li><a href="/cap.php<?=$extra?>&amp;format=letter">US - <?=_("WoT Form")?></a></li>
	<? /* <li><div style="white-space:nowrap"><a href="/ttp.php<?=$extra?>">A4 - <?=_("TTP Form")?></a></div></li>
	 <li><div style="white-space:nowrap"><a href="/ttp.php<?=$extra?>&amp;format=letter">US - <?=_("TTP Form")?></a></div></li> */
	?>
	 <? if($_SESSION['profile']['points'] > 100) { ?><li><div style="white-space:nowrap"><a href="/cap.php<?=$extra2?>">A4 - <?=_("Assurance Form")?></a></div></li>
	 <li><div style="white-space:nowrap"><a href="/cap.php<?=$extra2?>&amp;format=letter">US - <?=_("Assurance Form")?></a></div></li>
	 <? } ?>
	 <? /*
	  <li><div style="white-space:nowrap"><a href="/ttp.php">A4 - <?=_("Blank TTP Form")?></a></div></li>
	  <li><div style="white-space:nowrap"><a href="/ttp.php?&amp;format=letter">US - <?=_("Blank TTP Form")?></a></div></li>
	 */ ?>
	 <li><div style="white-space:nowrap"><a href="/cap.php">A4 - <?=_("Blank CAP Form")?></a></div></li>
	 <li><div style="white-space:nowrap"><a href="/cap.php?&amp;format=letter">US - <?=_("Blank CAP Form")?></a></div></li></ul>
    </div>
<? if($_SESSION['profile']['admin'] == 1 || $_SESSION['profile']['locadmin'] == 1) { ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('sysadmin')">+ <?=_("System Admin")?></h3>
      <ul class="menu" id="sysadmin"><? if($_SESSION['profile']['admin'] == 1) { ?><li><a href="account.php?id=42"><?=_("Find User")?></a></li><li><a href="account.php?id=48"><?=_("Find Domain")?></a></li><? } if($_SESSION['profile']['locadmin'] == 1) { ?><li><a href="account.php?id=53"><?=_("Location DB")?></a></li><? } ?></ul>
    </div>
<? } ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('disputes')">+ <?=_("Disputes/Abuses")?></h3>
      <ul class="menu" id="disputes"><li><a href="disputes.php?id=0"><?=_("More Information")?></a></li><li><a href="disputes.php?id=1"><?=_("Email Dispute")?></a></li><li><a href="disputes.php?id=2"><?=_("Domain Dispute")?></a></li><? if($_SESSION['profile']['admin'] == 1) { ?><li><a href="disputes.php?id=3"><?=_("Abuses")?></a></li><? } ?></ul>
    </div>
<? if($_SESSION['profile']['adadmin'] >= 1) { ?>
    <div class="relatedLinks">
      <h3 class="pointer" onclick="explode('advertising')">+ <?=_("Advertising")?></h3>
      <ul class="menu" id="advertising"><li><a href="advertising.php?id=1"><?=_("New Ad")?></a></li><li><a href="advertising.php?id=0"><?=_("View Ads")?></a></li></ul>
    </div>
<? } ?>
  </div>
  <div id="content">
    <div class="story">
      <h3><?=$title2?></h3>
<? if($_SESSION['_config']['errmsg'] != "") { ?>
<p><font color="#ff0000" size="+2"><? echo $_SESSION['_config']['errmsg']; $_SESSION['_config']['errmsg'] = ""; ?> </font></p>
<? } ?>
<?
	}

	function showfooter()
	{
?>
      </div>
    </div>
  <div id="siteInfo"><a href="//wiki.cacert.org/FAQ/AboutUs"><?=_("About Us")?></a> | <a href="account.php?id=38"><?=_("Donations")?></a> | <a href="http://wiki.cacert.org/wiki/CAcertIncorporated"><?=_("Association Membership")?></a> |
	<a href="/policy/PrivacyPolicy.html"><?=_("Privacy Policy")?></a> | <a href="account.php?id=40"><?=_("Contact Us")?></a>
		| &copy;2002-<?=date("Y")?> <?=_("by CAcert")?></div>
</div>  
</body>             
</html><?
	}
