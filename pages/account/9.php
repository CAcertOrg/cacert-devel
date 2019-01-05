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
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="3" class="title"><?php echo _("Domains")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Delete")?></td>
    <td class="DataTD"><?php echo _("Status")?></td>
    <td class="DataTD"><?php echo _("Address")?></td>

<?php 	$query = "select * from `domains` where `memid`='".intval($_SESSION['profile']['id'])."' and `deleted`=0";
	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
	{
?>
  <tr>
    <td colspan="3" class="DataTD"><?php echo _("No domains are currently listed.")?></td>
  </tr>
<?php } else {
	while($row = mysql_fetch_assoc($res))
	{
		if($row['hash'] == "")
			$verified = _("Verified");
		else
			$verified = _("Unverified");
?>
  <tr>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?php echo intval($row['id'])?>"></td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['domain'])?></td>
  </tr>
<?php } ?>
  <tr>
    <td class="DataTD" colspan="3"><input type="submit" name="process" value="<?php echo _("Delete")?>"></td>
  </tr>
<?php } ?>
</table>
<input type="hidden" name="oldid" value="<?php echo $id?>">
</form>
