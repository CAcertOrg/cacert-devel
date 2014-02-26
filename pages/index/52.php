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

?>

<div style="text-align: center;">
	<h1><?=_('CAcert Community Agreement Acceptance')?></h1>
	<p><?=sprintf(_('To get access to your account you need to accept the %s CAcert Community Agreement %s (CCA).'),'<a href="/policy/CAcertCommunityAgreement.php">', '</a>')?></p>
	<p><?=_('#### Explanation why #### Please replace me ####')?></p>
	<p><?=sprintf(_('If you do not want to accept the CCA you should think about closing your account. In this case please send an email to support (%s).'),'<a href="mailto:support@cacert.org">support@cacert.org</a>')?></p>
	<form method="post" action="index.php">
		<input type="submit" name="agree" value="<?=_('I agree CCA')?>">
		<input type="submit" name="disagree" value="<?=_('I do not want to accept the CCA')?>">

		<input type="hidden" name="oldid" value="<?=$id?>">
	</form>
</div>
