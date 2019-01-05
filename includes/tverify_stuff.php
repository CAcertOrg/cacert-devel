<?php /*
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
<title><?php echo $title?></title>
<?php if($_SESSION['_config']['header'] != "") { ?><?php echo $_SESSION['_config']['header']?><?php } ?>
<link rel="stylesheet" href="/styles/default.css" type="text/css">
<link href="http://my.rsscache.com/www.cacert.org/rss.php" rel="alternate" type="application/rss+xml" title="rss">
</head>
<body>
 <div id="pagecell1">
  <div id="pageName"><br>
    <h2><a href="http://<?php echo $_SESSION['_config']['normalhostname']?>"><img src="https://www.CAcert.org/images/cacert4.png" border="0" alt="CAcert.org logo"></a></h2>
<div id="googlead"><h2><?php echo _("Free digital certificates!")?></h2></div>
  </div>
  <div id="pageNav">
    <div class="relatedLinks">
      <h3><?php echo _("Join CAcert.org")?></h3>
      <a href="http://<?php echo $_SESSION['_config']['normalhostname']?>/"><?php echo _("Main Website")?></a>
      <a href="https://<?php echo $_SESSION['_config']['normalhostname']?>/index.php?id=1"><?php echo _("Join")?></a>
    </div>
    <div class="relatedLinks">
      <h3><?php echo _("My Account")?></h3>
      <a href="https://<?php echo $_SESSION['_config']['normalhostname']?>/index.php?id=4"><?php echo _("Normal Login")?></a>
      <a href="https://<?php echo $_SESSION['_config']['securehostname']?>/index.php?id=4"><?php echo _("Cert Login")?></a>
      <a href="https://<?php echo $_SESSION['_config']['normalhostname']?>/index.php?id=5"><?php echo _("Lost Password")?></a>
    </div>
  </div>
  <div id="content">
    <div class="story">
      <h3><?php echo $title2?></h3>
<?php if($_SESSION['_config']['errmsg'] != "") { ?>
<p><font color="#ff0000" size="+2"><?php echo $_SESSION['_config']['errmsg']; $_SESSION['_config']['errmsg'] = ""; ?> </font></p>
<?php } ?>
<?php 	}
}

if(!function_exists("showfooter"))
{
	function showfooter()
	{
?>
      </div>
    </div>
  <div id="siteInfo">
        <a href="//wiki.cacert.org/FAQ/AboutUs"><?php echo _("About Us")?></a> | <a href="/index.php?id=13"><?php echo _("Donations")?></a> | <a href="http://wiki.cacert.org/wiki/CAcertIncorporated"><?php echo _("Association Membership")?></a> |
        <a href="/index.php?id=10"><?php echo _("Privacy Policy")?></a> |
        <a href="/index.php?id=51"><?php echo _("Mission Statement")?></a> | <a href="/index.php?id=11"><?php echo _("Contact Us")?></a> |
        <a href="/index.php?id=19"><?php echo _("Further Information")?></a> | &copy;2002-<?php echo date("Y")?> <?php echo _("by CAcert")?></div>
</div>  
</body>             
</html><?php 	}
}
?>
