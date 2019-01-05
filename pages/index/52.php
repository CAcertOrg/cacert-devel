<?php
/*
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

?>

<div style="text-align: center;">
	<h1><?php echo _('CAcert Community Agreement Acceptance')?></h1>
	<p><?php echo sprintf(_('To get access to your account your agreement to the %s CAcert Community Agreement %s (CCA) is required.'),'<a href="/policy/CAcertCommunityAgreement.php">', '</a>')?></p>
	<p><?php echo _('Every member, who has agreed to the CCA, should be able to rely on the fact that every other user of CAcert has also agreed to the CCA and that the same rules apply to everybody. Moreover it is a basic requirement for the audit to be able to tell who has accepted our rules.')?></p>
	<p><?php echo _('Originally the acceptance was not recorded. Up until now, we do not have your agreement on record. Once you have accepted the CCA (again) your agreement is recorded and you will not need to do this step again.')?></p>
	<p><?php echo sprintf(_('If you do not wish to accept the CCA you should consider to ask for the closing of your account as you will not be able to access our system. In this case please send an email to support (%s).'),'<a href="mailto:support@cacert.org">support@cacert.org</a>')?></p>
	<p><?php echo _('If you do not want to decide about the acceptance of the CCA now, you can come back at any time.')?></p>
	<form method="post" action="index.php">
		<input type="submit" name="agree" value="<?php echo _('I agree to the CCA')?>">
		<input type="submit" name="disagree" value="<?php echo _('I do not want to accept the CCA')?>">
		<input type="hidden" name="oldid" value="<?php echo $id?>">
	</form>
</div>
