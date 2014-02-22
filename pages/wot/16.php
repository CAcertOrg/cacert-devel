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
include_once("../includes/shutdown.php");
require_once("../includes/lib/l10n.php");
?>
<?
if(array_key_exists('error',$_SESSION['_config']) && $_SESSION['_config']['error'] != "")
{
    ?><font color="orange" size="+1">
      <? echo _("ERROR").": ".$_SESSION['_config']['error'] ?>
    </font>
    <?unset($_SESSION['_config']['error']);
}
?>
<? if(array_key_exists('noemailfound',$_SESSION['_config']) && $_SESSION['_config']['noemailfound'] == 1) { ?>
<!--<form method="post" action="wot.php">
<input type="hidden" name="email" value="<?=sanitizeHTML($_POST['email'])?>"><br>
<select name="reminder-lang">
    <?
//if($_SESSION['_config']['reminder-lang'] == "")
//    $_SESSION['_config']['reminder-lang'] = L10n::get_translation();
//    foreach(L10n::$translations as $key => $val)
//    {
//        echo "<option value='$key'";
//        if($key == $_SESSION['_config']['reminder-lang'])
//            echo " selected";
//        echo ">$val</option>\n";
//    }
    ?>
        </select><br>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="submit" name="reminder" value="<?=_("Send reminder notice")?>">
</form> -->
    <? unset($_SESSION['_config']['noemailfound']); } ?>
<form method="post" action="wot.php" name="form1">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_('Check Assurer Status')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_('Email to check')?>:</td>
<? if(array_key_exists('remindersent',$_SESSION['_config']) && $_SESSION['_config']['remindersent'] == 1) { unset($_SESSION['_config']['remindersent']) ?>
    <td class="DataTD"><input type="text" name="email" id="email" value=""></td>
    <? } else { ?>
    <td class="DataTD"><input type="text" name="email" id="email" value="<?=array_key_exists('email',$_POST)?sanitizeHTML($_POST['email']):""?>"></td>
        <? } ?>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
<SCRIPT LANGUAGE="JavaScript">
//<![CDATA[
	function my_init()
	{
		document.getElementById("email").focus();
	}

	window.onload = my_init();
//]]>
</script>