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
	$row = mysqli_fetch_assoc(mysql_query($_SESSION['mconn'], $query));
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="500">
  <tr>
    <td colspan="5" class="title"><?php printf(_("%s's Administrators"), $row['O']); ?> (<a href="account.php?id=33&amp;orgid=<?php echo $row['id']?>"><?php echo _("Add")?></a>)</td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Administrator")?></td>
    <td class="DataTD"><?php echo _("Master Account")?></td>
    <td class="DataTD"><?php echo _("Department")?></td>
    <td class="DataTD"><?php echo _("Comments")?></td>
    <td class="DataTD"><?php echo _("Delete")?></td>
  </tr>
<?php
	$query = "select * from `org` where `orgid`='".intval($_REQUEST['orgid'])."'";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$user = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], "select * from `users` where `id`='".intval($row['memid'])."'"));
?>
  <tr>
    <td class="DataTD"><a href='mailto:<?php echo sanitizeHTML($user['email'])?>'><?php echo sanitizeHTML($user['fname'])?> <?php echo sanitizeHTML($user['lname'])?></a></td>
    <td class="DataTD"><?php echo ($row['masteracc'])?></a></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['OU'])?></a></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['comments'])?></a></td>
<?php if($row['masteracc'] == 0 || $_SESSION['profile']['orgadmin'] == 1) { ?>
    <td class="DataTD"><a href="account.php?id=34&amp;orgid=<?php echo $row['orgid']?>&amp;memid=<?php echo $row['memid']?>"><?php echo _("Delete")?></a></td>
<?php } else { ?>
    <td class="DataTD">N/A</td>
<?php } ?>
  </tr>
<?php } ?>
</table>
