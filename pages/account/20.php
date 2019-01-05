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
*/
	include_once("../includes/shutdown.php");
?>
<h3><?php echo _("CAcert Certificate Acceptable Use Policy")?></h3>
<p><?php echo _("Once you decide to subscribe for an SSL Server Certificate you will need to complete this agreement. Please read it carefully. Your Certificate Request can only be processed with your acceptance and understanding of this agreement.")?></p>

<p><?php echo _("I hereby represent that I am fully authorized by the owner of the information contained in the CSR sent to CAcert Inc. to apply for an Digital Certificate for secure and authenticated electronic transactions. I understand that a digital certificate serves to identify the Subscriber for the purposes of electronic communication and that the management of the private keys associated with such certificates is the responsibility of the subscriber's technical staff and/or contractors.")?></p>

<p><?php echo _("CAcert Inc.'s public certification services are governed by a CPS as amended from time to time which is incorporated into this Agreement by reference. The Subscriber will use the SSL Server Certificate in accordance with CAcert Inc.'s CPS and supporting documentation published at")?> <a href="http://www.cacert.org/policy/">http://www.cacert.org/policy/</a></p>

<p><?php echo _("If the Subscriber's name and/or domain name registration change the subscriber will immediately inform CAcert Inc. who shall revoke the digital certificate. When the Digital Certificate expires or is revoked the company will permanently remove the certificate from the server on which it is installed and will not use it for any purpose thereafter. The person responsible for key management and security is fully authorized to install and utilize the certificate to represent this organization's electronic presence.")?></p>

<form method="post" action="account.php">
<p><label for="description"><?php echo _("Optional comment, only used in the certificate overview")?></label><br />
	<input type="text" id="description" name="description" maxlength="80" size="80" />
</p>
<p><label for="CSR"><?php echo _("Paste your CSR (Certificate Signing Request) below...")?></label><br />
	<textarea id="CSR" name="CSR" cols="80" rows="15"></textarea>
</p>

<fieldset>
<legend>
	<input type="checkbox" id="expertbox" onchange="showExpert(this.checked)" style="display:none" />
	<label for="expertbox"><?php echo _("Advanced Options")?></label>
</legend>
<div id="advanced_options">
<ul class="no_indent">
	<li>
		<input type="radio" id="root1" name="rootcert" value="1" />
		<label for="root1"><?php echo _("Sign by class 1 root certificate")?></label>
	</li>
	<li>
		<input type="radio" id="root2" name="rootcert" value="2" checked="checked" />
		<label for="root2"><?php echo _("Sign by class 3 root certificate")?></label>
	</li>
</ul>
<p><?php echo _("Please note: The class 3 root certificate needs to be setup in your webserver as a chained certificate, while slightly more complicated to setup, this root certificate is more likely to be trusted by more people.")?></p>

<p class="attach_ul"><?php echo _("Hash algorithm used when signing the certificate:")?></p>
<ul class="no_indent">
<?php foreach (HashAlgorithms::getInfo() as $algorithm => $display_info) {
?>
	<li>
		<input type="radio" id="hash_alg_<?php echo $algorithm?>" name="hash_alg" value="<?php echo $algorithm?>" <?php echo (HashAlgorithms::$default === $algorithm)?'checked="checked"':''?> />
		<label for="hash_alg_<?php echo $algorithm?>"><?php echo $display_info['name']?><?php echo $display_info['info']?' - '.$display_info['info']:''?></label>
	</li>
<?php }
?>
</ul>

</div>
</fieldset>

<input type="submit" name="process" value="<?php echo _("Submit")?>" />
<input type="hidden" name="oldid" value="<?php echo $id?>" />
</form>

<script language="javascript">
function showExpert(a)
{
	var options=document.getElementById("advanced_options");
	options.style.display = (a) ? "" : "none";

	var checkbox=document.getElementById("expertbox");
	checkbox.style.display = "";
}
showExpert(false);
</script>
