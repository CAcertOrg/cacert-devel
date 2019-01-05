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
<?php
	$ccid = array_key_exists('ccid',$_REQUEST)?intval($_REQUEST['ccid']):0;
	$regid = array_key_exists('regid',$_REQUEST)?intval($_REQUEST['regid']):0;
	$locid = array_key_exists('locid',$_REQUEST)?intval($_REQUEST['locid']):0;
	$name = array_key_exists('name',$_REQUEST)?mysql_escape_string($_REQUEST['name']):"";

	if($ccid > 0 && $_REQUEST['action'] == "add") { ?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Add Region")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Region")?>:</td>
    <td class="DataTD"><input type="text" name="name" value="<?php echo sanitizeHTML($name)?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("Add")?>"></td>
  </tr>
</table>
<input type="hidden" name="action" value="add">
<input type="hidden" name="ccid" value="<?php echo $ccid?>">
<input type="hidden" name="oldid" value="54">
</form>
<?php } if($regid > 0 && $_REQUEST['action'] == "edit") {
	$query = "select * from `regions` where `id`='$regid' order by `name`";
	$row = mysql_fetch_assoc(mysql_query($query));
	$name = $row['name'];
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Edit Region")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Region")?>:</td>
    <td class="DataTD"><input type="text" name="name" value="<?php echo sanitizeHTML($name)?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="action" value="edit">
<input type="hidden" name="regid" value="<?php echo $regid?>">
<input type="hidden" name="oldid" value="54">
</form>
<?php } if($regid > 0 && $_REQUEST['action'] == "add") { ?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Add Location")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Location")?>:</td>
    <td class="DataTD"><input type="text" name="name" value="<?php echo sanitizeHTML($name)?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Longitude")?>:</td>
    <td class="DataTD"><input type="text" name="longitude" value="<?php echo array_key_exists('longitude',$_REQUEST)?sanitizeHTML($_REQUEST['longitude']):""?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Latitude")?>:</td>
    <td class="DataTD"><input type="text" name="latitude" value="<?php echo array_key_exists('latitude',$_REQUEST)?sanitizeHTML($_REQUEST['latitude']):""?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("Add")?>"></td>
  </tr>
</table>
<input type="hidden" name="action" value="add">
<input type="hidden" name="regid" value="<?php echo $regid?>">
<input type="hidden" name="oldid" value="54">
</form>
<?php } if($locid > 0 && $_REQUEST['action'] == "edit") {
	$query = "select * from `locations` where `id`='$locid'";
	$row = mysql_fetch_assoc(mysql_query($query));

	if($name == "")
		$name = $row['name'];
	if(!array_key_exists('longitude',$_REQUEST) || $_REQUEST['longitude'] == "")
		$_REQUEST['longitude'] = $row['long'];
	if(!array_key_exists('latitude',$_REQUEST) || $_REQUEST['latitude'] == "")
		$_REQUEST['latitude'] = $row['lat'];
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Edit Location")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Location")?>:</td>
    <td class="DataTD"><input type="text" name="name" value="<?php echo sanitizeHTML($name)?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Longitude")?>:</td>
    <td class="DataTD"><input type="text" name="longitude" value="<?php echo sanitizeHTML($_REQUEST['longitude'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Latitude")?>:</td>
    <td class="DataTD"><input type="text" name="latitude" value="<?php echo sanitizeHTML($_REQUEST['latitude'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="action" value="edit">
<input type="hidden" name="locid" value="<?php echo $locid?>">
<input type="hidden" name="oldid" value="54">
</form>
<?php } if($locid > 0 && $_REQUEST['action'] == "aliases") {
	$query = "select * from `localias` where `locid`='".intval($locid)."'";
	$res = mysql_query($query);
	$rc = mysql_num_rows($res);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Location Aliases")?> - <a href="javascript:Show_Stuff()"><?php echo _("Add")?></a></td>
  </tr>
  <tr ID="display1">
    <td colspan="2" class="DataTD">
	<form method="post" action="account.php" ACCEPTCHARSET="utf-8">
	<?php echo _("Location Alias")?>: <input type="text" name="name"> <input type="submit" value="Add">
	<input type="hidden" name="action" value="alias">
	<input type="hidden" name="locid" value="<?php echo intval($locid)?>">
	<input type="hidden" name="oldid" value="54">
	</form>
    </td>
  </tr>
<?php 	while($row = mysql_fetch_assoc($res))
	{
?>
  <tr>
    <td class="DataTD"><?php echo $row['name']?></td>
    <td class="DataTD"><a href="account.php?id=54&amp;locid=<?php echo $locid?>&amp;name=<?php echo ($row['name'])?>&amp;action=delalias" onclick="return confirm('Are you sure you want to delete this location alias?');"><?php echo _("Delete")?></td>
  </tr>
<?php } ?>
</table>
<script language="JavaScript" type="text/javascript">
<!--
function Show_Stuff()
{
	if (document.getElementById("display1").style.display == "none")
	{
		document.getElementById("display1").style.display = "";
	} else {
		document.getElementById("display1").style.display = "none";
	}
}

document.getElementById("display1").style.display = "none";
-->
</script>
<?php } if($locid > 0 && $_REQUEST['action'] == "move") {
	$query = "select * from `locations` where `id`='$locid'";
	$row = mysql_fetch_assoc(mysql_query($query));
	$newreg = $_REQUEST['newreg'] = $row['regid'];
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Move Location")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Location")?>:</td>
    <td class="DataTD"><?php echo $row['name']?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Set Region")?>:</td>
    <td class="DataTD"><select name="newreg">
<?php 	$query = "select * from `regions` where `ccid`='".intval($row['ccid'])."' order by `name`";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		echo "<option value='".intval($row['id'])."'";
		if($_REQUEST['newreg'] == $row['id'])
			echo " selected='selected'";
		echo ">$row[name]</option>\n";
	}
?>
    </select></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="action" value="move">
<input type="hidden" name="locid" value="<?php echo $locid?>">
<input type="hidden" name="oldid" value="54">
</form>
<?php } ?>

