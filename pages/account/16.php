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
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("New Client Certificate")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Add")?></td>
    <td class="DataTD"><?=_("Address")?></td>
<? if(array_key_exists('emails',$_SESSION['_config']) && is_array($_SESSION['_config']['emails']))
	foreach($_SESSION['_config']['emails'] as $val) { ?>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
    <td class="DataTD"><input type="text" name="emails[]" value="<?=$val?>"></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
    <td class="DataTD"><input type="text" name="emails[]"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Name")?>:</td>
    <td class="DataTD"><input type="text" name="name" value="<?=array_key_exists('name',$_SESSION['_config'])?($_SESSION['_config']['name']):''?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Department")?>:</td>
    <td class="DataTD"><input type="text" name="OU" value="<?=array_key_exists('OU',$_SESSION['_config'])?($_SESSION['_config']['OU']):''?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2" align="left">
        <input type="radio" name="rootcert" value="1" checked> <?=_("Sign by class 1 root certificate")?><br>
        <input type="radio" name="rootcert" value="2"> <?=_("Sign by class 3 root certificate")?><br>
        <?=str_replace("\n", "<br>\n", wordwrap(_("Please note: The class 3 root certificate needs to be imported into your email program as well as the class 1 root certificate so your email program can build a full trust path chain. Until we are included in browsers this might not be a desirable option for most people"), 60))?>
    </td>
  </tr>
<? if($_SESSION['profile']['codesign'] && $_SESSION['profile']['points'] >= 100) { ?>
  <tr>
    <td class="DataTD" colspan="2" align="left"><input type="checkbox" name="codesign" value="1" /><?=_("Code Signing")?></td>
  </tr>
<? } ?>
   <tr>
   <td class="DataTD" colspan="2" align="left">
      <?=_("Optional comment, only used in the certifictate overview")?><br>
       <input type="text" name="description" maxlength="80" size=80>
   </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="add_email" value="<?=_("Another Email")?>">
			<input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
