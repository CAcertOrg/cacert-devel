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
<p><?=_("Please Note: You only need to enter the main part of your domain, eg. mydomain.com rather then www.mydomain.com. Once you have verified your domain you are able to enter any sub-domain, such as www.mydomain.com or www.this.is.mydomain.com as the system checks from right to left, rather then specific hostnames when you upload a CSR to the system.")?></p>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">

  <tr>
    <td colspan="2" class="title"><?=_("Add Domain")?></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?=_("Domain")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="newdomain" value="<?=array_key_exists('newdomain',$_GET)?sanitizeHTML($_GET['newdomain']):''?>"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("I own or am authorised to control this domain")?>"/></td>
  </tr>
</table>     
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="csrf" value="<?=make_csrf('adddomain')?>" />
</form>
<p><?=_("Currently we only issue certificates for Punycode domains if the person requesting them has code signing attributes attached to their account, as these have potentially slightly higher security risk.")?></p>
