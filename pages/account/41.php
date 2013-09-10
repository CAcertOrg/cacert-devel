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
?>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="2" class="title"><?=_("My Language Settings")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("My preferred language")?>:</td>
    <td class="DataTD"><select name="lang">
<?
	foreach(L10n::$translations as $key => $val)
	{
		echo "<option value='$key'";
		if($key == L10n::get_translation())
			echo " selected";
		echo ">$val</option>\n";
	}
?>
	</select>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Update")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="action" value="default">
<input type="hidden" name="csrf" value="<?=make_csrf('mainlang')?>" />
</form>
<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="2" class="title"><?=_("Additional Language Preferences")?></td>
  </tr>
<?
	$query = "select * from `addlang` where `userid`='".intval($_SESSION['profile']['id'])."'";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$lang = mysql_fetch_assoc(mysql_query("select * from `languages` where `locale`='".mysql_real_escape_string($row['lang'])."'"));
?>
  <tr>
    <td class="DataTD"><?=_("Additional Language")?>:</td>
    <td class="DataTD" align="left"><? echo "${lang['lang']} - ${lang['country']}"; ?>
		<a href="account.php?oldid=41&amp;action=dellang&amp;remove=<?=$row['lang']?>&amp;csrf=<?=make_csrf('seclang')?>"><?=_("Delete")?></a></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><?=_("Secondary languages")?>:</td>
    <td class="DataTD"><select name="addlang">
<?
	$query = "select * from `languages` order by `locale`";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		printf("<option value=\"%s\">[%s] %s (%s)</option>\n",
			sanitizeHTML($row['locale']),
			sanitizeHTML($row['locale']),
			$row['lang'],
			$row['country']
			);
	}
?>
	</select>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Add")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="hidden" name="action" value="addsec">
<input type="hidden" name="csrf" value="<?=make_csrf('seclang')?>" />
</form>
