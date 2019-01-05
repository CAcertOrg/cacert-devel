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

<p>
<?php echo _("Please make sure the following details are correct before proceeding ".
		"any further.")?>
</p>

<p><?php if (is_array($_SESSION['_config']['rows'])) {
	foreach ($_SESSION['_config']['rows'] as $row) {
		echo _("CommonName"), ": $row<br>\n";
	}
}

if (is_array($_SESSION['_config']['altrows'])) {
	foreach ($_SESSION['_config']['altrows'] as $row) {
		echo _("subjectAltName"), ": $row<br>\n";
	}
}
?></p>

<p>
<?php echo _("No additional information will be included on certificates because it ".
		"can not be automatically checked by the system.")?>
</p>

<p><?php if (array_key_exists('rejected',$_SESSION['_config']) &&
		is_array($_SESSION['_config']['rejected'])) {
	echo _("The following hostnames were rejected because the system couldn't ".
			"link them to your account, if they are valid please verify the ".
			"domains against your account."), "<br>\n";
	
	foreach ($_SESSION['_config']['rejected'] as $row) {
		echo _("Rejected");
		echo ": <a href='account.php?id=7&amp;newdomain=$row'>$row</a><br>\n";
	}
}
?></p>

<?php if (is_array($_SESSION['_config']['rows']) ||
		is_array($_SESSION['_config']['altrows'])) {
	?>
	<form method="post" action="account.php">
		<p>
			<input type="submit" name="process" value="<?php echo _("Submit")?>">
			<input type="hidden" name="oldid" value="<?php echo $id?>">
		</p>
	</form>
	<?php } else {
	?>
	<p>
		<b><?php echo _("Unable to continue as no valid commonNames or ".
				"subjectAltNames were present on your certificate request.")?></b>
	</p>
	<?php }
