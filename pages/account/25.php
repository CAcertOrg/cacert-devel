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
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="700">
  <tr>
    <td colspan="5" class="title"><?=_("Organisations")?></td>
  </tr>

<tr>
  <td colspan="5" class="title"><?=_("Order by:")?>
    <a href="account.php?id=25"><?=_("Id")?></a> -
    <a href="account.php?id=25&amp;ord=1"><?=_("Country")?></a> -
    <a href="account.php?id=25&amp;ord=2"><?=_("Name")?></a>
  </td>
</tr>

  <tr>
    <td class="DataTD" width="350"><?=_("Organisation")?></td>
    <td class="DataTD"><?=_("Domains")?></td>
    <td class="DataTD"><?=_("Admins")?></td>
    <td class="DataTD"><?=_("Edit")?></td>
    <td class="DataTD"><?=_("Delete")?></td>
  </tr>
<?
	$order = 0;
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
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$r2 = mysqli_query($_SESSION['mconn'], "select * from `org` where `orgid`='".intval($row['id'])."'");
		$admincount = mysqli_num_rows($r2);
		$r2 = mysqli_query($_SESSION['mconn'], "select * from `orgdomains` where `orgid`='".intval($row['id'])."'");
		$domcount = mysqli_num_rows($r2);
?>
  <tr>
    <td class="DataTD"><?=htmlspecialchars($row['O'])?>, <?=htmlspecialchars($row['ST'])?> <?=htmlspecialchars($row['C'])?></td>
    <td class="DataTD"><a href="account.php?id=26&amp;orgid=<?=intval($row['id'])?>"><?=_("Domains")?> (<?=$domcount?>)</a></td>
    <td class="DataTD"><a href="account.php?id=32&amp;orgid=<?=$row['id']?>"><?=_("Admins")?> (<?=$admincount?>)</a></td>
    <td class="DataTD"><a href="account.php?id=27&amp;orgid=<?=$row['id']?>"><?=_("Edit")?></a></td>
    <td class="DataTD"><a href="account.php?id=31&amp;orgid=<?=$row['id']?>"><?=_("Delete")?></a></td>
    <? if(array_key_exists('viewcomment',$_REQUEST) && $_REQUEST['viewcomment']!='') { ?>
    <td class="DataTD"><?=sanitizeHTML($row['comments'])?></td>
    <? } ?>
  </tr>
<? } ?>
</table>
