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
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="3" class="title"><? printf(_("%s's Domains"), $row['O']); ?> (<a href="account.php?id=28&amp;orgid=<?=intval($row['id'])?>"><?=_("Add")?></a>)</td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Domain")?></td>
    <td class="DataTD"><?=_("Edit")?></td>
    <td class="DataTD"><?=_("Delete")?></td>
  </tr>
<?
	$query = "select * from `orgdomains` where `orgid`='".intval($_REQUEST['orgid'])."'";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{ ?>
  <tr>
    <td class="DataTD"><?=sanitizeHTML($row['domain'])?></a></td>
    <td class="DataTD"><a href="account.php?id=29&amp;orgid=<?=intval($row['orgid'])?>&amp;domid=<?=intval($row['id'])?>"><?=_("Edit")?></a></td>
    <td class="DataTD"><a href="account.php?id=30&amp;orgid=<?=intval($row['orgid'])?>&amp;domid=<?=intval($row['id'])?>"><?=_("Delete")?></a></td>
  </tr>
<? } ?>
</table>
