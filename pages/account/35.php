<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2020  CAcert Inc.

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

<?
$query = "select *
			from `orginfo`,`org`
			where `orginfo`.`id`=`org`.`orgid`
			and `org`.`memid`='".intval($_SESSION['profile']['id'])."'";

$res = $db_conn->query($query);
while($row = $res->fetch_assoc())
{
	?>
	<tr>
		<td colspan="3" class="title"><?=_("Organisation")?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Organisation Name")?>:</td>
		<td colspan="2" class="DataTD" ><b><?=$row['O']?></b></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Contact Email")?>:</td>
		<td colspan="2" class="DataTD"><?=($row['contact'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Town/Suburb")?>:</td>
		<td colspan="2" class="DataTD"><?=($row['L'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("State/Province")?>:</td>
		<td colspan="2" class="DataTD"><?=($row['ST'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?=_("Country")?>:</td>
		<td colspan="2" class="DataTD"><?=($row['C'])?></td>
	</tr>
	<?
	
	//domain info
	$query = "select `domain` from `orgdomains` where `orgid`='".intval($row['id'])."'";
	$res1 = $db_conn->query($query);
	while($domain = $res1->fetch_assoc())
	{
		?>
		<tr>
			<td class="DataTD"><?=_("Domain")?></td>
			<td colspan="2" class="DataTD"><?=sanitizeHTML($domain['domain'])?></td>
		</tr>
		<?
	}
	
	?>
	<tr>
		<td class="DataTD"><?=_("Administrator")?></td>
		<td class="DataTD"><?=_("Master Account")?></td>
		<td class="DataTD"><?=_("Department")?></td>
	</tr>
	<?
	
	//org admins
	$query = "select * from `org` where `orgid`='".intval($row['id'])."'";
	$res2 = $db_conn->query($query);
	while($org = $res2->fetch_assoc())
	{
		$user = $db_conn->query("select * from `users` where `id`='".intval($org['memid'])."'")->fetch_assoc();
		?> 
		<tr>
			<td class="DataTD"><a href='mailto:<?=$user['email']?>'><?=($user['fname'])?> <?=($user['lname'])?></a></td>
			<td class="DataTD"><?=($org['masteracc'])?></td>
			<td class="DataTD"><?=($org['OU'])?></td>
		</tr>
		<?
		
		if(intval($org['masteracc']) === 1 &&
				 intval($org['memid']) === intval($_SESSION['profile']['id']))
		{ 
			$master="account.php?id=32&amp;orgid=".intval($row['id']);
			?>
			<tr>
				<td colspan="3" class="DataTD"><a href="<?=$master ?>"><?=_("Edit")?></a></td>
			</tr>
			<?
		}
	} 
} ?>
</table>
