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
*/

if(array_key_exists('location',$_REQUEST) && $_REQUEST['location'] != "") { 
	if(intval($_REQUEST['location']) == 0)
	{
		$bits = explode(",", $_REQUEST['location']);

		$loc = trim(mysql_real_escape_string($bits['0']));
		$reg = ''; if(array_key_exists('1',$bits)) $reg=trim(mysql_real_escape_string($bits['1']));
		$ccname = ''; if(array_key_exists('2',$bits)) $ccname=trim(mysql_real_escape_string($bits['2']));
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
                        die("Unable to find suitable location");

		$row = mysql_fetch_assoc($res);
		$_REQUEST['location'] = $row['locid'];
	}

	$locid = intval($_REQUEST['location']);
	$query = "select * from `locations` where `id`='$locid'";
	$res = mysql_query($query);
	if(mysql_num_rows($res) > 0)
	{
		$loc = mysql_fetch_assoc($res);
	        $_SESSION['profile']['ccid'] = $loc['ccid'];
        	$_SESSION['profile']['regid'] = $loc['regid'];
	        $_SESSION['profile']['locid'] = $loc['id'];
		$query = "update `users` set `locid`='$loc[id]', `regid`='$loc[regid]', `ccid`='$loc[ccid]' where `id`='".$_SESSION['profile']['id']."'";
		mysql_query($query);
		echo "<p>"._("Your location has been updated")."</p>\n";
	} else {
		echo "<p>"._("I was unable to match your location with places in my database.")."</p>\n";
	}
}

	$query = "select `name` from `locations` where `id`='".$_SESSION['profile']['locid']."'";
	$res = mysql_query($query);
	$loc = mysql_fetch_assoc($res);
	$query = "select `name` from `regions` where `id`='".$_SESSION['profile']['regid']."'";
	$res = mysql_query($query);
	$reg = mysql_fetch_assoc($res);
	$query = "select `name` from `countries` where `id`='".$_SESSION['profile']['ccid']."'";
	$res = mysql_query($query);
	$cc = mysql_fetch_assoc($res);
?>
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
<p><?=sprintf(_("Your current location is set as: %s"), "$loc[name], $reg[name], $cc[name]")?></p>
<form name="f" action="wot.php" method="post">
<input type='hidden' name='id' value='13' />
<table>
  <tr>
    <td align=right valign=middle><?=_("Location:")?></td>
    <td><input autocomplete="off" type="text" id="location" name="location" value="" size="50" /> <?=_("(hit enter to submit)")?></td>
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
