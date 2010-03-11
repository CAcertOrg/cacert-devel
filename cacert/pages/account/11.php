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
<p>
<?=_("Please make sure the following details are correct before proceeding any further.")?>
</p>
<?// print_r($_SESSION['_config']['altrows']); ?>
<p>
<? if(is_array($_SESSION['_config']['rows']))
	foreach($_SESSION['_config']['rows'] as $row) { ?>
<?=_("CommonName")?>: <?=$row?><br>
<? } ?>
<? if(is_array($_SESSION['_config']['altrows']))
	foreach($_SESSION['_config']['altrows'] as $row) { ?>
<?=_("subjectAltName")?>: <?=$row?><br>
<? } ?>
<? if(1 == 0) { ?>
<?=_("Organisation")?>: <?=$_SESSION['_config']['O']?><br>
<?=_("Org. Unit")?>: <?=$_SESSION['_config']['OU']?><br>
<?=_("Location")?>: <?=$_SESSION['_config']['L']?><br>
<?=_("State/Province")?>: <?=$_SESSION['_config']['ST']?><br>
<?=_("Country")?>: <?=$_SESSION['_config']['C']?><br>
<?=_("Email Address")?>: <?=$_SESSION['_config']['emailAddress']?><br>
<? } ?>
<?=_("No additional information will be included on certificates because it can not be automatically checked by the system.")?>
<? if(array_key_exists('rejected',$_SESSION['_config']) && is_array($_SESSION['_config']['rejected'])) { ?>
<br><br><?=_("The following hostnames were rejected because the system couldn't link them to your account, if they are valid please verify the domains against your account.")?><br>
<? foreach($_SESSION['_config']['rejected'] as $row) { ?>
<?=_("Rejected")?>: <a href="account.php?id=7&amp;newdomain=<?=$row?>"><?=$row?></a><br>
<? } } ?>
<? if(is_array($_SESSION['_config']['rows']) || is_array($_SESSION['_config']['altrows'])) { ?>
<form method="post" action="account.php">
<input type="submit" name="process" value="<?=_("Submit")?>">
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
<? } else { ?>
<br><br><b><?=_("Unable to continue as no valid commonNames or subjectAltNames were present on your certificate request.")?></b>
<? } ?>
</p>
