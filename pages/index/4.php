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
*/ ?>

<?
   if(!$GLOBALS["db_conn"])
   {
     echo _("This function is currently unavailable. Please come back later.");
     exit;
   }
?>

<? if($_SESSION['_config']['hostname'] == $_SESSION['_config']['securehostname']) { ?>
<p><?=sprintf(_("Warning! You've attempted to log into the system with a client certificate, but the login failed due to the certificate being expired, revoked, disabled for certificate login, or simply not valid for this site. You can login using your email/pass phrase to get a new certificate, by clicking on %sPassword Login%s on the right side of this page."),"<a href='https://".$_SESSION['_config']['normalhostname']."/index.php?id=4'>", "</a>")?></p>
<? } else { ?>
<style>
.box2 {width:100%;text-align:center;}
.box {background:#F5F7F7;border:2px solid #cccccc;margin:0px auto;height:auto;width:300px;padding:1em;}
.smalltext {font-size:10px;}
label {width:100px;display:block;float:left;}
text {width:166px;display:block;float:left;}
br {clear:left;}
h1 {font-size:1.9em;text-align:center;}
</style>
<div class='box2'>
<div class='box'>
<form action='index.php' method='post'<? if(array_key_exists("noauto",$_REQUEST) && $_REQUEST['noauto'] == 1) echo " autocomplete='off'"; ?>>
<? if(array_key_exists("noauto",$_REQUEST) && $_REQUEST['noauto'] == 1) { ?><input type="hidden" name="noauto" value="1"><? } ?>
<h1><?=_("Login")?></h1>
<p class='smalltext'><?=_("Warning! This site requires cookies to be enabled to ensure your privacy and security. This site uses session cookies to store temporary values to prevent people from copying and pasting the session ID to someone else exposing their account, personal details and identity theft as a result.")?></p>
<label for="email"><?=_("Email Address")?>:</label><input type='text' name="email" value="<?=sanitizeHTML(array_key_exists("email",$_REQUEST)?$_REQUEST['email']:"")?>" <? if(array_key_exists('notauto',$_REQUEST) && $_REQUEST['noauto'] == 1) echo " autocomplete='off'"; ?>/><br />
<label for="pword"><?=_("Pass Phrase")?>:</label><input type='password' name='pword' autocomplete="off"/><br />
<input type='submit' name="process" value="<?=_("Login")?>" /><br /><br />
<a href='https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=4'><?=_("Password Login")?></a> -
<a href='https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=5'><?=_("Lost Password")?></a> -
<a href='https://<?=$_SESSION['_config']['normalhostname']?>/index.php?id=4&amp;noauto=1'><?=_("Net Cafe Login")?></a><br />
<p class='smalltext'><?=sprintf(_("If you are having trouble with your username or password, please visit our %swiki page%s for more information"), "<a href='http://wiki.cacert.org/wiki/FAQ/LostPasswordOrAccount' target='_new'>", "</a>");?></p>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
</div>
</div>
<? }
if(array_key_exists("oldlocation",$_SESSION['_config']) && $_SESSION['_config']['oldlocation']!="")
{
  echo "<br/><center>"._("If you want to use certificate login instead of username+password, please")." <a href='https://secure.cacert.org/".sanitizeHTML($_SESSION['_config']['oldlocation'])."'>"._("click here")."</a></center>";
}
?>
