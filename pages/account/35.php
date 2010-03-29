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
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="3" class="title"><?=_("Organisations")?></td>
  </tr>
  <tr>
    <td class="DataTD">#</td>
    <td class="DataTD"><?=_("Organisation")?></td>
    <td class="DataTD"><?=_("Admins")?></td>
  </tr>
<?
	$query = "select * from `orginfo`,`org` where `orginfo`.`id`=`org`.`orgid` and `org`.`memid`='".intval($_SESSION['profile']['id'])."'";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		//number of admins for the org
		$r2 = mysql_query("select * from `org` where `orgid`='".intval($row['id'])."'");
		$admincount = mysql_num_rows($r2);

		// number of domains for the org
		$r2 = mysql_query("select * from `orgdomains` where `orgid`='".intval($row['id'])."'");
		$domcount = mysql_num_rows($r2);
?>
  <tr>
    <td class="DataTD"><?=intval($row['id'])?></td>
    <td class="DataTD"><?=($row['O'])?>, <?=($row['ST'])?> <?=sanitizeHTML($row['C'])?></td>
    <td class="DataTD"><a href="account.php?id=32&amp;orgid=<?=$row['id']?>"><?=_("Admins")?> (<?=$admincount?>)</a></td>
  </tr>
<?
	// display the domains of each organisation
	$query3 = "select * from `orgdomains` where `orgid`='".intval($row['id'])."'";
	$res3 = mysql_query($query3);
	while($detailorg = mysql_fetch_assoc($res3))
	{
?>
  <tr>
    <td class="DataTD"><?=intval($detailorg['id'])?></td>
    <td class="DataTD"><?=_("Domain available")?></td>
    <td class="DataTD"><?=sanitizeHTML($detailorg['domain'])?></td>
  </tr>		
<? } } ?>
</table>
