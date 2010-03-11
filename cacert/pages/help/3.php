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
<h3><?=_("Generating a Key Pair and Certificate Signing Request (CSR) for a Microsoft Internet Information Server (IIS) 5.0.")?></h3>
<p><?=_("To generate a public and private key pair and CSR for a Microsoft IIS 5 Server:")?></p>
  <ol class="tutorial">
   <li><b><?=_("Key generation process")?></b><br />
       <?=_("Under 'Administrative Tools', open the 'Internet Services Manager'. Then open up the properties window for the website you wish to request the certificate for. Right-clicking on the particular website will open up its properties.")?><br />
       <img src="iistutorial/image001.jpg" height="453" width="642" alt="<?=_("Screenshot of IIS 5.0")?>" /><br />
       <img src="iistutorial/image002.jpg" height="453" width="463" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Open Directory Security folder")?></b><br />
       <?=_("In the 'Directory Security' folder click on the 'Server Certificate' button in the 'Secure communications' section. If you have not used this option before the 'Edit' button will not be active.")?><br />
       <img src="iistutorial/image003.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Select 'Create a new certificate'")?></b><br />
       <?=_("Now 'Create a new certificate'.")?><br />
       <img src="iistutorial/image004.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Prepare the request")?></b><br />
       <?=_("You'll prepare the request now, but you can only submit the request via the online request forms. We do not accept CSRs via email.")?><br />
       <img src="iistutorial/image005.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Enter a certificate name and select Certificate strength")?></b><br />
       <?=_("Select 'Bit length'. We advise a key length of 1024 bits.")?><br />
       <img src="iistutorial/image006.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /><br />
       <br />
       <?=_("You have now created a public/private key pair. The private key is stored locally on your machine. The public portion is sent to CAcert in the form of a CSR.")?><br />
       <br />
       <?=_("You will now create a CSR. This information will be displayed on your certificate, and identifies the owner of the key to users. The CSR is only used to request the certificate. The following characters must be excluded from your CSR fields, or your certificate may not work:")?> <p style="color: red;">! @ # $ % ^ * ( ) ~ ? &gt; &lt; &amp; / \</p>
       </li>
   <li><b><?=_("Enter your Organisation Information")?></b><br />
       <?=_("Enter the Organisation name: this must be the full legal name of the Organisation that is applying for the certificate.")?><br />
       <br />
       <?=_("The Organisational Unit field is the 'free' field. It is often the department or Server name for reference.")?><br />
       <img src="iistutorial/image007.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Enter your Common Name")?></b><br />
       <?=_("The Common Name is the fully qualified host and Domain Name or website address that you will be securing. Both 'www.CAcert.org' and 'secure.CAcert.com' are valid Common Names. IP addresses are usually not used.")?><br />
       <img src="iistutorial/image008.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Enter the geographical details")?></b><br />
       <?=_("Your country, state and city.")?><br />
       <img src="iistutorial/image009.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Choose a filename to save the request to")?></b><br />
       <?=_("Select an easy to locate folder. You'll have to open this file up with Notepad. The CSR must be copied and pasted into our online form. Once the CSR has been submitted, you won't need this CSR any more as IIS won't reuse old CSR to generate new certificates.")?><br />
       <img src="iistutorial/image010.gif" height="386" width="503" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
   <li><b><?=_("Confirm your request details")?></b></li>
  </ol>
<p><?=_("Finish up and exit IIS Certificate Wizard")?></p>

<h3><?=_("Certificate Installation process for IIS 5.0")?></h3>
<p><?=_("After your certificate has been emailed to you, follow this process to install the certificate.")?></p>
    <ol class="tutorial">
     <li><b><?=_("Saving the certificate")?></b><br />
     <?=_("Copy the contents of the email including the")?>
     <code>-----BEGIN CERTIFICATE-----</code> <?=_("and")?>
     <code>-----END CERTIFICATE-----</code> <?=_("lines. Do not copy any extra line feeds or carriage returns at the beginning or end of the certificate. Save the certificate into a text editor like Notepad. Save the certificate with an extension of .cer and a meaningful name like certificate.cer")?><br /><br />
         <img src="iistutorial/image011b.png" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
     <li><b><?=_("Installation steps")?></b><br />
         <?=_("Return to the 'Internet Information Services' screen in 'Administrative Tools' under 'Control Panel'. Right click on 'Default Web Site' and select 'Properties'.")?><br />
         <img src="iistutorial/image001.jpg" height="453" width="642" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
     <li><b><?=_("Select the Directory Security tab")?></b><br />
         <?=_("Select 'Server Certificate' at the bottom of the tab in the 'Secure communications' section.")?><br />
         <img src="iistutorial/image002.jpg" height="453" width="463" alt="<?=_("Screenshot of IIS 5.0")?>" /><br /></li>
     <li><b><?=_("In the 'IIS Certificate Wizard' you should find a 'Pending Certificate Request'.")?></b><br />
         <?=_("Ensure 'Process the pending request and install the certificate' is selected and click on 'Next'.")?><br />
         <img src="iistutorial/image012.gif" height="388" width="506" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
     <li><b><?=_("Browse to the location you saved the .cer file to in step 1")?></b><br />
         <?=_("Select the .cer file and click 'Next'.")?><br />
         <img src="iistutorial/image013.gif" height="388" width="505" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
     <li><b><?=_("Ensure that you are processing the correct certificate")?></b><br />
          <?=_("...then click 'Next'.")?><br />
         <img src="iistutorial/image014.jpg" height="390" width="506" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
     <li><b><?=_("You will see a confirmation screen.")?></b><br />
        <?=_("When you have read this information, click 'Finish'.")?><br />
         <img src="iistutorial/image015.gif" height="390" width="507" alt="<?=_("Screenshot of IIS 5.0")?>" /></li>
    </ol>
    <p><b><?=_("And you're done!")?></b></p>
  <p><?=_("For more information, refer to your server documentation or visit")?> <a href="http://support.microsoft.com/support/"><?=_("Microsoft Support Online")?></a>.</p>
