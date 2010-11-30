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
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?=_("Assurer Ranking")?></td>
  </tr>
  <tr>
<?
	$query = "SELECT COUNT(1) as `assurances` FROM `notary` WHERE `from`=".intval($_SESSION['profile']['id'])." AND `from` != `to`";
	
	$res = mysql_query($query);
	$row = mysql_fetch_assoc($res);
	$rc = intval($row['assurances']);

	$query = "SELECT COUNT(1) FROM `notary` GROUP BY `from` HAVING COUNT(1) > {$rc}";

	$rank = mysql_num_rows(mysql_query($query)) + 1;
?>
    <td class="DataTD"><?=sprintf(_("You have made %s assurances which ranks you as the #%s top assurer."), intval($rc), intval($rank))?></td>
  </tr>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="6" class="title"><?=_("Your Assurance Points")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("ID")?></b></td>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
  </tr>
<?
	$query = "SELECT n.`id`, n.`date`, n.`points`, n.`from` as `from_id`, u.`fname` AS `from_fname`, u.`lname` AS `from_lname`, n.`location`, n.`method` FROM `notary` n LEFT JOIN `users` u ON n.`from`=u.`id` WHERE n.`to`=".intval($_SESSION['profile']['id'])." ORDER BY n.`when` ASC, n.`id` ASC";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
?>
  <tr>
    <td class="DataTD"><?=$row['id']?></td>
    <td class="DataTD"><?=$row['date']?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?=intval($row['from_id'])?>"><?=$row['from_fname']." ".$row['from_lname']?></td>
    <td class="DataTD"><?=$row['points']?></td>
    <td class="DataTD"><?=$row['location']?></td>
    <td class="DataTD"><?=_(sprintf("%s", $row['method']))?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="3"><b><?=_("Total Points")?>:</b></td>
    <td class="DataTD"><?=intval($_SESSION['profile']['points'])?></td>
    <td class="DataTD" colspan="2">&nbsp;</td>
  </tr>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="6" class="title"><?=_("Assurance Points You Issued")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("ID")?></b></td>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
  </tr>
<?
	$points = 0;
	$query = "SELECT n.`id`, n.`date`, n.`points`, n.`location`, n.`method`, n.`to` AS `to_id`, u.`fname` AS `to_fname`, u.`lname` AS `to_lname` FROM `notary` n LEFT JOIN `users` u ON n.`to`=u.`id` WHERE n.`from`=".intval($_SESSION['profile']['id'])." AND n.`to`!=".intval($_SESSION['profile']['id'])." ORDER BY n.`when` ASC, n.`id` ASC";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$points += $row['points'];
		$name = trim($row['to_fname']." ".$row['to_lname']);
		if($name == "")
			$name = '<i>'._("Deleted before Verification").'</i>';
		else
			$name = "<a href='wot.php?id=9&amp;userid=".intval($row['to_id'])."'>$name</a>";
?>
  <tr>
    <td class="DataTD"><?=intval($row['id'])?></td>
    <td class="DataTD"><?=$row['date']?></td>
    <td class="DataTD"><?=$name?></td>
    <td class="DataTD"><?=intval($row['points'])?></td>
    <td class="DataTD"><?=$row['location']?></td>
    <td class="DataTD"><?=$row['method']==""?"":_(sprintf("%s", $row['method']))?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="3"><b><?=_("Total Points Issued")?>:</b></td>
    <td class="DataTD"><?=$points?></td>
    <td class="DataTD" colspan="2">&nbsp;</td>
  </tr>
</table>
<p>[ <a href='javascript:history.go(-1)'><?=_("Go Back")?></a> ]</p>

