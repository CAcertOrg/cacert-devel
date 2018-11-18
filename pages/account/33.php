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
*/ ?>
<?
	$query = "select * from `orginfo` where `id`='".intval($_REQUEST['orgid'])."'";
	$row = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], $query));
	
	// Reset session variables regarding OrgAdmin's, present empty form
  if (array_key_exists('email',$_SESSION['_config']))     $_SESSION['_config']['email']=""; 
  if (array_key_exists('OU',$_SESSION['_config']))        $_SESSION['_config']['OU'] = "";
  if (array_key_exists('masteracc',$_SESSION['_config'])) $_SESSION['_config']['masteracc'] = 0;
  if (array_key_exists('comments',$_SESSION['_config']))  $_SESSION['_config']['comments'] = "";	
	
?>
<form method="post" action="account.php">
<input type="hidden" name="orgid" value="<?=intval($_REQUEST['orgid'])?>">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><? printf(_("New Admin for %s"), ($row['O'])); ?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
    <td class="DataTD"><input type="text" name="email" value=""></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Department")?>:</td>
    <td class="DataTD"><input type="text" name="OU" value=""></td>
  </tr>
<? if($_SESSION['profile']['orgadmin'] == 1) { ?>
  <tr>
    <td class="DataTD"><?=_("Master Account")?>:</td>
    <td class="DataTD"><select name="masteracc">
		<option value="0">No</option>     // make default option as of SA telco 2011-08-02 on bug 966
		<option value="1">Yes</option>
	</select></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><?=_("Comments")?>:</td>
    <td class="DataTD"><textarea name="comments" cols="30" rows="5"></textarea></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Add")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="csrf" value="<?=make_csrf('orgadmadd')?>" />
</form>
