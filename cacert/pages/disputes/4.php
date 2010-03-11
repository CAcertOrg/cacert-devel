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
<H3><?=_("Email Dispute")?></H3>
<p><? printf(_("Currently the email '%s' is in dispute, you have been sent an email to resolve the issue, below you have the option to accept, reject or report the request as fraudulent."), sanitizeHTML($_SESSION['_config']['email'])); ?></p>
<form method="post" action="disputes.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Email Dispute")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="radio" name="action" value="reject" checked> <?=_("Reject Dispute")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="radio" name="action" value="accept"> <?=_("Accept Dispute")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="radio" name="action" value="abuse"> <?=_("Report Dispute as Abuse")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Update Dispute")?>"></td>
  </tr>
</table>     
<input type="hidden" name="type" value="reallyemail">
<input type="hidden" name="emailid" value="<?=intval($_REQUEST['emailid'])?>">
<input type="hidden" name="hash" value="<?=sanitizeHTML($_REQUEST['hash'])?>">

</form> 
