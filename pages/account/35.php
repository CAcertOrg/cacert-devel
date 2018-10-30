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

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">

<?php $query = "select *
			from `orginfo`,`org`
			where `orginfo`.`id`=`org`.`orgid`
			and `org`.`memid`='".intval($_SESSION['profile']['id'])."'";

$res = mysql_query($query);
while($row = mysql_fetch_assoc($res))
{
	?>
	<tr>
		<td colspan="3" class="title"><?php echo _("Organisation")?></td>
	</tr>
	<tr>
		<td class="DataTD"><?php echo _("Organisation Name")?>:</td>
		<td colspan="2" class="DataTD" ><b><?php echo $row['O']?></b></td>
	</tr>
	<tr>
		<td class="DataTD"><?php echo _("Contact Email")?>:</td>
		<td colspan="2" class="DataTD"><?php echo ($row['contact'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?php echo _("Town/Suburb")?>:</td>
		<td colspan="2" class="DataTD"><?php echo ($row['L'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?php echo _("State/Province")?>:</td>
		<td colspan="2" class="DataTD"><?php echo ($row['ST'])?></td>
	</tr>
	<tr>
		<td class="DataTD"><?php echo _("Country")?>:</td>
		<td colspan="2" class="DataTD"><?php echo ($row['C'])?></td>
	</tr>
	<?php
	//domain info
	$query = "select `domain` from `orgdomains` where `orgid`='".intval($row['id'])."'";
	$res1 = mysql_query($query);
	while($domain = mysql_fetch_assoc($res1))
	{
		?>
		<tr>
			<td class="DataTD"><?php echo _("Domain")?></td>
			<td colspan="2" class="DataTD"><?php echo sanitizeHTML($domain['domain'])?></td>
		</tr>
		<?php 	}
	
	?>
	<tr>
		<td class="DataTD"><?php echo _("Administrator")?></td>
		<td class="DataTD"><?php echo _("Master Account")?></td>
		<td class="DataTD"><?php echo _("Department")?></td>
	</tr>
	<?php
	//org admins
	$query = "select * from `org` where `orgid`='".intval($row['id'])."'";
	$res2 = mysql_query($query);
	while($org = mysql_fetch_assoc($res2))
	{
		$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($org['memid'])."'"));
		?> 
		<tr>
			<td class="DataTD"><a href='mailto:<?php echo $user['email']?>'><?php echo ($user['fname'])?> <?php echo ($user['lname'])?></a></td>
			<td class="DataTD"><?php echo ($org['masteracc'])?></td>
			<td class="DataTD"><?php echo ($org['OU'])?></td>
		</tr>
		<?php
		if(intval($org['masteracc']) === 1 &&
				 intval($org['memid']) === intval($_SESSION['profile']['id']))
		{ 
			$master="account.php?id=32&amp;orgid=".intval($row['id']);
			?>
			<tr>
				<td colspan="3" class="DataTD"><a href="<?php echo $master ?>"><?php echo _("Edit")?></a></td>
			</tr>
			<?php 		}
	} 
} ?>
</table>
