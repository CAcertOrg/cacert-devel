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
<?php 	$query = "select * from `orgdomains` where `id`='".intval($_REQUEST['domid'])."'";
	$row = mysqli_fetch_assoc(mysql_query($query));
	$query = "select * from `orginfo` where `id`='".intval($_REQUEST['orgid'])."'";
	$org = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], $query));

	$_SESSION['_config']['domain'] = $row['domain'];
?>
<h3><?php echo _("Warning!")?></h3>
<p><?php echo _("Hitting delete will also revoke all existing certificates issued under this domain")?></p>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php printf(_("Delete Domain for %s"), ($org['O'])); ?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><?php printf(_("Are you really sure you want to remove %s and all certificates issued under this domain?"), sanitizeHTML($row['domain'])); ?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="cancel" value="<?php echo _("Cancel")?>">
    		<input type="submit" name="process" value="<?php echo _("Delete")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?php echo intval($id)?>">
<input type="hidden" name="orgid" value="<?php echo intval($_REQUEST['orgid'])?>">
<input type="hidden" name="domain" value="<?php echo sanitizeHTML($row['domain'])?>">
<input type="hidden" name="domid" value="<?php echo intval($_REQUEST['domid'])?>">

</form>
