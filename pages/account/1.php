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
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Add Email")?></td>
  </tr>

  <tr>
    <td class="DataTD" width="125"><?php echo _("Email Address")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="newemail" value="<?php echo array_key_exists('newemail',$_SESSION['profile'])?sanitizeHTML($_SESSION['profile']['newemail']):''?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?php echo _("I own or am authorised to control this email address")?>"/></td>
  </tr>
</table>     
<input type="hidden" name="oldid" value="<?php echo $id?>">
<input type="hidden" name="csrf" value="<?php echo make_csrf('addemail')?>" />
</form> 
<p><?php echo _("Currently we only issue certificates for Punycode domains if the person requesting them has code signing attributes attached to their account, as these have potentially slightly higher security risk.")?></p>
