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
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="2" class="title"><?=_("My Alert Settings")?></td>
  </tr>
  <tr>
    <td class="DataTD" valign="top"><b><?=_("Alert me if")?></b>: </td>
    <td class="DataTD" align="left"><input type="checkbox" name="general" value="1"<?php if(array_key_exists('general',$_REQUEST) && $_REQUEST['general']) echo " checked='checked'"; ?>><?=_("General Announcements")?><br>
	<input type="checkbox" name="country" value="1"<?php if(array_key_exists('country',$_REQUEST) && $_REQUEST['country']) echo " checked='checked'"; ?>><?=_("Country Announcements")?><br>
	<input type="checkbox" name="regional" value="1"<?php if(array_key_exists('regional',$_REQUEST) && $_REQUEST['regional']) echo " checked='checked'"; ?>><?=_("Regional Announcements")?><br>
	<input type="checkbox" name="radius" value="1"<?php if(array_key_exists('radius',$_REQUEST) && $_REQUEST['radius']) echo " checked='checked'"; ?>><?=_("Within 200km Announcements")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Update My Settings")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
