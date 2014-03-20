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
?>
<h3><?=_("CAcert Certificate Acceptable Use Policy")?></h3>
<p><?=_("Once you decide to subscribe for an SSL Server Certificate you will need to complete this agreement. Please read it carefully. Your Certificate Request can only be processed with your acceptance and understanding of this agreement.")?></p>

<p><?=_("I hereby represent that I am fully authorized by the owner of the information contained in the CSR sent to CAcert Inc. to apply for an Digital Certificate for secure and authenticated electronic transactions. I understand that a digital certificate serves to identify the Subscriber for the purposes of electronic communication and that the management of the private keys associated with such certificates is the responsibility of the subscriber's technical staff and/or contractors.")?></p>

<p><?=_("CAcert Inc.'s public certification services are governed by a CPS as amended from time to time which is incorporated into this Agreement by reference. The Subscriber will use the SSL Server Certificate in accordance with CAcert Inc.'s CPS and supporting documentation published at")?> <a href="http://www.cacert.org/cps.php">http://www.cacert.org/cps.php</a></p>

<p><?=_("If the Subscriber's name and/or domain name registration change the subscriber will immediately inform CAcert Inc. who shall revoke the digital certificate. When the Digital Certificate expires or is revoked the company will permanently remove the certificate from the server on which it is installed and will not use it for any purpose thereafter. The person responsible for key management and security is fully authorized to install and utilize the certificate to represent this organization's electronic presence.")?></p>

<form method="post" action="account.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("New Client Certificate")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Add")?></td>
    <td class="DataTD"><?=_("Address")?></td>
  </tr>

<?
	$query = "select * from `email` where `memid`='".intval($_SESSION['profile']['id'])."' and `deleted`=0 and `hash`=''";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{ ?>
  <tr>
    <td class="DataTD"><input type="checkbox" id="addid<?=intval($row['id'])?>" name="addid[]" value="<?=intval($row['id'])?>"></td>
    <td class="DataTD" align="left"><label for="addid<?=intval($row['id'])?>"><?=sanitizeHTML($row['email'])?></label></td>
  </tr>
<? }
if($_SESSION['profile']['points'] >= 50)
{
	$fname = $_SESSION['profile']['fname'];
	$mname = $_SESSION['profile']['mname'];
	$lname = $_SESSION['profile']['lname'];
	$suffix = $_SESSION['profile']['suffix'];
?>
  <tr>
    <td class="DataTD" colspan="2" align="left">
      <input type="radio" id="incname0" name="incname" value="0" checked="checked" />
        <label for="incname0"><?=_("No Name")?></label><br />
      <? if($fname && $lname) { ?>
        <input type="radio" id="incname1" name="incname" value="1" />
        <label for="incname1"><?=_("Include")?> '<?=$fname." ".$lname?>'</label><br />
      <? } ?>
      <? if($fname && $mname && $lname) { ?>
        <input type="radio" id="incname2" name="incname" value="2" />
        <label for="incname2"><?=_("Include")?> '<?=$fname." ".$mname." ".$lname?>'</label><br />
      <? } ?>
      <? if($fname && $lname && $suffix) { ?>
        <input type="radio" id="incname3" name="incname" value="3" />
        <label for="incname3"><?=_("Include")?> '<?=$fname." ".$lname." ".$suffix?>'</label><br />
      <? } ?>
      <? if($fname && $mname && $lname && $suffix) { ?>
        <input type="radio" id="incname4" name="incname" value="4" />
        <label for="incname4"><?=_("Include")?> '<?=$fname." ".$mname." ".$lname." ".$suffix?>'</label><br />
      <? } ?>
    </td>
  </tr>
<? } ?>

  <tr>
    <td class="DataTD">
      <input type="checkbox" id="login" name="login" value="1" checked="checked" />
    </td>
    <td class="DataTD" align="left">
      <label for="login"><?=_("Enable certificate login with this certificate")?><br />
      <?=_("By allowing certificate login, this certificate can be used to login into this account at https://secure.cacert.org/ .")?></label>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2" align="left">
      <label for="description"><?=_("Optional comment, only used in the certificate overview")?></label><br />
      <input type="text" id="description" name="description" maxlength="100" size="100" />
    </td>
  </tr>

  <tr name="expertoff" style="display:none">
    <td class="DataTD">
      <input type="checkbox" id="expertbox" name="expertbox" onchange="showExpert(this.checked)" />
    </td>
    <td class="DataTD" align="left">
      <label for="expertbox"><?=_("Show advanced options")?></label>
    </td>
  </tr>

