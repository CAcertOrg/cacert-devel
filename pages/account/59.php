<?php
/*
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


$userid = intval($_REQUEST['userid']);

$res = get_user_data($userid);
if (mysql_num_rows($res) <= 0)
{
    echo _("I'm sorry, the user you were looking for seems to have disappeared! Bad things are afoot!");
    exit;
}

$user = mysql_fetch_assoc($res);

$fname = $user['fname'];
$mname = $user['mname'];
$lname = $user['lname'];
$suffix = $user['suffix'];
$dob = $user['dob'];
$username = $fname." ".$mname." ".$lname." ".$suffix;
$email = $user['email'];
$alerts =get_alerts($userid);

$ticketno = "";
if (array_key_exists('ticketno', $_SESSION)) {
    $ticketno = $_SESSION['ticketno'];
}

$oldid = 0;
if (array_key_exists('oldid', $_REQUEST)) {
    $oldid = intval($_REQUEST['oldid']);
}

// Support Engineer access restrictions
$support=0;
if ($userid != $_SESSION['profile']['id']) {
    // Check if support engineer
    if (array_key_exists('admin', $_SESSION['profile']) &&
        $_SESSION['profile']['admin'] != 0)
    {
        $support=$_SESSION['profile']['admin'];

    } else {
        echo _("You do not have access to this page.");
        showfooter();
        exit;
    }

    if (!valid_ticket_number($ticketno)) {
        printf(_("I'm sorry, you did not enter a ticket number! %s Support is not allowed to view the account history without a ticket number."), '<br/>');
        echo '<br/><a href="account.php?id=43&amp;userid='.intval($userid).'">'. _('Back to previous page.') .'</a>';
        showfooter();
        exit;
    }

    if (!write_se_log($userid, $_SESSION['profile']['id'], 'SE View account history', $ticketno)) {
        echo _("Writing to the admin log failed. Can't continue.");
        echo '<br/><a href="account.php?id=43&amp;userid='.intval($userid).'">'. _('Back to previous page.') .'</a>';
        showfooter();
        exit;
    }
}

// Account details
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="2" class="title"><?printf(_('Account history of %s'),$username)?></td>
    </tr>
    <tr>
        <td colspan="2" class="title"><?php echo _('User actions')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _('User name')?></td>
        <td class="DataTD"><?php echo sanitizeHTML($username)?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _('Date of Birth')?></td>
        <td class="DataTD"><?php echo sanitizeHTML($dob)?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Is Assurer")?>:</td>
        <td class="DataTD"><?php echo ($user['assurer']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Blocked Assurer")?>:</td>
        <td class="DataTD"><?php echo ($user['assurer_blocked']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Account Locking")?>:</td>
        <td class="DataTD"><?php echo ($user['locked']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Code Signing")?>:</td>
        <td class="DataTD"><?php echo ($user['codesign']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Org Assurer")?>:</td>
        <td class="DataTD"><?php echo ($user['orgadmin']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("TTP Admin")?>:</td>
        <td class="DataTD"><?php echo $user['ttpadmin']._(' - 0 = none, 1 = TTP Admin, 2 = TTP TOPUP admin')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Location Admin")?>:</td>
        <td class="DataTD"><?php echo ($user['locadmin']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Admin")?>:</td>
        <td class="DataTD"><?php echo ($user['admin']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Ad Admin")?>:</td>
        <td class="DataTD"><?php echo $user['adadmin']._(' - 0 = none, 1 = submit, 2 = approve')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("General Announcements")?>:</td>
        <td class="DataTD"><?php echo ($alerts['general']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
          <td class="DataTD"><?php echo _("Country Announcements")?>:</td>
          <td class="DataTD"><?php echo ($alerts['country']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Regional Announcements")?>:</td>
        <td class="DataTD"><?php echo ($alerts['regional']==0)? _('No'):_('Yes')?></td>
    </tr>
    <tr>
        <td class="DataTD"><?php echo _("Within 200km Announcements")?>:</td>
        <td class="DataTD"><?php echo ($alerts['radius']==0)? _('No'):_('Yes')?></td>
    </tr>
</table>
<br/>
<?php
// Email addresses
$dres = get_email_addresses($userid,'',1);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="3" class="title"><?php echo _('Email addresses')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_log_email_header();
    while ($drow = mysql_fetch_assoc($dres))
    {
        output_log_email($drow,$email);
    }
} else {
    ?>
    <tr>
        <td colspan="3" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
</table>
<br/>
<?php
// Domains
$dres = get_domains($userid, 1);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="3" class="title"><?php echo _('Domains')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_log_domains_header();
    while ($drow = mysql_fetch_assoc($dres))
    {
          output_log_domains($drow);
    }
} else {
    ?>
    <tr>
        <td colspan="3" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
</table>
<br/>

<?php // Trainings
$dres = get_training_results($userid);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="3" class="title"><?php echo _('Trainings')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_log_training_header();
    while ($drow = mysql_fetch_assoc($dres))
    {
        output_log_training($drow);
    }
} else {
    ?>
    <tr>
        <td colspan="3" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
</table>
<br/>

<?php // User Agreements
$dres = get_user_agreements($userid);
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="4" class="title"><?php echo _('User agreements')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_log_agreement_header();
    while ($drow = mysql_fetch_assoc($dres))
    {
        output_log_agreement($drow);
    }
} else {
    ?>
    <tr>
        <td colspan="4" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
</table>
<br/>

<?php // Client Certificates
$dres = get_client_certs($userid, 1);
$colspan=8;
if (1 == $support) {
    $colspan=6;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?php echo $colspan?>" class="title"><?php echo _('Client certificates')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_client_cert_header($support);
    while ($drow = mysql_fetch_assoc($dres))
    {
        output_client_cert($drow,$support);
    }
} else {
    ?>
    <tr>
        <td colspan="<?php echo $colspan?>" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
</table>
<br/>

<?php // Server Certificates
$dres = get_server_certs($userid,1);
$colspan = 7;
if (1 == $support) {
    $colspan = 5;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?php echo $colspan?>" class="title"><?php echo _('Server certificates')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_server_certs_header($support);
    while ($drow = mysql_fetch_assoc($dres))
    {
        output_server_certs($drow,$support);
    }
} else {
    ?>
    <tr>
        <td colspan="<?php echo $colspan?>" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
</table>
<br/>

<?php // GPG Certificates
$dres = get_gpg_certs($userid,1);
$colspan = 6;
if (1 == $support) {
    $colspan = 4;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?php echo $colspan?>" class="title"><?php echo _('GPG/PGP certificates')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_gpg_certs_header($support);
    while ($drow = mysql_fetch_assoc($dres))
    {
        output_gpg_certs($drow, $support);
    }
} else {
    ?>
    <tr>
        <td colspan="<?php echo $colspan?>" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }?>
</table>
<br/>

<?php
output_given_assurances($userid, $support, $ticketno, 1);
?><br/><?php
output_received_assurances($userid, $support, $ticketno, 1);
?><br/><?php
$dres = get_se_log($userid);
$colspan = 2;
if (1 == $support) {
    $colspan = 4;
}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
        <td colspan="<?php echo $colspan?>" class="title"><?php echo _('Admin log')?></td>
    </tr>
<?php if (mysql_num_rows($dres) > 0) {
    output_log_se_header($support);
    while ($drow = mysql_fetch_assoc($dres))
    {
       output_log_se($drow,$support);
    }
} else {
    ?>
    <tr>
        <td colspan="<?php echo $colspan?>" ><?php echo _('no entry available')?></td>
    </tr>
    <?php }
?>
<tr>
    <td colspan="<?php echo $colspan?>" >
        <a href="account.php?id=<?php echo $oldid?intval($oldid):($support?43:13)?>&amp;userid=<?php echo intval($userid)?>"><?php echo _('Back to previous page.')?></a>
    </td>
</tr>

</table>
