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
<? $viewall=0; if(array_key_exists('viewall',$_REQUEST)) $viewall=intval($_REQUEST['viewall']); ?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="8" class="title"><?=_("Domain Certificates")?> - <a href="account.php?id=12&amp;viewall=<?=intval(!$viewall)?>"><?=$viewall?_("Hide old certificates"):_("View all certificates")?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Renew/Revoke/Delete")?></td>
    <td class="DataTD"><?=_("Status")?></td>
    <td class="DataTD"><?=_("CommonName")?></td>
    <td class="DataTD"><?=_("SerialNumber")?></td>
    <td class="DataTD"><?=_("Revoked")?></td>
    <td class="DataTD"><?=_("Expires")?></td>
    <td colspan="2" class="DataTD"><?=_("Comment *")?></td>
  </tr>
<?
	$query = "select UNIX_TIMESTAMP(`domaincerts`.`created`) as `created`,
			UNIX_TIMESTAMP(`domaincerts`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`domaincerts`.`expire`) as `expired`,
			`domaincerts`.`expire`,
			`domaincerts`.`revoked` as `revoke`,
			UNIX_TIMESTAMP(`revoked`) as `revoked`,
			if (`domaincerts`.`expire`=0,CURRENT_TIMESTAMP(),`domaincerts`.`modified`) as `modified`,
			`CN`, `domaincerts`.`serial`, `domaincerts`.`id` as `id`,
			`domaincerts`.`description`
			from `domaincerts`,`domains`
			where `memid`='".intval($_SESSION['profile']['id'])."' and `domaincerts`.`domid`=`domains`.`id` ";
	if($viewall != 1)
	{
		$query .= "AND `revoked`=0 AND `renewed`=0 ";
		$query .= "HAVING `timeleft` > 0 or `expire` = 0 ";
	}
	$query .= "ORDER BY `modified` desc";
//echo $query."<br>\n";
	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
	{
?>
  <tr>
    <td colspan="8" class="DataTD"><?=_("No certificates are currently listed.")?></td>
  </tr>
<? } else {
	while($row = mysql_fetch_assoc($res))
	{
		if($row['timeleft'] > 0)
			$verified = _("Valid");
		if($row['timeleft'] < 0)
			$verified = _("Expired");
		if($row['expired'] == 0)
			$verified = _("Pending");
		if($row['revoked'] > 0)
			$verified = _("Revoked");
		if($row['revoked'] == 0)
			$row['revoke'] = _("Not Revoked");
?>
  <tr>
<? if($verified != _("Pending") && $verified != _("Revoked")) { ?>
    <td class="DataTD"><input type="checkbox" name="revokeid[]" value="<?=intval($row['id'])?>"/></td>
<? } else if($verified != _("Revoked")) { ?>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?=intval($row['id'])?>"/></td>
<? } else { ?>
    <td class="DataTD">&nbsp;</td>
<? } ?>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><a href="account.php?id=15&amp;cert=<?=intval($row['id'])?>"><?=htmlspecialchars($row['CN'])?></a></td>
    <td class="DataTD"><?=$row['serial']?></td>
    <td class="DataTD"><?=$row['revoke']?></td>
    <td class="DataTD"><?=$row['expire']?></td>
    <td class="DataTD"><input name="comment_<?=intval($row['id'])?>" type="text" value="<?=htmlspecialchars($row['description'])?>" /></td>
    <td class="DataTD"><input type="checkbox" name="check_comment_<?=intval($row['id'])?>" /></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="8">
      <a href="account.php?id=12&amp;viewall=<?=intval(!$viewall)?>"><b><?=$viewall?_("Hide old certificates"):_("View all certificates")?></b></a>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="8">
      <?=_('* Comment is NOT included in the certificate as it is intended for your personal reference only. To change the comment tick the checkbox and hit "Change Settings".')?>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="6"><input type="submit" name="renew" value="<?=_("Renew")?>"/>&#160;&#160;&#160;&#160;
	    <input type="submit" name="revoke" value="<?=_("Revoke/Delete")?>"></td>
	<td class="DataTD" colspan="2"><input type="submit" name="change" value="<?=_("Change settings")?>"/> </td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="8"><?=_("From here you can delete pending requests, or revoke valid certificates.")?></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=intval($id)?>"/>
<input type="hidden" name="csrf" value="<?=make_csrf('srvcerchange')?>"/>
</form>
