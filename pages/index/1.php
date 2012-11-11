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
<p><?=_("By joining CAcert and becoming a Member, you agree to the CAcert Community Agreement. Please take a moment now to read that and agree to it; this will be required to complete the process of joining.")?></p>
<p><?=_("Warning! This site requires cookies to be enabled to ensure your privacy and security. This site uses session cookies to store temporary values to prevent people from copying and pasting the session ID to someone else exposing their account, personal details and identity theft as a result.")?></p>
<p style="border:dotted 1px #900;padding:0.3em;background-color:#ffe;">
<?=_("A proper password wouldn't match your name or email at all, it contains at least 1 lower case letter, 1 upper case letter, a number, white space and a misc symbol. You get additional security for being over 15 characters and a second additional point for having it over 30. The system starts reducing security if you include any section of your name, or password or email address or if it matches a word from the english dictionary...")?><br><br>
<b><?=_("Note: White spaces at the beginning and end of a password will be removed.")?></b>
</p>

<form method="post" action="index.php" autocomplete="off">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="400">
  <tr>
    <td colspan="3" class="title"><?=_("My Details")?></td>
  </tr>

  <tr>
    <td class="DataTD" width="125"><?=_("First Name")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="fname" value="<?=array_key_exists('fname',$_REQUEST)?sanitizeHTML($_REQUEST['fname']):""?>" autocomplete="off"></td>
    <td rowspan="4" class="DataTD" width="125"><? printf(_("Help on Names %sin the wiki%s"),'<a href="//wiki.cacert.org/FAQ/HowToEnterNamesInJoinForm" target="_blank">','</a>')?></td>
  </tr>

  <tr>
    <td class="DataTD" valign="top"><?=_("Middle Name(s)")?><br>
      (<?=_("optional")?>)
    </td>
    <td class="DataTD"><input type="text" name="mname" value="<?=array_key_exists('mname',$_REQUEST)?sanitizeHTML($_REQUEST['mname']):""?>" autocomplete="off"></td>
  </tr>

  <tr>
    <td class="DataTD"><?=_("Last Name")?>: </td>
    <td class="DataTD"><input type="text" name="lname" value="<?=array_key_exists('lname',$_REQUEST)?sanitizeHTML($_REQUEST['lname']):""?>" autocomplete="off"></td>
  </tr>

  <tr>
    <td class="DataTD"><?=_("Suffix")?><br>
      (<?=_("optional")?>)</td>
    <td class="DataTD"><input type="text" name="suffix" value="<?=array_key_exists('suffix',$_REQUEST)?sanitizeHTML($_REQUEST['suffix']):""?>" autocomplete="off"><br><?=sprintf(_("Please only write Name Suffixes into this field."))?></td>
  </tr>

  <tr>
    <td class="DataTD"><?=_("Date of Birth")?><br>
	    (<?=_("dd/mm/yyyy")?>)</td>
    <td class="DataTD"><nobr><select name="day">
<?
	for($i = 1; $i <= 31; $i++)
	{
		echo "<option";
		if(array_key_exists('day',$_SESSION['signup']) && $_SESSION['signup']['day'] == $i)
			echo " selected=\"selected\"";
		echo ">$i</option>";
	}
?>
    </select>
    <select name="month">
<?
	for($i = 1; $i <= 12; $i++)
	{
		echo "<option value='$i'";
		if(array_key_exists('month',$_SESSION['signup']) && $_SESSION['signup']['month'] == $i)
			echo " selected=\"selected\"";
		echo ">".ucwords(strftime("%B", mktime(0,0,0,$i,1,date("Y"))))." ($i)</option>\n";
	}