<?
if($_SESSION['profile']['points'] >= 50)
{
?>
  <tr name="expert">
    <td class="DataTD" colspan="2" align="left">
      <input type="radio" id="root1" name="rootcert" value="1" /> <label for="root1"><?=_("Sign by class 1 root certificate")?></label><br />
      <input type="radio" id="root2" name="rootcert" value="2" checked="checked" /> <label for="root2"><?=_("Sign by class 3 root certificate")?></label><br />
      <?=str_replace("\n", "<br />\n", wordwrap(_("Please note: If you use a certificate signed by the class 3 root, the class 3 root certificate needs to be imported into your email program as well as the class 1 root certificate so your email program can build a full trust path chain."), 125))?>
    </td>
  </tr>
<? } ?>

  <tr name="expert">
    <td class="DataTD" colspan="2" align="left">
      <?=_("Hash algorithm used when signing the certificate:")?><br />
      <?
      foreach (HashAlgorithms::getInfo() as $algorithm => $display_info) {
      ?>
        <input type="radio" id="hash_alg_<?=$algorithm?>" name="hash_alg" value="<?=$algorithm?>" <?=(HashAlgorithms::$default === $algorithm)?'checked="checked"':''?> />
        <label for="hash_alg_<?=$algorithm?>"><?=$display_info['name']?><?=$display_info['info']?' - '.$display_info['info']:''?></label><br />
      <?
      }
      ?>
    </td>
  </tr>

<? if($_SESSION['profile']['points'] >= 100 && $_SESSION['profile']['codesign'] > 0) { ?>
  <tr name="expert">
    <td class="DataTD">
      <input type="checkbox" id="codesign" name="codesign" value="1" />
    </td>
    <td class="DataTD" align="left">
      <label for="codesign"><?=_("Code Signing")?><br />
      <?=_("Please note: By ticking this box you will automatically have your name included in the certificate.")?></label>
    </td>
  </tr>
<? } ?>

  <tr name="expert">
    <td class="DataTD">
      <input type="checkbox" id="SSO" name="SSO" value="1" />
    </td>
    <td class="DataTD" align="left">
      <label for="SSO"><?=_("Add Single Sign On ID Information")?><br />
      <?=str_replace("\n", "<br>\n", wordwrap(_("By adding Single Sign On (SSO) ID information to your certificates this could be used to track you, you can also issue certificates with no email addresses that are useful only for Authentication. Please see a more detailed description on our WIKI about it."), 125))?>
      <a href="http://wiki.cacert.org/wiki/SSO"><?=_("SSO WIKI Entry")?></a></label>
    </td>
  </tr>

  <tr name="expert">
    <td class="DataTD" colspan="2">
      <label for="optionalCSR"><?=_("Optional Client CSR, no information on the certificate will be used")?></label><br />
      <textarea id="optionalCSR" name="optionalCSR" cols="80" rows="5"></textarea>
    </td>
  </tr>


  <tr>
    <td class="DataTD">
      <input type="checkbox" id="CCA" name="CCA" />
    </td>
    <td class="DataTD" align="left">
      <label for="CCA"><strong><?=sprintf(_("I accept the CAcert Community Agreement (%s)."),"<a href='/policy/CAcertCommunityAgreement.html'>CCA</a>")?></strong><br />
      <?=_("Please note: You need to accept the CCA to proceed.")?></label>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>" /></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="<?=$id?>" />
</form>

<script language="javascript">
function showExpert(a)
{
  b=document.getElementsByName("expert");
  for(i=0;b.length>i;i++)
  {
    if(!a) {b[i].setAttribute("style","display:none"); }
    else {b[i].removeAttribute("style");}
  }
  b=document.getElementsByName("expertoff");
  for(i=0;b.length>i;i++)
  {
    b[i].removeAttribute("style");
  }

}
showExpert(false);
</script>
