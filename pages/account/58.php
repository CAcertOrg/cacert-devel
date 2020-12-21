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
*/

if ($_SESSION['profile']['admin'] != 1 || !array_key_exists('userid',$_REQUEST) || intval($_REQUEST['userid']) < 1) {
	echo _('You do not have access to this page');
} else {
	$user_id = intval($_REQUEST['userid']);
	$query = "select `users`.`fname`, `users`.`mname`, `users`.`lname` from `users` where `id`='$user_id' and `users`.`deleted`=0";
	$res = $db_conn->query($query);
	if($res->num_rows != 1){
		echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are afoot!");
	} else {
		if ($row = $res->fetch_assoc()){
			$username=sanitizeHTML($row['fname']).' '.sanitizeHTML($row['mname']).' '.sanitizeHTML($row['lname']);
			$query = "select `orginfo`.`o`, `org`.`masteracc`
				FROM `orginfo`, `org`
				WHERE `orginfo`.`id` = `org`.`orgid`
				AND `org`.`memid`='$user_id' order by `orginfo`.`o`";
			$res1 = $db_conn->query($query);?>
			<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper"><?
			if ($res1->num_rows <= 0) {?>
				<tr>
					<td colspan="2" class="title"><?=sprintf(_('%s is not listed as Organisation Administrator'), $username)?></td>
				</tr>
			<?}else{?>
				<tr>
					<td colspan="2" class="title"><?=sprintf(_('%s is listed as Organisation Administrator for:'), $username)?></td>
				</tr>
				<tr>
					<td class="DataTD"><b><?=_('Organisation')?></b></td>
					<td class="DataTD"><b><?=_('Masteraccount')?></b></td>
				</tr><?
				while($drow = $res1->fetch_assoc()){?>
					<tr>
						<td class="DataTD"><?=$drow['o']?></td>
						<td class="DataTD"><?=$drow['masteracc'] ? _("Yes") : _("No") ?></td>
					</tr>
				<?}
			}
			?></table>
<?		}else{
				echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are afoot!");
		}
	}
}
?>
