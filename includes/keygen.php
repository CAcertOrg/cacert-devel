<?php /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2011  CAcert Inc.

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

if (array_key_exists('HTTP_USER_AGENT',$_SERVER) && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) { ?>

	<noscript>
		<p><?php echo _('You have to enable JavaScript to generate certificates in the browser.')?></p>
		<p><?php echo _('If you don\'t want to do that for any reason, you can use '.
			'manually created certificate requests instead.')?></p>
	</noscript>

	<div id="noActiveX" style="color:red">
		<p><?php echo _('Could not initialize ActiveX object required for certificate generation.')?></p>
		<p><?php echo _('You have to enable ActiveX for this to work. On Windows Vista, Windows 7 and '.
			'later versions you have to add this website to the list of trusted sites '.
			'in the internet settings.')?></p>
		<p><?php
			printf(_('Go to "Extras -> Internet Options -> Security -> Trusted '.
				'Websites", click on "Custom Level", set "ActiveX control '.
				'elements that are not marked as safe initialized on start in '.
				'scripts" to "Confirm" and click "OK". Now click "Sites", add '.
				'"%s" and "%s" to your list of trusted sites and make the '.
				'changes come into effect by clicking "Close" and "OK".'),
				'https://'.$_SESSION['_config']['normalhostname'],
				'https://'.$_SESSION['_config']['securehostname'])?>
		</p>
	</div>

	<form method="post" style="display:none" action="account.php"
			id="CertReqForm">
		<input type="hidden" name="oldid" value="<?php echo intval($id)?>" />
		<input type="hidden" id="CSR" name="CSR" />
		<input type="hidden" name="keytype" value="MS" />

		<p><?php echo _('Security level')?>:
			<select id="SecurityLevel">
				<option value="high" selected="selected"><?php echo _('High')?></option>
				<option value="medium"><?php echo _('Medium')?></option>
				<option value="custom"><?php echo _('Custom')?>&hellip;</option>
			</select>
		</p>

		<fieldset id="customSettings" style="display:none">
			<legend><?php echo _('Custom Parameters')?></legend>

			<p><?php echo _('Cryptography Provider')?>:
				<select id="CspProvider"></select>
			</p>
			<p><?php echo _('Algorithm')?>: <select id="algorithm"></select></p>
			<p><?php echo _('Keysize')?>:
				<input id="keySize" type="number" />
				<?php echo _('Minimum Size')?>: <span id="keySizeMin"></span>,
				<?php echo _('Maximum Size')?>: <span id="keySizeMax"></span>,
				<?php
				// TRANSLATORS: this specifies the step between two valid key
				// sizes. E.g. if the step is 512 and the minimum is 1024 and
				// the maximum is 2048, then only 1024, 1536 and 2048 bits may
				// be specified as key size.
				echo _('Step')?>: <span id="keySizeStep"></span></p>
			<p style="color:red"><?php
				printf(_('Please note that RSA key sizes smaller than %d bit '.
					'will not be accepted by CAcert.'),
					2048)?>
			</p>
		</fieldset>

		<p><input type="submit" id="GenReq" name="GenReq" value="<?php echo _('Create Certificate')?>" /></p>
		<p id="generatingKeyNotice" style="display:none">
			<?php echo _('Generating your key. Please wait')?>&hellip;</p>
	</form>

	<!-- Error messages used in the JavaScript. Defined here so they can be
	translated without passing the JavaScript code through PHP -->
	<p id="createRequestErrorChooseAlgorithm" style="display:none">
		<?php echo _('Could not generate certificate request. Probably you need to '.
			'choose a different algorithm.')?>
	</p>
	<p id="createRequestErrorConfirmDialogue" style="display:none">
		<?php echo _('Could not generate certificate request. Please confirm the '.
			'dialogue if you are asked if you want to generate the key.')?>
	</p>
	<p id="createRequestErrorConnectDevice" style="display:none">
		<?php echo _('Could not generate certificate request. Please make sure the '.
			'cryptography device (e.g. the smartcard) is connected.')?>
	</p>
	<p id="createRequestError" style="display:none">
		<?php echo _('Could not generate certificate request.')?>
	</p>
	<p id="invalidKeySizeError" style="display:none">
		<?php echo _('You have specified an invalid key size')?>
	</p>
	<p id="unsupportedPlatformError" style="display:none">
		<?php echo _('Could not initialize the cryptographic module for your '.
			'platform. Currently we support Microsoft Windows XP, Vista '.
			'and 7. If you\'re using one of these platforms and see this '.
			'error message anyway you might have to enable ActiveX as '.
			'described in the red explanation text and accept loading of '.
			'the module.')?>
	</p>

	<script type="text/javascript" src="keygenIE.js"></script>

<?php } else { ?>
	<p>
		<form method="post" action="account.php">
			<input type="hidden" name="keytype" value="NS">
			<?php echo _("Keysize:")?> <keygen name="SPKAC" challenge="<?php $_SESSION['spkac_hash']=make_hash(); echo $_SESSION['spkac_hash']; ?>">

			<input type="submit" name="submit" value="<?php echo _("Generate key pair within browser")?>">
			<input type="hidden" name="oldid" value="<?php echo intval($id)?>">
		</form>
	</p>
<?php }
