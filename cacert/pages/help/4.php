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
<p><?=_("Firstly you will need to run the following command, preferably in secured directory no one else can access, however protecting your private keys is beyond the scope of this document.")?></p>
<p># openssl req -nodes -new -keyout private.key -out server.csr</p>
<p><?=_("Then the system will try to generate some very random numbers to get a secure key.")?></p>
<p><?=_("Generating a 1024 bit RSA private key")?><br>
  ...++++++<br>
  ....++++++<br>
<?=_("writing new private key to 'private.key'")?></p>
<p><?=_("You will then be asked to enter information about your company into the certificate. Below is a valid example:")?></p>
<p><?=_("Country Name (2 letter code) [AU]:")?>AU<br>
  <?=_("State or Province Name (full name) [NSW]:")?>NSW<br>
  <?=_("Locality Name (eg, city) [Sydney]:")?>Sydney<br>
  <?=_("Organization Name (eg, company) [XYZ Corp]:")?>CAcert Inc.<br>
  <?=_("Organizational Unit Name (eg, section) [Server Administration]:.")?><br>
  <?=_("Common Name (eg, YOUR name) []:")?>www.cacert.org<br>
  <?=_("Email Address")?> []:no-returns@cacert.org</p>
<p><?=_("Finally you will be asked information about 'extra' attribute, you simply hit enter to both these questions.")?></p>
<p><?=_("Next step is that you submit the contents of server.csr to the CAcert website, it should look *EXACTLY* like the following example otherwise the server may reject your request because it appears to be invalid.")?></p>
<p>-----BEGIN CERTIFICATE REQUEST-----<br>
  MIIBezCB5QIBADA8MRcwFQYDVQQDEw53d3cuY2FjZXJ0Lm9yZzEhMB8GCSqGSIb3<br>
  DQEJARYSc3VwcG9ydEBjYWNlcnQub3JnMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCB<br>
  iQKBgQDQd1+ut4TJLWZf5A9r3D17Kob+CNwz/jfCOYrH0P6q1uw4jfSyrWUeSaVc<br>
  59Xjpov8gRctlAuWM9KavkLSF6vcNdDEbvUYnL/+ixdmVE9tlXuSFEGz0GAF5faf<br>
  QZe30wk+2hnC6P+rwclypOhkTXtWgvSHPZg9Cos8xqDyv589QwIDAQABoAAwDQYJ<br>
  KoZIhvcNAQEEBQADgYEAJruzBZr4inqaeidn1m2q47lXZUWjgsrp3k3bFJ/HCb3S<br>
  2SgVqHFrOisItrr7H0Dw2EcPhIrRokRdjIAwwlxG9v21eFaksZUiaP5Yrmf89Njk<br>
  HV+MZXxbC71NIKrnZsDhHibZslICh/XjdPP7zfKMlHuaaz1oVAmu9BlsS6ZXkVA=<br>
-----END CERTIFICATE REQUEST----- </p>
<p><?=_("Once you've submitted it the system will process your request and send an email back to you containing your server certificate.")?></p>
