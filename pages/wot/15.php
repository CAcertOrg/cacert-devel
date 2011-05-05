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
    <td class="DataTD"><?=sprintf(_("You have made %s assurances which ranks you as the #%s top assurer."), intval($rc), intval($rank))?></td>
  </tr>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="7" class="title"><?=_("Assurance Points You Issued")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("ID")?></b></td>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
    <td class="DataTD"><b><?=_("Experience Points")?></b></td>
  </tr>
<?

	$points = 0;
	$sumexperienceA = 0;
	$query = "select * from `notary` where `from`='".intval($_SESSION['profile']['id'])."' and `to`!='".intval($_SESSION['profile']['id'])."' order by `id` desc";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$fromuser = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($row['to'])."'"));
		$points += $row['awarded'];
		$name = trim($fromuser['fname']." ".$fromuser['lname']);
		if($name == "")
			$name = _("Deleted before Verification");
		else
			$name = "<a href='wot.php?id=9&amp;userid=".intval($row['to'])."'>$name</a>";
		$experience="";
		if ($row['method'] == "Face to Face Meeting")
		{
			if ($sumexperienceA < 50)
				$sumexperienceA=$sumexperienceA+2;
			$experience="2";
		}
		
?>
  <tr>
    <td class="DataTD"><?=intval($row['id'])?></td>
    <td class="DataTD"><?=$row['date']?></td>
    <td class="DataTD"><?=$name?></td>
    <td class="DataTD"><?=intval($row['awarded'])?></td>
    <td class="DataTD"><?=$row['location']?></td>
    <td class="DataTD"><?=$row['method']==""?"":_(sprintf("%s", $row['method']))?></td>
    <td class="DataTD"><?=$experience?>&nbsp;</td>
</tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="3"><b><?=_("Total Points Issued")?>:</b></td>
    <td class="DataTD"><?=$points?></td>
    <td class="DataTD">&nbsp;</td>
    <td class="DataTD"><strong><?=_("Total Experience Points")?>:</strong></td>
    <td class="DataTD"><?=$sumexperienceA?></td>
  </tr>
</table>
<br>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="7" class="title"><?=_("Your Assurance Points")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("ID")?></b></td>
    <td class="DataTD"><b><?=_("Date")?></b></td>
    <td class="DataTD"><b><?=_("Who")?></b></td>
    <td class="DataTD"><b><?=_("Points")?></b></td>
    <td class="DataTD"><b><?=_("Location")?></b></td>
    <td class="DataTD"><b><?=_("Method")?></b></td>
    <td class="DataTD"><b><?=_("Experience Points")?></b></td>
  </tr>
<?
        $points = 0;
	$maxpoints = 100;
	$sumexperience = 0;
//        $query = "select sum(points) as apoints from `notary` where `from`='".intval($_SESSION['profile']['id'])."' and `from`=`to` ";
//        $res = mysql_query($query);
//	$row = mysql_fetch_assoc($res);
//	$maxpoints=intval($_SESSION['profile']['points'])-$row['apoints'];

	$points = 0;
	$query = "select * from `notary` where `to`='".intval($_SESSION['profile']['id'])."' and `from` != `to` order by `id` desc ";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$experience = 0;
		$awarded = $row['awarded'];
                if ($awarded < 0)
			$awarded = 0;
		if ($row['points'] > $row['awarded'])
			$awarded = $row['points'];
		if ($awarded > 150)
			$awarded = 150;
		$awardedcount = $awarded;
		if ($awarded > 100)
			{
			$experience = $awarded - $maxpoints;
			$awarded = 100;
			}
		if ($points+$awarded > $maxpoints)
			$awarded = $maxpoints-$points;
		
		switch ($row['method'])
		{
			case 'Thawte Points Transfer':
			//	$points=0;
				$awarded=sprintf("<strong style='color: red'>%s</strong>",_("Revoked"));
				$experience=0;
				break;
			case 'CT Magazine - Germany':
			//	$points=0;
				$awarded=sprintf("<strong style='color: red'>%s</strong>",_("Revoked"));
				$experience=0;
				break;
			case 'Temporary Increase':
// Current usage of 'Temporary Increase' may break audit aspects, needs to be reimplemented
			//	$points=0;
				$awarded=sprintf("<strong style='color: red'>%s</strong>",_("Revoked"));
				$experience=0;
				break;
			case 'Administrative Increase':
				if ($row['points'] > 2)
					$points = $points + $awarded;
				break;
			default:
				$points = $points + $awarded;
		}
		if ($sumexperience+$experience < 150)
			$sumexperience = $sumexperience + $experience;
		else
			$sumexperience = 150;

$fromuser = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($row['from'])."'"));
?>
  <tr>
    <td class="DataTD"><?=$row['id']?></td>
    <td class="DataTD"><?=$row['date']?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?=intval($row['from'])?>"><?=$fromuser['fname']." ".$fromuser['lname']?></td>
    <td class="DataTD"><?=$awarded?></td>
    <td class="DataTD"><?=$row['location']?></td>
    <td class="DataTD"><?=_(sprintf("%s", $row['method']))?></td>
    <td class="DataTD"><?=$experience?></b></td>
  </tr>
<? } 
	if ($sumexperienceA + $sumexperience > 50)
		$sumexperienceOut = 50-$sumexperienceA;
	else
		$sumexperienceOut = $sumexperience;
	if ($points < 100)
		{
		$sumexperienceAHold = $sumexperienceA;
		$sumexperienceA = 0;
		$sumexperienceOutHold = $sumexperienceOut;
		$sumexperienceOut = 0;
		}
?>
  <tr>
    <td class="DataTD" colspan="3"><b><?=_("Total Assurance Points")?>:</b></td>
    <td class="DataTD"><?=$points?></td>
    <td class="DataTD" colspan="3">&nbsp;</td>
  </tr>
  <tr>
      <td class="DataTD" colspan="3"><b><?=_("Total Experience Points by Assurance")?>:</b></td>
      <td class="DataTD"><?=$sumexperienceA?></td>
      <td class="DataTD" colspan="3">
<? if ($points < 100)
   {
	printf(_("%s Points on hold due to less Assurance points"), $sumexperienceAHold);
   }?>
      </td>
  </tr>
  <tr>
        <td class="DataTD" colspan="3"><b><?=_("Total Experience Points (other ways)")?>:</b></td>
        <td class="DataTD"><?=$sumexperienceOut?></td>
        <td class="DataTD" colspan="3">&nbsp;
	<? if ($sumexperience != $sumexperienceOut)
	{
		if (points <100)
    		{
    			printf(_("%s Points on hold due to less Assurance points"), $sumexperienceOutHold);
    		} else {
    			echo _("Limit reached");
    		}
	}?>
	</td>
  </tr>
  <tr>
      <td class="DataTD" colspan="3"><b><?=_("Total Points")?>:</b></td>
      <td class="DataTD"><?=$points + $sumexperienceA + $sumexperienceOut?></td>
      <td class="DataTD" colspan="3">&nbsp;</td>
  </tr>

</table>
<p>[ <a href='javascript:history.go(-1)'><?=_("Go Back")?></a> ]</p>

