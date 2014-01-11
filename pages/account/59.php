<?/*
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
*/
include_once($_SESSION['_config']['filepath']."/includes/notary.inc.php");

$colspandefault=2;
//$userid = intval($_REQUEST['userid']);
$res =get_user_data($userid);

if(mysql_num_rows($res) <= 0)
{
	echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are a foot!");
	exit;
}

$row = mysql_fetch_assoc($res);

$fname = $row['fname'];
$mname = $row['mname'];
$lname = $row['lname'];
$suffix = $row['suffix'];
$dob = $row['dob'];
$name = $fname." ".$mname." ".$lname." ".$suffix;
$email = $row['email'];
$alerts =get_alerts($userid);



?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=sprintf(_('Account history of %s'),'username')?></td>
  </tr>
  <tr>
    <td colspan="<? $colspandefault ?>" class="title"><?=_('User actions')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_('User name')?></td>
    <td class="DataTD"><?=$name?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_('Date of Birth')?></td>
    <td class="DataTD"><?=$dob?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Is Assurer")?>:</td>
    <td class="DataTD"><?= ($row['assurer']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Blocked Assurer")?>:</td>
    <td class="DataTD"><?= ($row['assurer_blocked']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Account Locking")?>:</td>
    <td class="DataTD"><?= ($row['locked']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Code Signing")?>:</td>
    <td class="DataTD"><?= ($row['codesign']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Org Assurer")?>:</td>
    <td class="DataTD"><?= ($row['orgadmin']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("TTP Admin")?>:</td>
    <td class="DataTD"><?= $row['ttpadmin']._(' - 0 = none, 1 = TTP Admin, 2 = TTP TOPUP admin')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Location Admin")?>:</td>
    <td class="DataTD"><?= ($row['locadmin']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Admin")?>:</td>
    <td class="DataTD"><?= ($row['admin']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Ad Admin")?>:</td>
    <td class="DataTD"><?= $row['adadmin']._(' - 0 = none, 1 = submit, 2 = approve')?></td>
  </tr>
    <td class="DataTD"><?=_("General Announcements")?>:</td>
    <td class="DataTD"><?= ($alerts['general']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Country Announcements")?>:</td>
    <td class="DataTD"><?= ($row['id']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Regional Announcements")?>:</td>
    <td class="DataTD"><?= ($row['id']==0)? _('No'):_('Yes')?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Within 200km Announcements")?>:</td>
    <td class="DataTD"><?= ($row['id']==0)? _('No'):_('Yes')?></td>
  </tr>
</table>
<br/>
<?
$dres = get_email_address($userid,'',1);
if(mysql_num_rows($dres) > 0) {
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="3" class="title"><?=_('Email addresses')?></td>
  </tr>
<?
output_log_email_header();
while($drow = mysql_fetch_assoc($dres))
{
  output_log_email($drow,$email);
} ?>
</table>
<br/>
<?
$dres = get_domains($userid,'',1);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="3" class="title"><?=_('Domains')?></td>
  </tr>
<?
if(mysql_num_rows($dres) > 0) {
  output_log_domain_header();
  while($drow = mysql_fetch_assoc($dres))
  {
    output_log_domain($drow,$email);
  }
}ELSE{?>
    <td colspan="3" ><?=_('no entry avialable')?></td>
<?}?>
</table>
<br/>

<?
$dres = get_training_result($userid);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="3" class="title"><?=_('Trainings')?></td>
  </tr>
<?
if(mysql_num_rows($dres) > 0) {
  output_log_training_header();
  while($drow = mysql_fetch_assoc($dres))
  {
    output_log_training($drow);
  }
}ELSE{
  ?><td colspan="3" ><?=_('no entry avialable')?></td><?
}?>
</table>
<br/>

<?
$dres = get_user_agreement($userid,'',1);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="4" class="title"><?=_('User agreements')?></td>
  </tr>
<?
if(mysql_num_rows($dres) > 0) {
  output_log_agreement_header();
  while($drow = mysql_fetch_assoc($dres))
  {
    output_log_agreement($drow);
  }
}ELSE{
  ?><td colspan="4" ><?=_('no entry avialable')?></td><?
}?>
</table>
<br/>

<?
$dres = get_client_certs($userid);
$colspan=10;
if (1==$support) {
	$colspan=7;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="<? $colspan?>" class="title"><?=_('Client certificates')?></td>
  </tr>
<?
if(mysql_num_rows($dres) > 0) {
  output_client_cert_header($support);
  while($drow = mysql_fetch_assoc($dres))
  {
    output_client_cert($drow,$support);
  }
}ELSE{
  ?><td colspan="<? $colspan?>" ><?=_('no entry avialable')?></td><?
}?>
</table>
<br/>

<?
$dres = get_server_certs($userid);
$colspan=8;
if (1==$support) {
	$colspan=5;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="<? $colspan?>" class="title"><?=_('Server certificates')?></td>
  </tr>
<?
if(mysql_num_rows($dres) > 0) {
  output_log_server_certs_header($support);
  while($drow = mysql_fetch_assoc($dres))
  {
    output_log_server_certs($drow,$support);
  }
}ELSE{
  ?><td colspan="<? $colspan?>" ><?=_('no entry avialable')?></td><?
}?>
</table>
<br/>

<?
$dres = get_server_certs($userid);
$colspan=6;
if (1==$support) {
	$colspan=4;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="<? $colspan?>" class="title"><?=_('Server certificates')?></td>
  </tr>
<?
if(mysql_num_rows($dres) > 0) {
  output_log_server_certs_header($support);
  while($drow = mysql_fetch_assoc($dres))
  {
    output_log_server_certs($drow,$support);
  }
}ELSE{
  ?><td colspan="<? $colspan?>" ><?=_('no entry avialable')?></td><?
}?>
</table>
}