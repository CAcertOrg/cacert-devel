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
<?
	if (intval($_REQUEST['force']) === 1)
{
?>

<p style="border:dotted 1px #900;padding:0.3em;bold;color:#ffffff;background-color:#ff0000;"><strong><center>
<?=_("For your own security you should change your pass phrase immediately!"); ?></center></strong>
</p>
<?}?>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="2" class="title"><?=_("Change Pass Phrase")?></td>
  </tr>
<?php if($_SESSION['_config']['hostname'] != $_SESSION['_config']['securehostname']) { ?>
  <tr>
    <td class="DataTD"><?=_("Old Pass Phrase")?>: </td>
    <td class="DataTD"><input type="password" name="oldpassword"></td>
  </tr>
<?php } ?>
  <tr>
    <td class="DataTD"><?=_("New Pass Phrase")?><font color="red">*</font>: </td>
    <td class="DataTD"><input type="password" name="pword1"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Pass Phrase Again")?><font color="red">*</font>: </td>
    <td class="DataTD"><input type="password" name="pword2"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><font color="red">*</font><?=_("Please note, in the interests of good security, the pass phrase must be made up of an upper case letter, lower case letter, number and symbol (all white spaces at the beginning and end are removed).")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Update Pass Phrase")?>"></td>
  </tr>
</table>
<input type="hidden" name="csrf" value="<?=make_csrf('pwchange')?>" />
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
