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
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="700">
  <tr>
    <td colspan="5" class="title"><?php echo _("Organisations")?></td>
  </tr>

<tr>
  <td colspan="5" class="title"><?php echo _("Order by:")?>
    <a href="account.php?id=25"><?php echo _("Id")?></a> -
    <a href="account.php?id=25&amp;ord=1"><?php echo _("Country")?></a> -
    <a href="account.php?id=25&amp;ord=2"><?php echo _("Name")?></a>
  </td>
</tr>

  <tr>
    <td class="DataTD" width="350"><?php echo _("Organisation")?></td>
    <td class="DataTD"><?php echo _("Domains")?></td>
    <td class="DataTD"><?php echo _("Admins")?></td>
    <td class="DataTD"><?php echo _("Edit")?></td>
    <td class="DataTD"><?php echo _("Delete")?></td>
  </tr>
<?php 	$order = 0;
	if (array_key_exists('ord',$_REQUEST)) {
		$order = intval($_REQUEST['ord']);
	}
	
	$order_by = "`id`";
	switch ($order) {
		case 1:
			$order_by = "`C`,`O`";
			break;
		case 2:
			$order_by = "`O`";
			break;
		// the 0 and default case are handled by the preset
	}
	
	// Safe because $order_by only contains fixed strings
	$query = sprintf("select * from `orginfo` ORDER BY %s", $order_by);
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$r2 = mysql_query("select * from `org` where `orgid`='".intval($row['id'])."'");
		$admincount = mysql_num_rows($r2);
		$r2 = mysql_query("select * from `orgdomains` where `orgid`='".intval($row['id'])."'");
		$domcount = mysql_num_rows($r2);
?>
  <tr>
    <td class="DataTD"><?php echo htmlspecialchars($row['O'])?>, <?php echo htmlspecialchars($row['ST'])?> <?php echo htmlspecialchars($row['C'])?></td>
    <td class="DataTD"><a href="account.php?id=26&amp;orgid=<?php echo intval($row['id'])?>"><?php echo _("Domains")?> (<?php echo $domcount?>)</a></td>
    <td class="DataTD"><a href="account.php?id=32&amp;orgid=<?php echo $row['id']?>"><?php echo _("Admins")?> (<?php echo $admincount?>)</a></td>
    <td class="DataTD"><a href="account.php?id=27&amp;orgid=<?php echo $row['id']?>"><?php echo _("Edit")?></a></td>
    <td class="DataTD"><a href="account.php?id=31&amp;orgid=<?php echo $row['id']?>"><?php echo _("Delete")?></a></td>
    <?php if(array_key_exists('viewcomment',$_REQUEST) && $_REQUEST['viewcomment']!='') { ?>
    <td class="DataTD"><?php echo sanitizeHTML($row['comments'])?></td>
    <?php } ?>
  </tr>
<?php } ?>
</table>
