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
<?php if($_SESSION['_config']['error'] != "") { ?><div color="orange">ERROR: <?php echo $_SESSION['_config']['error']?></div><?php unset($_SESSION['_config']['error']); } ?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?php echo _("Delete Account")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Email")?>:</td>
    <td class="DataTD"><b><?php echo sanitizeHTML($_REQUEST['email'])?></b></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("New Username from arbitration number + sequence number a20xxyyzz.a.b")?>:</td>
    <td class="DataTD"><input type="text" name="arbitrationno"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><?php echo _("Are you sure you want to delete this user, while not actually deleting the account it will completely disable it and revoke any/all certificates currently issued.")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="cancel" value="<?php echo _("No")?>"> <input type="submit" name="process" value="<?php echo _("Yes")?>"></td>
  </tr>
</table>
<input type="hidden" name="userid" value="<?php echo intval($_REQUEST['userid'])?>">
<input type="hidden" name="oldid" value="<?php echo $id?>">
</form>
