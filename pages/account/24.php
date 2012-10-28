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
<?
	// Reset session variables regarding Org's, present empty form	
	if (array_key_exists('O',$_SESSION['_config']))         $_SESSION['_config']['O'] = "";
  if (array_key_exists('contact',$_SESSION['_config']))   $_SESSION['_config']['contact'] = "";	
  if (array_key_exists('L',$_SESSION['_config']))         $_SESSION['_config']['L'] = "";
  if (array_key_exists('ST',$_SESSION['_config']))        $_SESSION['_config']['ST'] = "";
  if (array_key_exists('C',$_SESSION['_config']))         $_SESSION['_config']['C'] = "";
  if (array_key_exists('comments',$_SESSION['_config']))  $_SESSION['_config']['comments'] = "";
	
?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("New Organisation")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Organisation Name")?>:</td>
    <td class="DataTD"><input type="text" name="O" value="" maxlength="50" size="90"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Contact Email")?>:</td>
    <td class="DataTD"><input type="text" name="contact" value="" size="90"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Town/Suburb")?>:</td>
    <td class="DataTD"><input type="text" name="L" value="" size="90"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("State/Province")?>:</td>
    <td class="DataTD"><input type="text" name="ST" value="" size="90"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Country")?>:</td>
    <td class="DataTD"><input type="text" name="C" value="" size="5">(2 letter <a href="http://www.iso.org/iso/home/standards/country_codes/iso-3166-1_decoding_table.htm">ISO code</a>)</td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Comments")?>:</td>
    <td class="DataTD"><textarea name="comments" cols="60" rows="10"></textarea></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
