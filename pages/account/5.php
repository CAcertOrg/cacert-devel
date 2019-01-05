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
<?php $viewall=0; if(array_key_exists('viewall',$_REQUEST)) $viewall=intval($_REQUEST['viewall']); ?>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="10" class="title"><?php echo _("Client Certificates")?> - <a href="account.php?id=5&amp;viewall=<?php echo intval(!$viewall)?>"><?php echo $viewall?_("Hide old certificates"):_("View all certificates")?></a></td>
  </tr>
  <tr>
    <td class="DataTD"><?php echo _("Renew/Revoke/Delete")?></td>
    <td class="DataTD"><?php echo _("Status")?></td>
    <td class="DataTD"><?php echo _("Email Address")?></td>
    <td class="DataTD"><?php echo _("SerialNumber")?></td>
    <td class="DataTD"><?php echo _("Revoked")?></td>
    <td class="DataTD"><?php echo _("Expires")?></td>
    <td class="DataTD"><?php echo _("Login")?></td>
    <td colspan="2" class="DataTD"><?php echo _("Comment *")?></td>
<?php 	$query = "select UNIX_TIMESTAMP(`emailcerts`.`created`) as `created`,
			UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`emailcerts`.`expire`) as `expired`,
			`emailcerts`.`expire`,
			`emailcerts`.`revoked` as `revoke`,
			UNIX_TIMESTAMP(`emailcerts`.`revoked`) as `revoked`,
			if (`emailcerts`.`expire`=0,CURRENT_TIMESTAMP(),`emailcerts`.`modified`) as `modified` ,
			`emailcerts`.`id`,
			`emailcerts`.`CN`,
			`emailcerts`.`serial`,
			`emailcerts`.`disablelogin` as `disablelogin`,
			`emailcerts`.`description`
			from `emailcerts`
			where `emailcerts`.`memid`='".intval($_SESSION['profile']['id'])."'
			";
	if($viewall != 1)
		$query .= " AND `revoked`=0 AND `renewed`=0 ";
	$query .= " GROUP BY `emailcerts`.`id` ";
	if($viewall != 1)
		$query .= " HAVING `timeleft` > 0 or `expire` = 0 ";
	$query .= " ORDER BY `modified` desc";
// echo $query."<br>\n";
	$res = mysqli_query($_SESSION['mconn'], $query);
	if(mysqli_num_rows($res) <= 0)
	{
?>
  <tr>
    <td colspan="10" class="DataTD"><?php echo _("No client certificates are currently listed.")?></td>
  </tr>
<?php } else {
	while($row = mysqli_fetch_assoc($res))
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
<?php if($verified != _("Pending") && $verified != _("Revoked")) { ?>
    <td class="DataTD"><input type="checkbox" name="revokeid[]" value="<?php echo intval($row['id'])?>"></td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><a href="account.php?id=6&amp;cert=<?php echo intval($row['id'])?>"><?php echo (trim($row['CN'])=="" ? _("empty") : sanitizeHTML($row['CN']))?></a></td>
<?php } else if($verified != _("Revoked")) { ?>
    <td class="DataTD"><input type="checkbox" name="delid[]" value="<?php echo intval($row['id'])?>"></td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><?php echo (trim($row['CN'])=="" ? _("empty") : sanitizeHTML($row['CN']))?></td>
<?php } else { ?>
    <td class="DataTD">&nbsp;</td>
    <td class="DataTD"><?php echo $verified?></td>
    <td class="DataTD"><?php echo (trim($row['CN'])=="" ? _("empty") : sanitizeHTML($row['CN']))?></td>
<?php } ?>
    <td class="DataTD"><?php echo sanitizeHTML($row['serial'])?></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['revoke'])?></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['expire'])?></td>
    <td class="DataTD">
      <input type="checkbox" name="disablelogin_<?php echo intval($row['id'])?>" value="1" <?php echo $row['disablelogin']?"":'checked="checked"'?>/>
      <input type="hidden" name="cert_<?php echo intval($row['id'])?>" value="1" />
    </td>
    <td class="DataTD"><input name="comment_<?php echo intval($row['id'])?>" type="text" value="<?php echo htmlspecialchars($row['description'])?>" /></td>
    <td class="DataTD"><input type="checkbox" name="check_comment_<?php echo intval($row['id'])?>" /></td>
  </tr>
    <?php } ?>
  <tr>
    <td class="DataTD" colspan="9">
      <a href="account.php?id=5&amp;viewall=<?php echo intval(!$viewall)?>"><b><?php echo $viewall?_("Hide old certificates"):_("View all certificates")?></b></a>
    </td>
  </tr>

  <tr>
    <td class="DataTD" colspan="9">
      <?php echo _('* Comment is NOT included in the certificate as it is intended for your personal reference only. To change the comment tick the checkbox and hit "Change Settings".')?>
    </td>
  </tr>

  <tr>
    <td class="DataTD" colspan="5"><input type="submit" name="renew" value="<?php echo _("Renew")?>" />&#160;&#160;&#160;&#160;
       <input type="submit" name="revoke" value="<?php echo _("Revoke/Delete")?>" /></td>

    <td class="DataTD" colspan="4"><input type="submit" name="change" value="<?php echo _("Change settings")?>" /> </td>

  </tr>
<?php } ?>
</table>
<input type="hidden" name="oldid" value="<?php echo intval($id)?>" />
<input type="hidden" name="csrf" value="<?php echo make_csrf('clicerchange')?>" />
</form>
<p><?php echo _("From here you can delete pending requests, or revoke valid certificates.")?></p>
<p><?php echo _("Login").": "._("By allowing certificate login, this certificate can be used to login into your account at https://secure.cacert.org/ .")?></p>
