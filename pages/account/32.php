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
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="500">
  <tr>
    <td colspan="5" class="title"><? printf(_("%s's Administrators"), $row['O']); ?> (<a href="account.php?id=33&amp;orgid=<?=$row['id']?>"><?=_("Add")?></a>)</td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Administrator")?></td>
    <td class="DataTD"><?=_("Master Account")?></td>
    <td class="DataTD"><?=_("Department")?></td>
    <td class="DataTD"><?=_("Comments")?></td>
    <td class="DataTD"><?=_("Delete")?></td>
  </tr>
<?
	$query = "select * from `org` where `orgid`='".intval($_REQUEST['orgid'])."'";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($row['memid'])."'"));
?>
  <tr>
    <td class="DataTD"><a href='mailto:<?=sanitizeHTML($user['email'])?>'><?=sanitizeHTML($user['fname'])?> <?=sanitizeHTML($user['lname'])?></a></td>
    <td class="DataTD"><?=($row['masteracc'])?></a></td>
    <td class="DataTD"><?=sanitizeHTML($row['OU'])?></a></td>
    <td class="DataTD"><?=sanitizeHTML($row['comments'])?></a></td>
<? if($row['masteracc'] == 0 || $_SESSION['profile']['orgadmin'] == 1) { ?>
    <td class="DataTD"><a href="account.php?id=34&amp;orgid=<?=$row['orgid']?>&amp;memid=<?=$row['memid']?>"><?=_("Delete")?></a></td>
<? } else { ?>
    <td class="DataTD">N/A</td>
<? } ?>
  </tr>
<? } ?>
</table>
