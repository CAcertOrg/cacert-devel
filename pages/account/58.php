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
<?
include_once($_SESSION['_config']['filepath'].'/includes/notary.inc.php');

if ($_SESSION['profile']['admin'] != 1 || !array_key_exists('userid',$_REQUEST) || intval($_REQUEST['userid']) < 1) {
	echo _('You do not have access to this page');
} else {
	$user_id = intval($_REQUEST['userid']);
	if(mysql_num_rows($res) <= 0){
		echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are a foot!");
	} else {
		$row = mysql_fetch_assoc($res);
		$query = "select `users`.`fname`, `users`.`mname`, `users`.`lname`, `orginfo`.`o`, `org`.`masteracc`
			FROM `users`, `orginfo`, `org`
			WHERE `users`.`id` = `org`.`memid` AND `orginfo`.`id` = `org`.`orgid`
			AND `users`.`id`='$user_id' ";
		$res = mysql_query($query);?>
		<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper"><?
		if (mysql_num_rows($res) <= 0) {?>
			<tr>
				<td colspan="2" class="title"><?=sprintf(_('%s %s %s is not listed as Organisation Adminstrator'),sanitizeHTML($row['fname']),sanitizeHTML($row['mname']),sanitizeHTML($row['lname']))?></td>
			</tr>
		<?}else{?>
			<tr>
				<td colspan="2" class="title"><?=sprintf(_('%s %s %s is listed as Organisation Adminstrator for:'),sanitizeHTML($row['fname']),sanitizeHTML($row['mname']),sanitizeHTML($row['lname']))?></td>
			</tr>
			<tr>
				<td class="DataTD"><b><?=_('Organisation')?></b></td>
				<td class="DataTD"><b><?=_('Masteraccount')?></b></td>
			</tr><?
			while($drow = mysql_fetch_assoc($res)){?>
				<tr>
					<td class="DataTD"><?=$data['o']?></td>
					<td class="DataTD"><?=$data['masteracc']?></td>
				</tr>
			<?}
		?></table>
<?	}
}
?>
