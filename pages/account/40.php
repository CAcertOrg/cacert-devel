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
if(!array_key_exists('secrethash',$_SESSION['_config'])) $_SESSION['_config']['secrethash'] = md5(date("YmdHis").rand(0, intval(date("u"))));
?>
<H3><?=_("Contact Us")?></H3>

<p><b><?=_("General Questions")?></b></p>
<p><b><?=_("PLEASE NOTE: Due to the large amounts of support questions, incorrectly directed emails may be over looked, this is a volunteer effort and directing general questions to the right place will help everyone, including yourself as you will get a reply quicker.")?></b></p>
<p><b><?=_("If you are contacting us about advertising, please use the form at the bottom of the website, the first contact form is not the correct place.")?></b></p>
<p><?=sprintf(_("If you are having trouble with your username or password, please visit our %swiki page%s for more information"), "<a href='http://wiki.cacert.org/wiki/FAQ/LostPasswordOrAccount' target='_new'>", "</a>");?></p>
<p><?=_("Before contacting us, be sure to read the information on our official and unofficial HowTo and FAQ pages.")?> - <a href="//wiki.cacert.org/HELP/"><?=_("Go here for more details.")?></a></p>
<p><?=_("General questions about CAcert should be sent to the general support list, please send all emails in ENGLISH only, this list has many more volunteers then those directly involved with the running of the website, everyone on the mailing list understands english, even if this isn't their native language this will increase your chance at a competent reply. While it's best if you sign up to the mailing list to get replied to, you don't have to, but please make sure you note this in your email, otherwise it might seem like you didn't get a reply to your question.")?></p>
<p><a href="https://lists.cacert.org/wws/info/cacert-support"><?=_("Click here to go to the Support List")?></a></p>
<p><?=_("You can alternatively use the form below, however joining the list is the prefered option to support your queries")?></p>
<form method="post" action="account.php" name="form1">
  <input type="hidden" name="oldid" value="<?=$id?>">
<!--   <input type="hidden" name="support" value="yes"> --> 
  <input type="hidden" name="secrethash2" value="">
  <p class="robotic" id="pot">
    <label>If you're human leave this blank:</label>
    <input name="robotest" type="text" id="robotest" class="robotest" />
  </p>
<table border="0">
    <tr><td width="100"><?=_("Your Name")?>:</td><td colspan="3 width="300"><input type="text" name="who"></td>
    <tr><td width="100"><?=_("Your Email")?>:</td><td colspan="3"><input type="text" name="email"></td>
    <tr><td width="100"><?=_("Subject")?>:</td><td colspan="3"><input type="text" name="subject"></td></tr>
    <tr><td width="100" valign="top"><?=_("Message")?>:</td><td colspan="3"><textarea name="message" cols="70" rows="10"></textarea></td></tr>

    <tr>
      <td colspan="2"><font color="#ff0000"><?=_("Warning: Please do not use send to mailing list when you entered confidential data. The request is being sent to a public mailinglist.")?></font></td>
      <td colspan="2"><?=_("For confidential data use send to support.")?></font></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" name="process[0]" value="<?=_("Send to mailing list")?>"></td>
      <td colspan="2"><input type="submit" name="process[1]" value="<?=_("Send to support")?>"></td>
    </tr>
  </table>
</form>

<p><b>IRC</b></p>
<p><a href="irc://irc.CAcert.org/CAcert">irc://irc.CAcert.org/CAcert</a></p>
<p><b>Secure IRC</b></p>
<p><a href="ircs://irc.CAcert.org:7000/CAcert">ircs://irc.CAcert.org:7000/CAcert</a></p>

<p><b><?=_("Other Mailing Lists")?></b></p>
<p><?=_("There are a number of other mailing lists CAcert runs, some are general discussion, others are technical (such as the development list) or platform specific help (such as the list for Apple Mac users)")?></p>
<p><a href="http://lists.cacert.org/"><?=_("Click here to view all lists available")?></a></p>

<p><b><?=_("Security Issues")?></b></p>
<p><?=sprintf(_("Please use any of the following ways to report security ".
	"issues: You can use the above contact form for sensitive information. ".
	"You can email us to %s. You can file a bugreport on %s and mark it as ".
	"private."),
	"<a href='mailto:support@cacert.org'>support@cacert.org</a>",
	"<a href='https://bugs.cacert.org/'>bugs.cacert.org</a>")?></p>



<script type="text/javascript">
<!--
	var pagehash = '<?=$_SESSION['_config']['secrethash']?>';

	document.form1.secrethash2.value = pagehash;
	document.form2.secrethash2.value = pagehash;
-->
</script>
