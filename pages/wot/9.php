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

	require_once($_SESSION['_config']['filepath'].'/includes/lib/l10n.php');


	$res = mysql_query("select * from `users` where `id`='".intval($_REQUEST['userid'])."' and `listme`='1'");
	if(mysql_num_rows($res) <= 0)
	{
		echo _("Sorry, I was unable to locate that user, the person doesn't wish to be contacted, or isn't an assurer.");
	} else {

		$user = mysql_fetch_array($res);
		$userlang = $user['language'];
		$points = mysql_num_rows(mysql_query("select sum(`points`) as `total` from `notary`
				where `to`='".intval($user['id'])."' and `deleted`=0 group by `to` HAVING SUM(`points`) > 0"));
		if($points <= 0) {
			echo _("Sorry, I was unable to locate that user.");
		} else {

			$_SESSION['_config']['pagehash'] = md5(date("U"));
?>
<? if($_SESSION['_config']['error'] != "") { ?><font color="#ff0000" size="+1">ERROR: <?=$_SESSION['_config']['error']?></font><? unset($_SESSION['_config']['error']); } ?>
<form method="post" action="wot.php">
<input type="hidden" name="userid" value="<?=intval($user['id'])?>">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Contact Assurer")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("To")?>:</td>
    <td class="DataTD" align="left"><?=sanitizeHTML(trim($user['fname'].' '.substr($user['lname'], 0, 1)))?></td>
  </tr>
<? if($userlang != "") { ?>
  <tr>
    <td class="DataTD"><?=_("Language")?>:</td>
    <td class="DataTD" align="left"><? printf(_("%s prefers to be contacted in %s"), sanitizeHTML($user['fname']), L10n::$translations[$userlang]) ?></td>
  </tr>
<? } ?>
<?
	$query = "select * from `addlang` where `userid`='".intval($user['id'])."'";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$lang = mysql_fetch_assoc(mysql_query("select * from `languages` where `locale`='".mysql_real_escape_string($row['lang'])."'"));
?>
  <tr>
    <td class="DataTD"><?=_("Additional Language")?>:</td>
    <td class="DataTD" align="left"><? printf(_("%s will also accept email in %s - %s"), sanitizeHTML($user['fname']), sanitizeHTML($lang['lang']), sanitizeHTML($lang['country'])) ?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><?=_("Subject")?>:</td>
    <td class="DataTD" align="left"><input type="text" name="subject" value="<?=sanitizeHTML($_POST['subject'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Message")?>:</td>
    <td class="DataTD"><textarea name="message" cols="40" rows="5" wrap="virtual"><?=sanitizeHTML($_POST['message'])?></textarea></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Send")?>"></td>
  </tr>
</table>
<input type="hidden" name="pageid" value="<?=$_SESSION['_config']['pagehash']?>">
<input type="hidden" name="userid" value="<?=intval($_REQUEST['userid'])?>">
<input type="hidden" name="oldid" value="<?=intval($id)?>">
</form>
<p>[ <a href='javascript:history.go(-1)'><?=_("Go Back")?></a> ]</p>
<? } } ?>
