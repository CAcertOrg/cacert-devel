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
*/ ?>
<?php 	$query = "select * from `orginfo` where `id`='".intval($_REQUEST['orgid'])."'";
	$row = mysql_fetch_assoc(mysql_query($query));
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="3" class="title"><?php printf(_("%s's Domains"), $row['O']); ?> (<a href="account.php?id=28&amp;orgid=<?php echo intval($row['id'])?>"><?php echo _("Add")?></a>)</td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Domain")?></td>
    <td class="DataTD"><?php echo _("Edit")?></td>
    <td class="DataTD"><?php echo _("Delete")?></td>
  </tr>
<?php 	$query = "select * from `orgdomains` where `orgid`='".intval($_REQUEST['orgid'])."'";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{ ?>
  <tr>
    <td class="DataTD"><?php echo sanitizeHTML($row['domain'])?></a></td>
    <td class="DataTD"><a href="account.php?id=29&amp;orgid=<?php echo intval($row['orgid'])?>&amp;domid=<?php echo intval($row['id'])?>"><?php echo _("Edit")?></a></td>
    <td class="DataTD"><a href="account.php?id=30&amp;orgid=<?php echo intval($row['orgid'])?>&amp;domid=<?php echo intval($row['id'])?>"><?php echo _("Delete")?></a></td>
  </tr>
<?php } ?>
</table>
