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
  <td colspan="5" class="title">Order by: <a href="account.php?id=25">Id</a> - <a href="account.php?id=25&amp;ord=1">Country</a> - <a href="account.php?id=25&amp;ord=2">Name</a></td>
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
          if ( $_REQUEST['ord'] != '') {
            $order = intval($_REQUEST['ord']);
          }
          if($order>0 && $order<3) {
            if($order==1) {
              $query = "select * from `orginfo` ORDER BY `C`,`O`";
            } else {
              $query = "select * from `orginfo` ORDER BY `O`";
            }
          } else {
            $order=0;
            $query = "select * from `orginfo` ORDER BY `id`";
          }
        } else {
          $order=0;
          $query = "select * from `orginfo` ORDER BY `id`";
        }

// echo "<tr><td colspan='5'>(".$order.") ".$query."</td></tr>\r\n";

	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$r2 = mysql_query("select * from `org` where `orgid`='".intval($row['id'])."'");
		$admincount = mysql_num_rows($r2);
		$r2 = mysql_query("select * from `orgdomains` where `orgid`='".intval($row['id'])."'");
		$domcount = mysql_num_rows($r2);
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
