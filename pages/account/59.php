<?/*
       LibreSSL - CAcert web application
       Copyright (C) 2004-2008    CAcert Inc.

       This program is free software; you can redistribute it and/or modify
       it under the terms of the GNU General Public License as published by
       the Free Software Foundation; version 2 of the License.

       This program is distributed in the hope that it will be useful,
       but WITHOUT ANY WARRANTY; without even the implied warranty of
       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
       GNU General Public License for more details.

       You should have received a copy of the GNU General Public License
       along with this program; if not, write to the Free Software
       Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA    02110-1301    USA
*/
include_once($_SESSION['_config']['filepath']."/includes/notary.inc.php");


$colspandefault=2;
$userid = intval($_REQUEST['userid']);
$res =get_user_data($userid);



if(mysql_num_rows($res) <= 0)
{
    echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are afoot!");
    exit;
}

$row = mysql_fetch_assoc($res);

$fname = $row['fname'];
$mname = $row['mname'];
$lname = $row['lname'];
$suffix = $row['suffix'];
$dob = $row['dob'];
$username = $fname." ".$mname." ".$lname." ".$suffix;
$email = $row['email'];
$alerts =get_alerts($userid);
$support=0;
if(intval($_REQUEST['oldid'])==43){
    $support=$_SESSION['profile']['admin'];
}
$ticketno = ""; if(array_key_exists('ticketno', $_SESSION)) $ticketno = $_SESSION['ticketno'];
if (!valid_ticket_number($ticketno) && $support == 1) {
    printf(_("I'm sorry, you did not enter a ticket number! %s Support is not allowed to view the account history without a ticket number."), '<br/>');
    echo '<br/><a href="account.php?id=43&amp;userid=' . intval($_REQUEST['userid']) .'">'. _('Back to previous page.').'</a>';
    showfooter();
    exit;
}
if ( $support == 1) {
    if (!write_se_log($userid, $_SESSION['profile']['id'], 'SE View account history', $_REQUEST['ticketno'])) {
        echo _("Writing to the admin log failed. Can't continue.");
        printf('<br/><a href="account.php?id=43&amp;userid=' . intval($_REQUEST['userid']) . '">' . _('Back to previous page.') .'</a>');
        showfooter();
        exit;
    }
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?=$colspandefault ?>" class="title"><?=sprintf(_('Account history of %s'),$username)?></td>
    </tr>
    <tr>
        <td colspan="<?=$colspandefault ?>" class="title"><?=_('User actions')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?=_('User name')?></td>
        <td class="DataTD"><?=$username?></td>
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
    <tr>
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
$dres = get_email_addresses($userid,'',1);
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
<?}
$dres = get_domains($userid,'',1);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="3" class="title"><?=_('Domains')?></td>
    </tr>
<?
if(mysql_num_rows($dres) > 0) {
    output_log_domains_header();
    while($drow = mysql_fetch_assoc($dres))
    {
          output_log_domains($drow,$email);
    }
}ELSE{?>
          <td colspan="3" ><?=_('no entry avialable')?></td>
<?}?>
</table>
<br/>

<?
$dres = get_training_results($userid);
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
$dres = get_user_agreements($userid);
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
$dres = get_client_certs($userid,1);
$colspan=10;
if (1==$support) {
    $colspan=7;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?=$colspan?>" class="title"><?=_('Client certificates')?></td>
    </tr>
<?
    if(mysql_num_rows($dres) > 0) {
        output_client_cert_header($support);
        while($drow = mysql_fetch_assoc($dres))
        {
            output_client_cert($drow,$support);
        }
    }ELSE{
        ?><td colspan="<?=$colspan?>" ><?=_('no entry avialable')?></td><?
    }?>
</table>
<br/>

<?
$dres = get_server_certs($userid,1);
$colspan = 8;
if (1 == $support) {
    $colspan = 5;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?=$colspan?>" class="title"><?=_('Server certificates')?></td>
    </tr>
<?
    if(mysql_num_rows($dres) > 0) {
        output_server_certs_header($support);
        while($drow = mysql_fetch_assoc($dres))
        {
            output_server_certs($drow,$support);
        }
    }ELSE{
        ?><td colspan="<?=$colspan?>" ><?=_('no entry avialable')?></td><?
    }?>
</table>
<br/>

<?
$dres = get_gpg_certs($userid,1);
$colspan = 6;
if (1 == $support) {
    $colspan = 4;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?=$colspan?>" class="title"><?=_('GPG/PGP certificates')?></td>
    </tr>
<?
    if(mysql_num_rows($dres) > 0) {
        output_gpg_certs_header($support);
        while($drow = mysql_fetch_assoc($dres))
        {
            output_gpg_certs($drow,$support);
        }
    }ELSE{
        ?><td colspan="<?=$colspan?>" ><?=_('no entry avialable')?></td><?
    }?>
</table>
<br/>

<?

output_log_given_assurances($userid, $support);
?><br/><?

output_log_received_assurances($userid, $support);
?><br/><?

$dres = get_se_log($userid);
$colspan = 2;
if (1 == $support) {
    $colspan = 4;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?=$colspan?>" class="title"><?=_('Admin log')?></td>
    </tr>
<?
    if(mysql_num_rows($dres) > 0) {
        output_log_se_header($support);
        while($drow = mysql_fetch_assoc($dres))
        {
           output_log_se($drow,$support);
        }
    }ELSE{
        ?><td colspan="<?=$colspan?>" ><?=_('no entry avialable')?></td><?
    }
    if ($support==1) {
        ?><td colspan="<?=$colspan?>" ><a href="account.php?id=43&amp;userid=<?= $userid ?>"><?= _('Back to previous page.')?></a></td><?
    }

    ?>

</table>
