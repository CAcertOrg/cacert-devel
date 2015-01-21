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

if(!function_exists("showheader"))
{
	function showheader($title = "CAcert.org", $title2 = "")
	{

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=$title?></title>
<? if($_SESSION['_config']['header'] != "") { ?><?=$_SESSION['_config']['header']?><? } ?>
<link rel="stylesheet" href="/styles/default.css" type="text/css">
<link href="http://my.rsscache.com/www.cacert.org/rss.php" rel="alternate" type="application/rss+xml" title="rss">
</head>
<body>
 <div id="pagecell1">
  <div id="pageName"><br>
    <h2><a href="http://<?=$_SESSION['_config']['normalhostname']?>"><img src="https://www.CAcert.org/images/cacert4.png" border="0" alt="CAcert.org logo"></a></h2>
<div id="googlead"><h2><?=_("Free digital certificates!")?></h2></div>
  </div>
  <div id="pageNav">
    <div class="relatedLinks">
      <h3><?=_("Join CAcert.org")?></h3>
      <a href="http://<?=$_SESSION['_config']['normalhostname']?>/"><?=_("Main Website")?></a> 
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=1"><?=_("Join")?></a> 
    </div>
    <div class="relatedLinks">
      <h3><?=_("My Account")?></h3>
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=4"><?=_("Normal Login")?></a> 
      <a href="https://<?=$_SESSION['_config']['securehostname']?>/index.php?id=4"><?=_("Cert Login")?></a>
      <a href="https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=5"><?=_("Lost Password")?></a>
    </div>
  </div>
  <div id="content">
    <div class="story">
      <h3><?=$title2?></h3>
<? if($_SESSION['_config']['errmsg'] != "") { ?>
<p class="error_fatal"><? echo $_SESSION['_config']['errmsg']; $_SESSION['_config']['errmsg'] = ""; ?></p>
<? } ?>
<?
	}
}

if(!function_exists("showfooter"))
{
	function showfooter()
	{
?>
      </div>
    </div>
  <div id="siteInfo">
        <a href="//wiki.cacert.org/FAQ/AboutUs"><?=_("About Us")?></a> | <a href="/index.php?id=13"><?=_("Donations")?></a> | <a href="http://wiki.cacert.org/wiki/CAcertIncorporated"><?=_("Association Membership")?></a> |
        <a href="/index.php?id=10"><?=_("Privacy Policy")?></a> |
        <a href="/index.php?id=51"><?=_("Mission Statement")?></a> | <a href="/index.php?id=11"><?=_("Contact Us")?></a> |
        <a href="/index.php?id=19"><?=_("Further Information")?></a> | &copy;2002-<?=date("Y")?> <?=_("by CAcert")?></div>
</div>  
</body>             
</html><?
	}
}
?>
