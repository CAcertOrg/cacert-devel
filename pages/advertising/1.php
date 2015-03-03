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
<? global $errmsg, $link, $title, $months; if($errmsg != "") { ?><p class="error_fatal"><?=$errmsg?></p><? } ?>
<form method="post" action="advertising.php" ACCEPTCHARSET="utf-8">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="6" class="title"><?=_("New Advertisement")?></td>
  </tr>
  <tr><td class='DataTD'>Link Title:</td><td class='DataTD'><input type="text" name="title" value="<?=$title?>"></td></tr>
  <tr><td class='DataTD'>URL:</td><td class='DataTD'><input type="text" name="link" value="<?=$link?>"></td></tr>
  <tr><td class='DataTD'>Months:</td><td class='DataTD'><select name="months"><?
	for($i = 1; $i <= 12; $i++)
	{
		echo "<option value='$i'";
		if($months == $i)
			echo " selected";
		echo ">$i Months</option>";
	}
	?></td></tr>
  <tr><td class='DataTD' colspan='2'><input type="submit" name="process" value="Submit New Advertisment"></tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
