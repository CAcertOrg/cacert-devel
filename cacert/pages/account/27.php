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
	$row = mysql_fetch_assoc(mysql_query("select * from `orginfo` where `id`='".intval($_REQUEST['orgid'])."'"));
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Edit Organisation")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Organisation Name")?>:</td>
    <td class="DataTD"><input type="text" name="O" value="<?=$row['O']?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Contact Email")?>:</td>
    <td class="DataTD"><input type="text" name="contact" value="<?=($row['contact'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Town/Suburb")?>:</td>
    <td class="DataTD"><input type="text" name="L" value="<?=($row['L'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("State/Province")?>:</td>
    <td class="DataTD"><input type="text" name="ST" value="<?=($row['ST'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Country")?>:</td>
    <td class="DataTD"><input type="text" name="C" value="<?=($row['C'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Comments")?>:</td>
    <td class="DataTD"><textarea name="comments" cols=15 rows=5><?=($row['comments'])?></textarea></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=intval($id)?>">
<input type="hidden" name="orgid" value="<?=intval($_REQUEST['orgid'])?>">
<input type="hidden" name="csrf" value="<?=make_csrf('orgdetchange')?>" />
</form>
