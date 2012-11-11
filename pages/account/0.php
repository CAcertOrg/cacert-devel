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
<H3><?=_("My Account")?></H3>
<p><?=_("Welcome to your account section of the website. Below is a description of the different sections and what they're for.")?></p>
<H4><?=_("CAcert.org")?></H4>
<p><?=_("If you would like to view news items or change languages you can click the logout or go home links. Go home doesn't log you out of the system, just returns you to the front of the website. Logout logs you out of the system.")?></p>
<H4><?=_("My Details")?></H4>
<p><?=_("In this section you will be able to edit your personal information (if you haven't been assured), update your pass phrase, and lost pass phrase questions. You will also be able to set your location for the Web of Trust, it also effects the email announcement settings which among other things can be set to notify you if you're within 200km of a planned assurance event. You'll also be able to set additional contact information when you become fully trusted, so others can contact you to meet up outside official events.")?></p>
<h4><?=_("Email Addresses and Client Certificates")?></h4>
<p><?=_("The email address section is for adding/updating/removing email addresses which can be used to issue client certificates against. The client certificate section steps you through generating a certificate signing request for one or more email addressess you've registered in the email address section.")?></p>
<h4><?=_("Domains and Server Certificates.")?></h4>
<p><?=_("Before you can start issuing certificates for your website, irc server, smtp server, pop3, imap etc you will need to add domains to your account under the domain menu. You can also remove domains from here as well. Once you've added a domain you are free then to go into the Server Certificate section and start pasting CSR into the website and have the website return you a valid certificate for up to 2 years if you have 50 assurance points, or 6 months for no assurance points.")?></p>
<h4><?=_("Org Client and Server Certificates")?></h4>
<p><?=_("Once you have verified your company you will see these menu options. They allow you to issue as many certificates as you like without proving individual email accounts as you like, further more you are able to get your company details on the certificate.")?></p>
<h4><?=_("CAcert Web of Trust")?></h4>
<p><?=_("The Web of Trust system CAcert uses is similar to that many use that are involved with GPG/PGP. They hold face to face meetings to verify, if each others photo identity cards match their GPG/PGP key information. CAcert differs however in that we have modified things to work within the PKI framework. To gain trust in the system, you must first locate someone already trusted. The trust of a person depends on how many people they've trusted or met before and that will determine how many points they can issue to you (the number of points they can issue is listed in the locate assurer section). Once you've met up, you can show your ID documents and you will need to fill out a CAP form, which the person assuring your details must retain for later verification if needed.")?></p>
<p><b><?=_("The former TTP (Trusted Third Party) System has been stopped, and is currently not available.")?></b></p>
<? // "You can also get trust points via the Trust Third Party system where you go to a lawyer, bank manager, accountant, or public notary/juctise of the peace and they via your ID and fill in the TTP form to state they have viewed your ID documents and it appears authentic and true. More information on the TTP system can be found in the TTP sub-menu</p> ?>
