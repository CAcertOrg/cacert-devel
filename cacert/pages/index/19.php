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
<h3><?=_("Information")?></h3>
<table border="0" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <td class="title" colspan="2"><?=_("What can CAcert provide to you, to increase your privacy and security for free?")?></td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Client certificates (un-assured)")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("You can send digitally signed/encrypted emails; others can send encrypted emails to you.")?><br /><br />
      <u><?=_("Limitations")?>:</u> <?=_("Certificates expire in 6 months. Only the email address itself can be entered into the certificate (not your full name)")?>.<br /><br />
      <u><?=_("Verification needed")?>:</u> <?=_("You must confirm it is your email address by responding to a 'ping' email sent to it.")?><br /><br />
    </td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Assured client certificates")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("Same as above plus you can include your full name in the certificates.")?><br /><br />
      <u><?=_("Limitations")?>:</u> <?=_("Certificates expire in 24 months.")?><br /><br />
      <u><?=_("Verification needed")?>:</u> <?=_("Same as above, plus you must get a minimum of 50 assurance points by meeting with one or more assurers from the CAcert Web of Trust, who verify your identity using your government issued photo identity documents.")?><br /><br />
    </td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Code signing certificates")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("Digitally sign code, web applets, installers, etc. including your name and location in the certificates.")?><br><br>
      <u><?=_("Limitations")?>:</u> <?=sprintf(_("Certificates expire in 12 months. Certificates %s must%s include your full name."),"<u>","</u>")?><br /><br />
      <u><?=_("Verification needed")?>:</u> <?=_("Same as above plus get 100 assurance points by meeting with multiple assurers from the CAcert Web of Trust, who verify your identity using your government issued photo identity documents.")?><br><br>
    </td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Server certificates (un-assured)")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("Enable encrypted data transfer for users accessing your web, email, or other SSL enabled service on your server; wildcard certificates are allowed.")?><br><br>
      <u><?=_("Limitations")?>:</u> <?=_("Certificates expire in 6 months; only the domain name itself can be entered into the certificates (not your full name, company name, location, etc.).")?><br><br>
      <u><?=_("Verification needed")?>:</u> <?=_("You must confirm that you are the owner (or authorized administrator) of the domain by responding to a 'ping' email sent to either the email address listed in the whois record, or one of the RFC-mandatory addresses (hostmaster/postmaster/etc).")?><br><br>
    </td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Assured server certificates")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("Same as above.")?><br><br>
      <u><?=_("Limitations")?>:</u> <?=_("Same as above, except certificates expire in 24 months.")?><br><br>
      <u><?=_("Verification needed")?>:</u> <?=_("Same as above, plus get 50 assurance points by meeting with assurer(s) from the CAcert Web of Trust, who verify your identity using your government issued photo identity documents.")?><br><br>
    </td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Become an assurer in CAcert Web of Trust")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("The ability to assure other new CAcert users; contribute to the strengthening and broadening of the CAcert Web of Trust.")?><br><br>
      <u><?=_("Limitations")?>:</u> <?=_("The number of assurance point you have will limit the maximum assurance points you can issue for people you assure.")?><br><br>
      <u><?=_("Verification needed")?>:</u> <?=_("You will need to be issued 100 points by meeting with existing assurers from the CAcert Web of Trust, who verify your identity using your government issued photo identity documents; OR if it is too difficult to meet up with existing assurers in your area, meet with two Trusted Third Party assurers (notary public, justice of the peace, lawyer, bank manager, accountant) to do the verifying.")?><br><br>
    </td>
  </tr>
  <tr>
    <td class="DataTD">
      <h4><?=_("Become a member of the CAcert Association")?></h4>
    </td>
    <td class="DataTD">
      <u><?=_("Benefits")?>:</u> <?=_("You get a vote in how CAcert (a non-profit association incorporated in Australia) is run; be eligible for positions on the CAcert board.")?><br><br>
      <u><?=_("Limitations")?>:</u> <?=_("None, the sky is the limit for CAcert.")?><br><br>
      <u><?=_("Verification needed")?>:</u> <?=_("None; $10 USD per year membership fee.")?><br><br>
    </td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2">
	(*) <?=_("Please note a general limitation is that, unlike long-time players like Verisign, CAcert's root certificate is not included by default in mainstream browsers, email clients, etc. This means people to whom you send encrypted email, or users who visit your SSL-enabled web server, will first have to import CAcert's root certificate, or they will have to agree to pop-up security warnings (which may look a little scary to non-techy users).")?>
    </td>
  </tr>
</table>
<br>
