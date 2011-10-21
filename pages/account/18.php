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
    <td colspan="6" class="title"><?=_("Client Certificates")?> - <a href="account.php?id=18&amp;viewall=<?=!$viewall?>"><?=_("View all certificates")?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Renew/Revoke/Delete")?></td>
    <td class="DataTD"><?=_("Status")?></td>
    <td class="DataTD"><?=_("CommonName")?></td>
	<td class="DataTD"><?=_("SerialNumber")?></td>
    <td class="DataTD"><?=_("Revoked")?></td>
    <td class="DataTD"><?=_("Expires")?></td>

<?
	$query = "select UNIX_TIMESTAMP(`created`) as `created`,
			UNIX_TIMESTAMP(`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`expire`) as `expired`,
			`expire` as `expires`, `revoked` as `revoke`,
			UNIX_TIMESTAMP(`revoked`) as `revoked`, `CN`, `serial`, `id`
			from `orgemailcerts`, `org`
			where `memid`='".intval($_SESSION['profile']['id'])."' and
				`org`.`orgid`=`orgemailcerts`.`orgid` ";
	if($viewall != 1)
	{
		$query .= "AND `revoked`=0 AND `renewed`=0 ";
		$query .= "HAVING `timeleft` > 0 AND `revoked`=0 ";
	}
	$query .= "ORDER BY `modified` desc";
	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
	{
?>
  <tr>
    <td colspan="6" class="DataTD"><?=_("No client certificates are currently listed.")?></td>
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
<? if($verified == _("Valid") || $verified == _("Expired")) { ?>
    <td class="DataTD"><input type="checkbox" name="revokeid[]" value="<?=$row['id']?>"></td>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><a href="account.php?id=19&cert=<?=$row['id']?>"><?=$row['CN']?></a></td>
<? } else if($verified == _("Pending")) { ?>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?=$row['id']?>"></td>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><?=$row['CN']?></td>
<? } else { ?>
    <td class="DataTD">&nbsp;</td>
    <td class="DataTD"><?=$verified?></td>
    <td class="DataTD"><a href="account.php?id=19&cert=<?=$row['id']?>"><?=$row['CN']?></a></td>
<? } ?>
	<td class="DataTD"><?=$row['serial']?></td>
    <td class="DataTD"><?=$row['revoke']?></td>
    <td class="DataTD"><?=$row['expires']?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD" colspan="6"><input type="submit" name="renew" value="<?=_("Renew")?>">&#160;&#160;&#160;&#160;
    			<input type="submit" name="revoke" value="<?=_("Revoke/Delete")?>"></td>
  </tr>
<? } ?>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="csrf" value="<?=make_csrf('clicerchange')?>" />
</form>
<p><?=_("From here you can delete pending requests, or revoke valid certificates.")?></p>
