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

<p><?=sprintf(_("You are bound by the %s Root Distribution Licence %s for any re-distributions of CAcert's roots."),"<a href='/policy/RootDistributionLicense.php'>","</a>")?></p>

<p>
Class 1 <?=_("PKI Key")?><br>
<a href="index.php?id=17"><?=_("Click here if you want to import the root certificate into Microsoft Internet Explorer 5.x/6.x")?></a><br>
<a href="certs/root.crt"><?=_("Root Certificate (PEM Format)")?></a><br>
<a href="certs/root.der"><?=_("Root Certificate (DER Format)")?></a><br>
<a href="certs/root.txt"><?=_("Root Certificate (Text Format)")?></a><br>
<a href="<?=$_SERVER['HTTPS']?"https":"http"?>://crl.cacert.org/revoke.crl">CRL</a><br>
<?=_("Fingerprint")?> SHA1: 13:5C:EC:36:F4:9C:B8:E9:3B:1A:B2:70:CD:80:88:46:76:CE:8F:33<br/>
<?=_("Fingerprint")?> MD5: A6:1B:37:5E:39:0D:9C:36:54:EE:BD:20:31:46:1F:6B<br/>
</p>

<p>
Class 3 <?=_("PKI Key")?><br>
<a href="certs/class3.crt"><?=_("Intermediate Certificate (PEM Format)")?></a><br/>
<a href="certs/class3.der"><?=_("Intermediate Certificate (DER Format)")?></a><br/>
<a href="certs/class3.txt"><?=_("Intermediate Certificate (Text Format)")?></a><br/>
<a href="<?=$_SERVER['HTTPS']?"https":"http"?>://crl.cacert.org/class3-revoke.crl">CRL</a><br/>
<?=_("Fingerprint")?> SHA1: DB:4C:42:69:07:3F:E9:C2:A3:7D:89:0A:5C:1B:18:C4:18:4E:2A:2D<br/>
<?=_("Fingerprint")?> MD5: 73:3F:35:54:1D:44:C9:E9:5A:4A:EF:51:AD:03:06:B6<br/>
</p>

<p>
<?=_("GPG Key")?><br>
<a href="certs/cacert.asc"><?=_("CAcert's GPG Key")?></a><br>
</p>

<p>
<?=_("PKI finger/thumb print signed by the CAcert GPG Key")?><br>
<pre>
-----BEGIN PGP SIGNED MESSAGE-----
Hash: SHA1

For most software, the fingerprint is reported as:
A6:1B:37:5E:39:0D:9C:36:54:EE:BD:20:31:46:1F:6B

Under MSIE the thumbprint is reported as:
135C EC36 F49C B8E9 3B1A B270 CD80 8846 76CE 8F33
-----BEGIN PGP SIGNATURE-----
Version: GnuPG v1.2.2 (GNU/Linux)

iD8DBQE/VtRZ0rsNAWXQ/VgRAphfAJ9jh6TKBDexG0NTTUHvdNuf6O9RuQCdE5kD
Mch2LMZhK4h/SBIft5ROzVU=
=R/pJ
-----END PGP SIGNATURE-----
</pre>
<pre>
-----BEGIN PGP SIGNED MESSAGE-----
Hash: SHA1

pub  1024D/65D0FD58 2003-07-11 CA Cert Signing Authority (Root CA)
     Key fingerprint = A31D 4F81 EF4E BD07 B456  FA04 D2BB 0D01 65D0 FD58
sub  2048g/113ED0F2 2003-07-11 [expires: 2033-07-03]
-----BEGIN PGP SIGNATURE-----
Version: GnuPG v1.2.5 (GNU/Linux)

iD8DBQFCEDLN0rsNAWXQ/VgRArhhAJ9EY1TJOzsVVuy2lL98CoKL0vnJjQCfbdBk
TG1yj+lkktROGGyn0hJ5SbM=
=tXoj
-----END PGP SIGNATURE-----
</pre>
</p>
