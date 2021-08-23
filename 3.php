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
    <li><?=_("SHA1 fingerprint:")?> DDFC DA54 1E75 77AD DCA8 7E88 27A9 8A50 6032 52A5</li>
</ul>

<h3><?=_("Class 3 PKI Key")?></h3>
<ul class="no_indent">
	<li><a href="certs/CAcert_Class3Root_x14E228.crt"><?=_("Intermediate Certificate (PEM Format)")?></a></li>
	<li><a href="certs/CAcert_Class3Root_x14E228.der"><?=_("Intermediate Certificate (DER Format)")?></a></li>
	<li><a href="certs/CAcert_Class3Root_x14E228.txt"><?=_("Intermediate Certificate (Text Format)")?></a></li>
	<li><a href="<?=$_SERVER['HTTPS']?"https":"http"?>://crl.cacert.org/class3-revoke.crl">CRL</a></li>
    <li><?=_("SHA256 fingerprint:")?> 1BC5 A61A 2C0C 0132 C52B 284F 3DA0 D8DA CF71 7A0F 6C1D DF81 D80B 36EE E444 2869</li>
    <li><?=_("SHA1 fingerprint:")?> D8A8 3A64 117F FD21 94FE E198 3DD2 5C7B 32A8 FFC8</li>
</ul>

<h3><?=_("GPG Key")?></h3>
<ul class="no_indent">
	<li><a href="certs/cacert.asc"><?=_("CAcert's GPG Key")?></a></li>
	<li><?=_("GPG Key ID:")?> 0x65D0FD58</li>
	<li><?=_("Fingerprint:")?> A31D 4F81 EF4E BD07 B456 FA04 D2BB 0D01 65D0 FD58</li>
</ul>


<?php if ( false ) { ?>
    /**
    Since we don't seem to have a way to GPG sign our current key, we have, at least temporarily, removed this.

    https://bugs.cacert.org/view.php?id=1305#c5784

    **/
<h4><?=_("PKI fingerprint signed by the CAcert GPG Key")?></h4>
    <pre>
-----BEGIN PGP SIGNED MESSAGE-----
Hash: SHA1

For most software, the fingerprint is reported as (SHA1):
DD:FC:DA:54:1E:75:77:AD:DC:A8:7E:88:27:A9:8A:50:60:32:52:A5
(and/or SHA256):
07:ED:BD:82:4A:49:88:CF:EF:42:15:DA:20:D4:8C:2B:
41:D7:15:29:D7:C9:00:F5:70:92:6F:27:7C:C2:30:C5

Under MS MMC-Certificates and MS browsers the thumbprint is reported as:
ddfc da54 1e75 77ad dca8 7e88 27a9 8a50 6032 52a5
-----BEGIN PGP SIGNATURE-----
Version: GnuPG v1.2.2 (GNU/Linux)

iD8DBQE/VtRZ0rsNAWXQ/VgRAphfAJ9jh6TKBDexG0NTTUHvdNuf6O9RuQCdE5kD
Mch2LMZhK4h/SBIft5ROzVU=
=R/pJ
-----END PGP SIGNATURE-----
</pre>
<?php } ?>

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
