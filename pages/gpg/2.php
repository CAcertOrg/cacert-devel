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
<form method="post" action="gpg.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="6" class="title"><?=_("OpenPGP Keys")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Status")?></td>
    <td class="DataTD"><?=_("Email Address")?></td>
    <td class="DataTD"><?=_("Expires")?></td>
    <td class="DataTD"><?=_("Key ID")?></td>
    <td colspan="2" class="DataTD"><?=_("Comment *")?></td>
<?
	$query = "select UNIX_TIMESTAMP(`issued`) as `issued`,
			UNIX_TIMESTAMP(`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`expire`) as `expired`,
			`expire`, `id`, `level`,
			`email`,`keyid`,`description` from `gpg` where `memid`='".intval($_SESSION['profile']['id'])."'
			ORDER BY `issued` desc";
	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
	{
?>
  <tr>
    <td colspan="6" class="DataTD"><?=_("No OpenPGP keys are currently listed.")?></td>
  </tr>
<? } else {
	while($row = mysql_fetch_assoc($res))
	{
		$verified = '';
		if($row['timeleft'] > 0)
			$verified = _("Valid");
		if($row['timeleft'] < 0)
			$verified = _("Expired");
		if($row['expired'] == 0)
			$verified = _("Pending");
?>
  <tr>
<? if($verified == _("Valid")) { ?>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><a href="gpg.php?id=3&amp;cert=<?=intval($row['id'])?>"><?=sanitizeHTML($row['email'])?></a></td>
<? } else if($verified == _("Pending")) { ?>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><?=sanitizeHTML($row['email'])?></td>
<? } else { ?>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><a href="gpg.php?id=3&amp;cert=<?=intval($row['id'])?>"><?=sanitizeHTML($row['email'])?></a></td>
<? } ?>
    <td class="DataTD"><?=$row['expire']?></td>
    <td class="DataTD"><a href="gpg.php?id=3&amp;cert=<?=intval($row['id'])?>"><?=sanitizeHTML($row['keyid'])?></a></td>
    <td class="DataTD"><input name="comment_<?=intval($row['id'])?>" type="text" value="<?=htmlspecialchars($row['description'])?>" /></td>
    <td class="DataTD"><input type="checkbox" name="check_comment_<?=intval($row['id'])?>" /></td>
  </tr>
<? } ?>
<? } ?>
  <tr>
    <td class="DataTD" colspan="6">
      <?=_('* Comment is NOT included in the certificate as it is intended for your personal reference only. To change the comment tick the checkbox and hit "Change Settings".')?>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="6"><input type="submit" name="change" value="<?=_("Change settings")?>" /> </td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=intval($id)?>" />
</form>
