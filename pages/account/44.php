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
*/

if(array_key_exists('error',$_SESSION['_config']) && $_SESSION['_config']['error'] != "") {
    ?>
    <div style="color: orange;">ERROR: <?=$_SESSION['_config']['error']?></div>
    <?
    unset($_SESSION['_config']['error']);
}

$ticketno = "";
if (array_key_exists('ticketno', $_SESSION)) {
    $ticketno = $_SESSION['ticketno'];
}

if (!valid_ticket_number($ticketno)) {
    printf(_("I'm sorry, you did not enter a ticket number! %s You cannot reset the password."), '<br/>');
    echo '<br/><a href="account.php?id=43&amp;userid='.intval($_REQUEST['userid']).'">'._('Back to previous page.').'</a>';
    showfooter();
    exit;
 }
?>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Change Password")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
    <td class="DataTD"><b><?=sanitizeHTML($_REQUEST['email'])?></b></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("New Password")?>:</td>
    <td class="DataTD"><input type="text" name="newpass" value="<?=array_key_exists('newpass',$_REQUEST)?sanitizeHTML($_REQUEST['newpass']):""?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>
<input type="hidden" name="userid" value="<?=intval($_REQUEST['userid'])?>">
<input type="hidden" name="oldid" value="<?=intval($id)?>">
<input type="hidden" name="ticketno" value="<?=sanitizeHTML($ticketno)?>"/>
</form>
