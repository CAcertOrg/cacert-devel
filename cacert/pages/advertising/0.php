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
	$tdcols = 6;
	if($_SESSION['profile']['adadmin'] == 2)
		$tdcols++;

	if(array_key_exists('approve',$_REQUEST) && intval($_REQUEST['approve']) > 0 && $_SESSION['profile']['adadmin'] >= 2)
	{
		$approve = intval($_REQUEST['approve']);
		$query = "select * from `advertising` where `id`='$approve' and `expires`='0000-00-00 00:00:00'";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_assoc($res);
			$end = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")+$row['months'], date("d"), date("Y")));
			$query = "update `advertising` set `expires`='$end', `active`=1, `approvedby`='".$_SESSION['profile']['id']."' where `id`='$approve'";
			mysql_query($query);
			echo "<p>The ad was approved and is now active.</p>\n";
		}
	}
	if(array_key_exists('deactive',$_REQUEST) && intval($_REQUEST['deactive']) > 0 && $_SESSION['profile']['adadmin'] >= 2)
	{
		$deactive = intval($_REQUEST['deactive']);
		$query = "select * from `advertising` where `id`='$deactive'";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_assoc($res);
			$end = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m")+$row['months'], date("d"), date("Y")));
			$query = "update `advertising` set `active`=0 where `id`='$deactive'";
			mysql_query($query);
			echo "<p>The ad was deactivated and is now inactive.</p>\n";
		}
	}

?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="500">
  <tr>
    <td colspan="<?=$tdcols?>" class="title"><?=_("View Advertising")?> <a href="advertising.php?id=<?=$id?>&showall=1"><?=_("Show All")?></a></td>
  </tr>
  <tr>
	<td class="DataTD">ID</td>
	<td class="DataTD">Link</td>
	<td class="DataTD">Status</td>
	<td class="DataTD">Expires</td>
	<td class="DataTD">Edit</td>
	<td class="DataTD">Disable</td>
<? if($_SESSION['profile']['adadmin'] == 2) { echo "\t<td class='DataTD'>Approve</td>\n"; }
?>  </tr>
<?
	$query = "select *,UNIX_TIMESTAMP(`expires`)-UNIX_TIMESTAMP(NOW()) as `timeleft` from `advertising` where `replaced`=0 ";
	if(!array_key_exists('showall',$_REQUEST) || $_REQUEST['showall'] != 1)
		$query .= "and `active`=1 having `timeleft` > 0 ";
	$query .= "order by `id` desc";

	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		if($row['expires'] == "0000-00-00 00:00:00")
			$status = "Pending";
		else if($row['active'] == 1 && $row['timeleft'] > 0)
			$status = "Active";
		else if($row['timeleft'] <= 0)
			$status = "Expired";
		else if($row['active'] != 1)
			$status = "Disabled";
		else
			$status = "Unknown";
		echo "<tr><td class='DataTD'>".$row['id']."</td><td class='DataTD'><a href='".$row['link']."' target='_new'>".$row['title']."</a></td>";
		echo "<td class='DataTD'>$status</td><td class='DataTD'>".$row['expires']."</td><td class='DataTD'>Edit</td>";
		echo "<td class='DataTD'>Disable</td>";
		if($_SESSION['profile']['adadmin'] == 2)
		{
			if($status == "Pending" && $row['expires'] == "0000-00-00 00:00:00")
				echo "<td class='DataTD'><a href='advertising.php?id=0&amp;approve=".$row['id']."'>Approve</a></td>";
			else if($status == "Active")
				echo "<td class='DataTD'><a href='advertising.php?id=0&amp;deactive=".$row['id']."'>De-Activate</a></td>";
			else
				echo "<td class='DataTD'>N/A</td>";
		}
		echo "</tr>\n";
	}
?>
</table>
