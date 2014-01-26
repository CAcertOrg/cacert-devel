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
	include_once("../includes/shutdown.php");
	require_once("../includes/lib/l10n.php");
?>
<?
  if(array_key_exists('error',$_SESSION['_config']) && $_SESSION['_config']['error'] != "")
  {
    ?><font color="orange" size="+1">
      <? echo _("ERROR").": ".$_SESSION['_config']['error'] ?>
    </font>
    <?unset($_SESSION['_config']['error']);
  }

  if (!isset($_SESSION['assuresomeone']['year'])) {
      $_SESSION['assuresomeone']['year'] = '';
  }
  if (!isset($_SESSION['assuresomeone']['month'])) {
      $_SESSION['assuresomeone']['month'] = '';
  }
  if (!isset($_SESSION['assuresomeone']['day'])) {
      $_SESSION['assuresomeone']['day'] = '';
  }
?>
<? if(array_key_exists('noemailfound',$_SESSION['_config']) && $_SESSION['_config']['noemailfound'] == 1) { ?>
<form method="post" action="wot.php">
<input type="hidden" name="email" value="<?=sanitizeHTML($_POST['email'])?>"><br>
<select name="reminder-lang">
<?
	if($_SESSION['_config']['reminder-lang'] == "")
		$_SESSION['_config']['reminder-lang'] = L10n::get_translation();
        foreach(L10n::$translations as $key => $val)
        {
                echo "<option value='$key'";
                if($key == $_SESSION['_config']['reminder-lang'])
                        echo " selected";
                echo ">$val</option>\n";
        }
?>
        </select><br>
<input type="hidden" name="oldid" value="<?=$id?>">
<input type="submit" name="reminder" value="<?=_("Send reminder notice")?>">
</form>
<? unset($_SESSION['_config']['noemailfound']); } ?>
<form method="post" action="wot.php" name="form1">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Assure Someone")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
<? if(array_key_exists('remindersent',$_SESSION['_config']) && $_SESSION['_config']['remindersent'] == 1) { unset($_SESSION['_config']['remindersent']) ?>
    <td class="DataTD"><input type="text" name="email" id="email" value=""></td>
<? } else { ?>
    <td class="DataTD"><input type="text" name="email" id="email" value="<?=array_key_exists('email',$_POST)?sanitizeHTML($_POST['email']):""?>"></td>
<? } ?>
  </tr>
    <tr>
    <td class="DataTD">
        <?=_("Date of Birth")?><br/>
        (<?=_("yyyy/mm/dd")?>)</td>
    <td class="DataTD">
        <input type="text" name="year" value="<?=array_key_exists('year',$_SESSION['assuresomeone']) ? sanitizeHTML($_SESSION['assuresomeone']['year']):""?>" size="4" autocomplete="off"></nobr>
        <select name="month">
<?
for($i = 1; $i <= 12; $i++)
{
    echo "<option value='$i'";
    if(array_key_exists('month',$_SESSION['assuresomeone']) && $_SESSION['assuresomeone']['month'] == $i)
        echo " selected=\"selected\"";
    echo ">".ucwords(strftime("%B", mktime(0,0,0,$i,1,date("Y"))))." ($i)</option>\n";
}
?>
        </select>
        <select name="day">
<?
for($i = 1; $i <= 31; $i++)
{
    echo "<option";
    if(array_key_exists('day',$_SESSION['assuresomeone']) && $_SESSION['assuresomeone']['day'] == $i)
        echo " selected=\"selected\"";
    echo ">$i</option>";
}
?>
        </select>
    </td>
  </tr>

  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
<SCRIPT LANGUAGE="JavaScript">
//<![CDATA[
	function my_init()
	{
		document.getElementById("email").focus();
	}

	window.onload = my_init();
//]]>
</script>
