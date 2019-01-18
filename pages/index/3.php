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

<p><?=sprintf(_("You are bound by the %s Root Distribution Licence %s for any re-distributions of CAcert's roots."),"<a href='/policy/RootDistributionLicense.html'>","</a>")?></p>

<h3><?=_("Class 1 PKI Key")?></h3>
<ul class="no_indent">
	<li><a href="certs/root_X0F.crt"><?=_("Root Certificate (PEM Format)")?></a></li>
	<li><a href="certs/root_X0F.der"><?=_("Root Certificate (DER Format)")?></a></li>
	<li><a href="certs/root_X0F.txt"><?=_("Root Certificate (Text Format)")?></a></li>
	<li><a href="<?=$_SERVER['HTTPS']?"https":"http"?>://crl.cacert.org/revoke.crl">CRL</a></li>
	<li><?=_("SHA256 fingerprint:")?> 07ED BD82 4A49 88CF EF42 15DA 20D4 8C2B 41D7 1529 D7C9 00F5 7092 6F27 7CC2 30C5</li>
</ul>

<h3><?=_("Class 3 PKI Key")?></h3>
<ul class="no_indent">
	<li><a href="certs/class3_X0E.crt"><?=_("Intermediate Certificate (PEM Format)")?></a></li>
	<li><a href="certs/class3_X0E.der"><?=_("Intermediate Certificate (DER Format)")?></a></li>
	<li><a href="certs/class3_X0E.txt"><?=_("Intermediate Certificate (Text Format)")?></a></li>
	<li><a href="<?=$_SERVER['HTTPS']?"https":"http"?>://crl.cacert.org/class3-revoke.crl">CRL</a></li>
    <li><?=_("SHA256 fingerprint:")?> F687 3D70 D675 96C2 ACBA 3440 1E69 738B 5270 1DD6 AB06 B497 49BC 5515 0936 D544</li>
</ul>

<h3><?=_("GPG Key")?></h3>
<ul class="no_indent">
	<li><a href="certs/cacert.asc"><?=_("CAcert's GPG Key")?></a></li>
	<li><?=_("GPG Key ID:")?> 0x65D0FD58</li>
	<li><?=_("Fingerprint:")?> A31D 4F81 EF4E BD07 B456 FA04 D2BB 0D01 65D0 FD58</li>
</ul>

<h4><?=_("PKI fingerprint signed by the CAcert GPG Key")?></h4>
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

<h3><?=_("History")?></h3>
<p>
<? printf(_('An overview over all CA certificates ever issued can be found in '.
        '%sthe wiki%s.'),
    '<a href="//wiki.cacert.org/Roots/StateOverview">',
    '</a>') ?>
</p>
