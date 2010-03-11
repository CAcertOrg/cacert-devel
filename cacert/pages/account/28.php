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
	$row = mysql_fetch_assoc(mysql_query($query));
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><? printf(_("New Domain for %s"), sanitizeHTML($row['O'])); ?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Domain")?>:</td>
    <td class="DataTD"><input type="text" name="domainname" value="<?=array_key_exists('domain',$_SESSION['_config'])?sanitizeHTML($_SESSION['_config']['domain']):""?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Add")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=intval($id)?>">
<input type="hidden" name="orgid" value="<?=intval($_REQUEST['orgid'])?>">

</form>
