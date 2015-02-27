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

<? if(!array_key_exists('location',$_REQUEST) || $_REQUEST['location'] == "") { ?>
<script language="javascript" src="/ac.js"></script>
<script language="javascript">
<!--
function oncomplete() {
	document.f.submit();
}
// -->
</script>
<p><?=_("Please enter your town or suburb name, followed by region or state or province and then the country (please separate by commas)")?><br />
<?=_("eg Sydney, New South Wales, Australia")?></p>
<p><?=_("This is an AJAX form which depends heavily on javascript for auto-complete functionality and while it will work without javascript the usability will be heavily degraded.")?></p>
<form name="f" action="wot.php" method="post">
<input type='hidden' name='oldid' value='12' />
<table>
  <tr>
    <td align=right valign=middle><?=_("Maximum Distance:")?></td>
    <td><select name="maxdist">
<?
	$arr = array(10, 25, 50, 100, 250, 500, 1000);
	foreach($arr as $val)
	{
		echo "<option value='$val'";
		if(array_key_exists('maxdist',$_REQUEST) && $val == $_REQUEST['maxdist'])
			echo " selected";
		echo ">${val}km</option>\n";
	}
?>
    </td>
  </tr>
  <tr>
    <td align=right valign=middle><?=_("Location:")?></td>
    <td><input autocomplete="off" type="text" id="location" name="location" value="" size="50" /> <input type="submit" name="process" value="Go"></td>
  </tr>
</table>

</form>
<script language="javascript">
<!--
var ac1 = new AC('location', 'location', oncomplete);
ac1.enable_unicode();
document.f.location.focus();
// -->
</script>
<? } else {
	if(intval($_REQUEST['location']) == 0)
	{
		$bits = explode(",", $_REQUEST['location']);

		$loc = trim(mysql_real_escape_string($bits['0']));
		$reg = ""; if(array_key_exists('1',$bits)) $reg=trim(mysql_real_escape_string($bits['1']));
		$ccname = ""; if(array_key_exists('2',$bits)) $ccname=trim(mysql_real_escape_string($bits['2']));

		$query = "select `locations`.`id` as `locid` from `locations`, `regions`, `countries` where
			`locations`.`name` like '$loc%' and `regions`.`name` like '$reg%' and `countries`.`name` like '$ccname%' and
			`locations`.`regid`=`regions`.`id` and `locations`.`ccid`=`countries`.`id`
			order by `locations`.`name` limit 1";
		$res = mysql_query($query);
		if($reg != "" && $ccname == "" && mysql_num_rows($res) <= 0)
		{
			$query = "select `locations`.`id` as `locid` from `locations`, `regions`, `countries` where
				`locations`.`name` like '$loc%' and `countries`.`name` like '$reg%' and
				`locations`.`regid`=`regions`.`id` and `locations`.`ccid`=`countries`.`id`
				order by `locations`.`name` limit 1";
			$res = mysql_query($query);
		}
		if(mysql_num_rows($res) <= 0)
			die(_("Unable to find suitable location"));
		$row = mysql_fetch_assoc($res);
		$_REQUEST['location'] = $row['locid'];
	}

	$maxdist = intval($_REQUEST['maxdist']);

	$locid = intval($_REQUEST['location']);
	$query = "select * from `locations` where `id`='$locid'";
	$loc = mysql_fetch_assoc(mysql_query($query));
	if($maxdist <= 10)
	{
		$query = "SELECT ROUND(6378.137 * ACOS(0.9999999*((SIN(PI() * $loc[lat] / 180) * SIN(PI() * `locations`.`lat` / 180)) + (COS(PI() * $loc[lat] / 180 ) *
				COS(PI() * `locations`.`lat` / 180) * COS(PI() * `locations`.`long` / 180 - PI() * $loc[long] / 180)))), -1) AS `distance`,
				`locations`.`name` AS `location`, concat(`users`.`fname`, ' ', LEFT(`users`.`lname`, 1)) AS `name`, `long`, `lat`,
				`users`.`id` as `uid`, `contactinfo` FROM `locations`, `users` WHERE `users`.`locid` = `locations`.`id` AND
				`users`.`assurer` = 1 AND `users`.`listme` = 1 HAVING `distance` <= '$maxdist' ORDER BY `distance`";
	} else {
		$query = "SELECT ROUND(6378.137 * ACOS(0.9999999*((SIN(PI() * $loc[lat] / 180) * SIN(PI() * `locations`.`lat` / 180)) + (COS(PI() * $loc[lat] / 180 ) *
				COS(PI() * `locations`.`lat` / 180) * COS(PI() * `locations`.`long` / 180 - PI() * $loc[long] / 180)))), -1) AS `distance`,
				`locations`.`name` AS `location`, concat(`users`.`fname`, ' ', LEFT(`users`.`lname`, 1)) AS `name`, `long`, `lat`,
				`users`.`id` as `uid`, `contactinfo` FROM `locations`, `users` WHERE `users`.`locid` = `locations`.`id` AND
				`users`.`assurer` = 1 AND `users`.`listme` = 1 HAVING `distance` <= '$maxdist' ORDER BY `distance` LIMIT 50";
				//echo $query;
	}
	$res = mysql_query($query);
?><table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="700">
  <tr>
    <td class="title"><?=_("Name")?></td>
    <td class="title"><?=_("Distance")?></td>
    <td class="title"><?=_("Max Points")?></td>
    <td class="title"><?=_("Contact Details")?></td>
    <td class="title"><?=_("Email Assurer")?></td>
  </tr>
<?	while($row = mysql_fetch_assoc($res))
	{
		$points = maxpoints($row['uid']);
		if($points > 35)
			$points = 35;
?>
  <tr>
    <td class="DataTD" width="100"><nobr><?=$row['name']?></nobr></td>
    <td class="DataTD"><?=$row['distance']?>km</td>
    <td class="DataTD"><?=$points?></td>
    <td class="DataTD"><?=$row['contactinfo']?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?=$row['uid']?>"><?=_("Email Me")?></a></td>
  </tr>
<? } ?>
</table>
<? } ?>
