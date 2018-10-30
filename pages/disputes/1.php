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
<H3><?php echo _("Email Dispute")?></H3>
<p><?php echo _("If your dispute is successful you will have the email address removed from the system, you will need add the email address as per usual afterwards. The email will be removed from the current account and any certificates will be revoked.")?></p>
<form method="post" action="disputes.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Which Email?")?></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?php echo _("Email Address")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="dispute" value=""></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("File Dispute")?>"></td>
  </tr>
</table>     
<input type="hidden" name="oldid" value="<?php echo $id?>">
<input type="hidden" name="csrf" value="<?php echo make_csrf('emaildispute')?>" />
</form> 
