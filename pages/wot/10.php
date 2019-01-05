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
<?php 	$thawte = false;

?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="5" class="title"><?php echo _("Assurer Ranking")?></td>
  </tr>
  <tr>
<?php
// the rank calculation is not adjusted to the new deletion method
	$query = "SELECT `users`. *, count(*) AS `list` FROM `users`, `notary`
			WHERE `users`.`id` = `notary`.`from` AND `notary`.`from` != `notary`.`to`
			AND `from`='".intval($_SESSION['profile']['id'])."' GROUP BY `notary`.`from`";
	$res = mysql_query($query);
	$row = mysql_fetch_assoc($res);
	$rc = intval($row['list']);
/*
	$query = "SELECT `users`. *, count(*) AS `list` FROM `users`, `notary`
			WHERE `users`.`id` = `notary`.`from` AND `notary`.`from` != `notary`.`to`
			GROUP BY `notary`.`from` HAVING count(*) > '$rc' ORDER BY `notary`.`when` DESC";
*/
	$query = "SELECT count(*) AS `list` FROM `users`
			inner join `notary` on `users`.`id` = `notary`.`from`
			GROUP BY `notary`.`from` HAVING count(*) > '$rc'";

	$rank = mysql_num_rows(mysql_query($query)) + 1;
?>
    <td class="DataTD"><?php echo sprintf(_("You have made %s assurances which ranks you as the #%s top assurer."), intval($rc), intval($rank))?></td>
  </tr>
</table>
<center>
<br>
<?php echo sprintf(_("The calculation of points will be changed in the near future. Please check the %s new calculation %s"), "<a href='/wot.php?id=15'>", "</a>")?>
<br>
</center>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="6" class="title"><?php echo _("Your Assurance Points")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?php echo _("ID")?></b></td>
    <td class="DataTD"><b><?php echo _("Date")?></b></td>
    <td class="DataTD"><b><?php echo _("Who")?></b></td>
    <td class="DataTD"><b><?php echo _("Points")?></b></td>
    <td class="DataTD"><b><?php echo _("Location")?></b></td>
    <td class="DataTD"><b><?php echo _("Method")?></b></td>
  </tr>
<?php 	$query = "select `id`, `date`, `from`, `points`, `location`, `method` from `notary` where `to`='".intval($_SESSION['profile']['id'])."' and `deleted`=0";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$fromuser = mysql_fetch_assoc(mysql_query("select `fname`, `lname` from `users` where `id`='".intval($row['from'])."'"));
?>
  <tr>
    <td class="DataTD"><?php echo intval($row['id'])?></td>
    <td class="DataTD"><?php echo $row['date']?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?php echo intval($row['from'])?>"><?php echo sanitizeHTML(trim($fromuser['fname']." ".$fromuser['lname']))?></td>
    <td class="DataTD"><?php echo intval($row['points'])?></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['location'])?></td>
    <td class="DataTD"><?php echo _(sprintf("%s", $row['method']))?></td>
  </tr>
<?php   $thawte = ($row['method'] == "Thawte Points Transfer") || $thawte;
} ?>
  <tr>
    <td class="DataTD" colspan="3"><b><?php echo _("Total Points")?>:</b></td>
    <td class="DataTD"><?php echo intval($_SESSION['profile']['points'])?></td>
    <td class="DataTD" colspan="2">&nbsp;</td>
  </tr>
</table>
<?php if ($thawte)
{
?>
<br>
<center>
<strong style='color: red'>
<?php echo _("Your Thawte-Points will be revoked in the near future. Please check new calculation!");?>
<br>
</strong>
</center>
<?}?>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="6" class="title"><?php echo _("Assurance Points You Issued")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?php echo _("ID")?></b></td>
    <td class="DataTD"><b><?php echo _("Date")?></b></td>
    <td class="DataTD"><b><?php echo _("Who")?></b></td>
    <td class="DataTD"><b><?php echo _("Points")?></b></td>
    <td class="DataTD"><b><?php echo _("Location")?></b></td>
    <td class="DataTD"><b><?php echo _("Method")?></b></td>
  </tr>
<?php 	$points = 0;
	$query = "select `id`, `date`, `points`, `to`, `location`, `method` from `notary` where `from`='".intval($_SESSION['profile']['id'])."' and `to`!='".intval($_SESSION['profile']['id'])."'  and `deleted`=0" ;
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$fromuser = mysql_fetch_assoc(mysql_query("select `fname`, `lname` from `users` where `id`='".intval($row['to'])."'"));
		$points += intval($row['points']);
		$name = trim($fromuser['fname']." ".$fromuser['lname']);
		if($name == "")
			$name = _("Deleted before Verification");
		else
			$name = "<a href='wot.php?id=9&amp;userid=".intval($row['to'])."'>".sanitizeHTML($name)."</a>";
?>
  <tr>
    <td class="DataTD"><?php echo intval($row['id'])?></td>
    <td class="DataTD"><?php echo $row['date']?></td>
    <td class="DataTD"><?php echo $name?></td>
    <td class="DataTD"><?php echo intval($row['points'])?></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['location'])?></td>
    <td class="DataTD"><?php echo $row['method']==""?"":_(sprintf("%s", $row['method']))?></td>
  </tr>
<?php } ?>
  <tr>
    <td class="DataTD" colspan="3"><b><?php echo _("Total Points Issued")?>:</b></td>
    <td class="DataTD"><?php echo intval($points)?></td>
    <td class="DataTD" colspan="2">&nbsp;</td>
  </tr>
</table>
<p>[ <a href='javascript:history.go(-1)'><?php echo _("Go Back")?></a> ]</p>