?>
    </select>
    <input type="text" name="year" value="<?=array_key_exists('year',$_SESSION['signup']) ? sanitizeHTML($_SESSION['signup']['year']):""?>" size="4" autocomplete="off"></nobr>
    </td>
    <td class="DataTD">&nbsp;</td>
  </tr>

  <tr>
    <td class="DataTD"><?=_("Email Address")?>: </td>
    <td class="DataTD"><input type="text" name="email" value="<?=array_key_exists('email',$_REQUEST)?sanitizeHTML($_REQUEST['email']):""?>" autocomplete="off"></td>
    <td class="DataTD"><?=_("I own or am authorised to control this email address")?></td>
  </tr>

  <tr>
    <td class="DataTD"><?=_("Pass Phrase")?><font color="red">*</font>: </td>
    <td class="DataTD"><input type="password" name="pword1" autocomplete="off"></td>
    <td class="DataTD" rowspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Pass Phrase Again")?><font color="red">*</font>: </td>
    <td class="DataTD"><input type="password" name="pword2" autocomplete="off"></td>
  </tr>

  <tr>
    <td class="DataTD" colspan="3"><font color="red">*</font><?=_("Please note, in the interests of good security, the pass phrase must be made up of an upper case letter, lower case letter, number and symbol.")?></td>
  </tr>

  <tr>
    <td class="DataTD" colspan="3"><?=_("Lost Pass Phrase Questions - Please enter five questions and your responses to be used for security verification. The questions and answers must be unambiguous and easy to remember forever, that means permanent issues that never change, not current issues, even more no random strings.")?></td>
  </tr>

  <tr>
    <td class="DataTD">1)&nbsp;<input type="text" name="Q1" size="15" value="<?=array_key_exists('Q1',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['Q1']):""?>"></td>
    <td class="DataTD"><input type="text" name="A1" value="<?=array_key_exists('A1',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['A1']):""?>" autocomplete="off"></td>
    <td class="DataTD" rowspan="5">&nbsp;</td>
  </tr>

  <tr>
    <td class="DataTD">2)&nbsp;<input type="text" name="Q2" size="15" value="<?=array_key_exists('Q2',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['Q2']):""?>"></td>
    <td class="DataTD"><input type="text" name="A2" value="<?=array_key_exists('A2',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['A2']):""?>" autocomplete="off"></td>
  </tr>

  <tr>
    <td class="DataTD">3)&nbsp;<input type="text" name="Q3" size="15" value="<?=array_key_exists('Q3',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['Q3']):""?>"></td>
    <td class="DataTD"><input type="text" name="A3" value="<?=array_key_exists('A3',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['A3']):""?>" autocomplete="off"></td>
  </tr>

  <tr>
    <td class="DataTD">4)&nbsp;<input type="text" name="Q4" size="15" value="<?=array_key_exists('Q4',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['Q4']):""?>"></td>
    <td class="DataTD"><input type="text" name="A4" value="<?=array_key_exists('A4',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['A4']):""?>" autcomplete="off"></td>
  </tr>

  <tr>
  <td class="DataTD">5)&nbsp;<input type="text" name="Q5" size="15" value="<?=array_key_exists('Q5',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['Q5']):""?>"></td>
    <td class="DataTD"><input type="text" name="A5" value="<?=array_key_exists('A5',$_SESSION['signup'])?sanitizeHTML($_SESSION['signup']['A5']):""?>" autocomplete="off"></td>
  </tr>

  <tr>
    <td class="DataTD" colspan="3"><?=_("It's possible to get notifications of up and coming events and even just general announcements, untick any notifications you don't wish to receive. For country, regional and radius notifications to work you must choose your location once you've verified your account and logged in.")?></td>
  </tr>

  <tr>
    <td class="DataTD" valign="top"><?=_("Alert me if")?>: </td>
    <td class="DataTD" align="left">
        <input type="checkbox" name="general" value="1" <?=array_key_exists('general',$_SESSION['signup'])? ($_SESSION['signup']['general'] == "0" ?"":"checked=\"checked\""):"checked=\"checked\"" ?>><?=_("General Announcements")?><br>
	<input type="checkbox" name="country" value="1" <?=array_key_exists('country',$_SESSION['signup'])? ($_SESSION['signup']['country'] == "0" ?"":"checked=\"checked\""):"checked=\"checked\"" ?>><?=_("Country Announcements")?><br>
	<input type="checkbox" name="regional" value="1" <?=array_key_exists('regional',$_SESSION['signup'])? ($_SESSION['signup']['regional'] == "0" ?"":"checked=\"checked\""):"checked=\"checked\"" ?>><?=_("Regional Announcements")?><br>
	<input type="checkbox" name="radius" value="1" <?=array_key_exists('radius',$_SESSION['signup'])? ($_SESSION['signup']['radius'] == "0" ?"":"checked=\"checked\""):"checked=\"checked\"" ?>><?=_("Within 200km Announcements")?></td>
    <td class="DataTD">&nbsp;</td>
  </tr>

  <tr>
    <td class="DataTD" colspan="3"><?=_("When you click on next, we will send a confirmation email to the email address you have entered above.")?></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="3"><input type="checkbox" name="cca_agree" value="1" <?=array_key_exists('cca_agree',$_SESSION['signup'])? ($_SESSION['signup']['cca_agree'] == "1" ?"checked=\"checked\"":""):"" ?> ><?=_("I agree to the terms and conditions of the CAcert Community Agreement")?>: <a href="/policy/CAcertCommunityAgreement.php">http://www.cacert.org/policy/CAcertCommunityAgreement.php</a></td>
  </tr>

  <tr>
    <td class="DataTD" colspan="3"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>

</table>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
