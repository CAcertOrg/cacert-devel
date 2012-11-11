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
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="4" class="title"><?=_("Email Addresses")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Default")?></td>
    <td class="DataTD"><?=_("Status")?></td>
    <td class="DataTD"><?=_("Delete")?></td>
    <td class="DataTD"><?=_("Address")?></td>

<?
	$query = "select * from `email` where `memid`='".intval($_SESSION['profile']['id'])."' and `deleted`=0";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		if($row['hash'] == "")
			$verified = _("Verified");
		else
			$verified = _("Unverified");
?>
  <tr>
    <td class="DataTD"><? if($row['hash'] == "") { ?><input type="radio" name="emailid" value="<?=$row['id']?>"
	<? if($row['email'] == $_SESSION['profile']['email']) echo " checked"; ?>><? } else { echo "&nbsp;"; } ?></td>
    <td class="DataTD"><?=$verified?></td>
<? if($row['email'] == $_SESSION['profile']['email']) { ?>
    <td class="DataTD"><?=_("N/A")?></td>
<? } else { ?>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?=$row['id']?>"></td>
<? } ?>
    <td class="DataTD"><?=sanitizeHTML($row['email'])?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="makedefault" value="<?=_("Make Default")?>"></td>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Delete")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="csrf" value="<?=make_csrf('chgdef')?>" />
</form>
<p>
<?=_("Please Note: You can not set an unverified account as a default account, and you can not remove a default account. To remove the default account you must set another verified account as the default.")?>
</p>
