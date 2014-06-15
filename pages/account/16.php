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
<?
if (array_key_exists('emails',$_SESSION['_config']) && is_array($_SESSION['_config']['emails'])) {
	$i = 1;
	foreach($_SESSION['_config']['emails'] as $val) {
?>
  <tr>
    <td class="DataTD"><label for="email<?=$i?>"><?=_("Email")?></label></td>
    <td class="DataTD"><input type="text" id="email<?=$i?>" name="emails[]" value="<?=$val?>"/></td>
  </tr>
<?
		$i++;
	}
} ?>
  <tr>
    <td class="DataTD"><label for="email0"><?=_("Email")?></td>
    <td class="DataTD"><input type="text" id="email0" name="emails[]"/></td>
  </tr>
  <tr>
    <td class="DataTD"><label for="name"><?=_("Name")?></label></td>
    <td class="DataTD"><input type="text" id="name" name="name" value="<?=array_key_exists('name',$_SESSION['_config'])?($_SESSION['_config']['name']):''?>"/></td>
  </tr>
  <tr>
    <td class="DataTD"><label for="OU"><?=_("Department")?></label></td>
    <td class="DataTD"><input type="text" id="OU" name="OU" value="<?=array_key_exists('OU',$_SESSION['_config'])?(sanitizeHTML($_SESSION['_config']['OU'])):''?>"/></td>
  </tr>

  <tr name="expertoff" style="display:none">
    <td class="DataTD">
      <input type="checkbox" id="expertbox" name="expertbox" onchange="showExpert(this.checked)" />
    </td>
    <td class="DataTD">
      <label for="expertbox"><?=_("Show advanced options")?></label>
    </td>
  </tr>
  <tr name="expert">
    <td class="DataTD" colspan="2" align="left">
        <input type="radio" id="root1" name="rootcert" value="1" /> <label for="root1"><?=_("Sign by class 1 root certificate")?></label><br />
        <input type="radio" id="root2" name="rootcert" value="2" checked="checked" /> <label for="root2"><?=_("Sign by class 3 root certificate")?></label><br />
        <?=str_replace("\n", "<br>\n", wordwrap(_("Please note: If you use a certificate signed by the class 3 root, the class 3 root certificate needs to be imported into your email program as well as the class 1 root certificate so your email program can build a full trust path chain."), 60))?>
    </td>
  </tr>

  <tr name="expert">
    <td class="DataTD" colspan="2" align="left">
      <?=_("Hash algorithm used when signing the certificate:")?><br />
      <?
      foreach (HashAlgorithms::getInfo() as $algorithm => $display_info) {
      ?>
        <input type="radio" id="hash_alg_<?=$algorithm?>" name="hash_alg" value="<?=$algorithm?>" <?=(HashAlgorithms::$default === $algorithm)?'checked="checked"':''?> />
        <label for="hash_alg_<?=$algorithm?>"><?=$display_info['name']?><?=$display_info['info']?' - '.$display_info['info']:''?></label><br />
      <?
      }
      ?>
    </td>
  </tr>

<? if($_SESSION['profile']['codesign'] && $_SESSION['profile']['points'] >= 100) { ?>
  <tr name="expert">
    <td class="DataTD" colspan="2" align="left">
      <input type="checkbox" id="codesign" name="codesign" value="1" />
      <label for="codesign"><?=_("Code Signing")?></label>
    </td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="2" align="left">
      <label for="description"><?=_("Optional comment, only used in the certificate overview")?></label><br />
      <input type="text" id="description" name="description" maxlength="80" size="80" />
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2">
      <input type="submit" name="add_email" value="<?=_("Add Another Email Address")?>">
      <input type="submit" name="process" value="<?=_("Next")?>" />
    </td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>

<script language="javascript">
function showExpert(a)
{
  b=document.getElementsByName("expert");
  for(i=0;b.length>i;i++)
  {
    if(!a) {b[i].setAttribute("style","display:none"); }
    else {b[i].removeAttribute("style");}
  }
  b=document.getElementsByName("expertoff");
  for(i=0;b.length>i;i++)
  {
    b[i].removeAttribute("style");
  }

}
showExpert(false);
</script>
