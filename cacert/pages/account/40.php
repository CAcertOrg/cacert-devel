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
<p><?=_("Before contacting us, be sure to read the information on our official and unofficial HowTo and FAQ pages.")?> - <a href="http://www.CAcert.org/help.php"><?=_("Go here for more details.")?></a></p>
<p><?=_("General questions about CAcert should be sent to the general support list, please send all emails in ENGLISH only, this list has many more volunteers then those directly involved with the running of the website, everyone on the mailing list understands english, even if this isn't their native language this will increase your chance at a competent reply. While it's best if you sign up to the mailing list to get replied to, you don't have to, but please make sure you note this in your email, otherwise it might seem like you didn't get a reply to your question.")?></p>
<p><a href="https://lists.cacert.org/wws/info/cacert-support"><?=_("Click here to go to the Support List")?></a></p>
<p><?=_("You can alternatively use the form below, however joining the list is the prefered option to support your queries")?></p>
<form method="post" name="form1">
  <input type="hidden" name="oldid" value="<?=$id?>">
  <input type="hidden" name="support" value="yes">
  <input type="hidden" name="secrethash2" value="">
  <table border="0">
    <tr><td width="90"><?=_("Your Name")?>:</td><td><input type="text" name="who"></td><td>&#160;</td></tr>
    <tr><td><?=_("Your Email")?>:</td><td><input type="text" name="email"></td></tr>
    <tr><td><?=_("Subject")?>:</td><td><input type="text" name="subject"></td></tr>
    <tr><td colspan="2"><textarea name="message" cols="40" rows="10"></textarea></td></tr>
    <tr><td colspan="3"><font color="#ff0000"><?=_("Warning: Please do not enter confidential data into this form, it is being sent to a public mailinglist. Use the form further below instead.")?></font></td></tr>
    <tr><td colspan="2"><input type="submit" name="process" value="<?=_("Send")?>"></td></tr>
  </table>
</form>

<p><b>IRC</b></p>
<p><a href="irc://irc.CAcert.org/CAcert">irc://irc.CAcert.org/CAcert</a></p>
<p><b>Secure IRC</b></p>
<p><a href="ircs://irc.CAcert.org:7000/CAcert">ircs://irc.CAcert.org:7000/CAcert</a></p>

<p><b><?=_("Other Mailing Lists")?></b></p>
<p><?=_("There are a number of other mailing lists CAcert runs, some are general discussion, others are technical (such as the development list) or platform specific help (such as the list for Apple Mac users)")?></p>
<p><a href="http://lists.cacert.org/"><?=_("Click here to view all lists available")?></a></p>

<p><b><?=_("Sensitive Information")?></b></p>
<p><?=_("If you have questions, comments or otherwise and information you're sending to us contains sensitive details, you should use the contact form below. Due to the large amounts of support emails we receive, sending general questions via this contact form will generally take longer then using the support mailing list. Also sending queries in anything but english could cause delays in supporting you as we'd need to find a translator to help.")?></p>
<form method="post" action="https://www.cacert.org/index.php" name="form2">
  <input type="hidden" name="secrethash2" value="">
  <input type="hidden" name="oldid" value="<?=$id?>">
  <table border="0">
    <tr><td><?=_("Your Name")?>:</td><td><input type="text" name="who"></td></tr>
    <tr><td><?=_("Your Email")?>:</td><td><input type="text" name="email"></td></tr>
    <tr><td><?=_("Subject")?>:</td><td><input type="text" name="subject"></td></tr>
    <tr><td colspan="2"><textarea name="message" cols="40" rows="10"></textarea></td></tr>
    <tr><td colspan="2"><input type="submit" name="process" value="<?=_("Send")?>"></td></tr>
  </table>
</form>

<p><b><?=_("Security Issues")?></b></p>
<p><?=_("Please use any of the following ways to report security issues: You can use the above contact form for sensitive information. You can email us to support@cacert.org. You can file a bugreport on <a href='https://bugs.cacert.org/'>bugs.cacert.org</a> and mark it as private.")?></p>

<p><b><?=_("Snail Mail")?></b></p>
<p><?=_("Alternatively you can get in contact with us via the following methods:")?></p>

<p><?=_("Postal Address:")?><br>
CAcert Inc.<br>
P.O. Box 4107<br>
Denistone East NSW 2112<br>
Australia</p>

<script type="text/javascript">
<!--
	var pagehash = '<?=$_SESSION['_config']['secrethash']?>';

	document.form1.secrethash2.value = pagehash;
	document.form2.secrethash2.value = pagehash;
-->
</script>
