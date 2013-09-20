<?php
/*LibreSSL - CAcert web application
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
*/

//*******************  TTP Console ************

if ($_SESSION['profile']['ttpadmin'] < 1) {
	echo _("You are not allowed to view this page.");
	exit;
}

//Check for test or productive environment, in case of test the user data for the print out is extended by 'test system'
$testserver='';
if ($_SESSION['_config']['normalhostname']=='cacert1.it-sls.de') {
	$testserver=' test system';
}

$row = $_SESSION['_config']['notarise'];
$fname = $row['fname'];
$mname = $row['mname'];
$lname = $row['lname'];
$suffix = $row['suffix'];
$fullname = $fname." ".$mname." ".$lname." ".$suffix;
$email = $row['email'];
$dob = date_format(new DateTime($row['dob']), 'Y-m-d');
$userid = $row['id'];

//List TTP Assurances and TotalPoints
//changed get_received_assurances ($userid, $support)

//include_once($_SESSION['_config']['filepath']."/includes/wot.inc.php");
include_once($_SESSION['_config']['filepath']."/includes/notary.inc.php");

output_received_assurances(intval($userid),2); //support==2 => TTP


$query = "select sum(`points`) as `points` from `notary` where `to`='".intval($userid)."'";
$dres = mysql_query($query);
$drow = mysql_fetch_assoc($dres);

$points=$drow['points'];
if ($points<1) {
	$points=0;
}

$res = get_received_assurances(intval($userid), 2);
$ttp_assurances_count=$num_rows = mysql_num_rows($res);

//Form
?>
<table align="center" class="wrapper">
	<tr>
		<td class="title"><?=sprintf(_('Total assurance points for %s'),$fullname)?></td>
	</tr>
	<tr>
		<td><?=sprintf(_('%s points'), $points)?></td>
	</tr>
</table>
<br/>
<form action="https://pdf.cacert.eu/cacertpdf.php" method="get">
	<table align="center" class="wrapper">
		<tr>
			<td colspan="2" class="title"><?= _('TTP CAP form creation')?></td>
		</tr>
		<tr>
			<td colspan="2" class="title"><?= _('User information')?></td>
		</tr>
		<tr>
			<td><?=_('Fullname')?><input type="hidden" name="fullname" value="<?=$fullname.$testserver?>"/></td>
			<td><?=$fullname?></td>
		</tr>
		<tr>
			<td><?=_('Date of Birth')?><input type="hidden" name="dob" value="<?=$dob.$testserver?>"/></td>
			<td><?=$dob?></td>
		</tr>
		<tr>
			<td><?=_('Email')?><input type="hidden" name="email" value="<?=$email.$testserver?>"/></td>
			<td><?=$email?></td>
		</tr>
		<tr></tr>
		<tr>
			<td><?=_('Country where the TTP will be visited')?></td>
			<td>
				<?
				$ttpcountries=get_array_from_ini('../config/ttp.ini');
				echo create_selectbox_HTML('type',$ttpcountries, '',TRUE);
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="title"><?=_('TTP Admin postal address, including name, street, country etc.')?></td>
		</tr>
		<tr>
			<td><?=_('Line').' 1'?></td>
			<td><input type="text" name="adress" /></td>
		</tr>
		<tr>
			<td><?=_('Line').' 2'?></td>
			<td><input type="text" name="adress1" /></td>
		</tr>
		<tr>
			<td><?=_('Line').' 3'?></td>
			<td><input type="text" name="adress2" /></td>
		</tr>
		<tr>
			<td><?=_('Line').' 4'?></td>
			<td><input type="text" name="adress3" /></td>
		</tr>
		<tr>
			<td><?=_('Line').' 5'?></td>
			<td><input type="text" name="adress4" /></td>
		</tr>
		<tr>
			<td colspan="2" class="title">
			<?
			if ($points>=100 || $ttp_assurances_count>=2) {
				echo _('No TTP assurance allowed');
			}else{
				?><input type="submit" value="<?=_('Create TTP CAP pdf file')?>"/><?
			}?>
			</td>
		</tr>
	</table>
	<input type="hidden" name="lang" value="en"/>
</form>

<div class="blockcenter">
	<a href="wot.php?id=6&amp;userid=<?=$userid ?>"><?=_("Back")?></a>
</div>
