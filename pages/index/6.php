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
<p style="border:dotted 1px #900;padding:0.3em;background-color:#ffe;">
<?=_("A proper password wouldn't match your name or email at all, it contains at least 1 lower case letter, 1 upper case letter, a number, white space and a misc symbol. You get additional security for being over 15 characters and a second additional point for having it over 30. The system starts reducing security if you include any section of your name, or password or email address or if it matches a word from the english dictionary...")?>
</p>

<form method="post" action="index.php" autocomplete="off">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="350">
  <tr>
    <td colspan="2" class="title"><?=_("Lost Pass Phrase - Step 2")?></td>
  </tr>
<?
	srand ((double) microtime() * 1000000);
	$num2 = $nums = array();
	for($i = 1; $i <= 5; $i++)
	{
		if($_SESSION['lostpw']['user']["Q$i"] == "")
			continue;
		$nums[] = $i;
	}

	for($i = 0; $i < count($nums); $i++)
	{
		if(count($num2) == count($nums))
			break;

		$val = rand(1, 5);
		if($_SESSION['lostpw']['user']["Q$val"] == "")
		{
			$i--;
			continue;
		}

		if($val < 1 || $val > 5)
		{
			$i--;
			continue;
		}

		if(!in_array($val, $num2))
			$num2[] = $val;
		else
			$i--;

		if(count($num2) >= 3)
			break;
	}

	if($i > 1)
	{

	$_SESSION['lostpw']['total'] = count($num2);

	foreach($num2 as $num)
	{
		$q = "Q$num"; $a = "A$num";
		if($_SESSION['lostpw']['user'][$q] == "")
			continue;
?>
  <tr>
    <td class="DataTD"><?=$_SESSION['lostpw']['user'][$q]?></td>
    <td class="DataTD"><input type="text" name="<?=$a?>" autocomplete="off">
	<input type="hidden" name="<?=$q?>" value="<?=sanitizeHTML($_SESSION['lostpw']['user'][$q])?>"></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD"><?=_("New Pass Phrase")?><font color="red">*</font>: </td>
    <td class="DataTD"><input type="password" name="newpass1" autocomplete="off"></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Repeat")?><font color="red">*</font>: </td>
    <td class="DataTD"><input type="password" name="newpass2" autocomplete="off"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><font color="red">*</font><?=_("Please note, in the interests of good security, the pass phrase must be made up of an upper case letter, lower case letter, number and symbol.")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>     
<input type="hidden" name="oldid" value="<?=$id?>">
</form> 
<? } else { ?>
<p><?=_("You do not have enough/any lost password questions set. You will not be able to continue to reset your password via this method.")?></p>
<? } ?>
