#!/usr/bin/php -q
<?php
/*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2009  CAcert Inc.

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
include_once("../includes/mysql.php");

// read texts

$lines_EN = <<<EOF

there are news [1] about a bug in OpenSSL that may allow an attacker to leak arbitrary information from any process using OpenSSL. [2]

We contacted you, because you have subscribed to get general announcements, or you have had a server certificate since the bug was introduced into the OpenSSL releases and are especially likely to be affected by it.

CAcert is not responsible for this issue. But we want to inform members about it, who are especially likely to be vulnerable or otherwise affected.


Good news:
==========
Certificates issued by CAcert are not broken and our central systems did not leak your keys.


Bad news:
=========
Even then you may be affected.

Although your keys were not leaked by CAcert your keys on your own systems might have been compromised if you were or are running a vulnerable version of OpenSSL.


To elaborate on this:
=====================
The central systems of CAcert and our root certificates are not affected by this issue. Regrettably some of our infrastructure systems were affected by the bug. We are working to fix them and already completed work for the most critical ones. If you logged into those systems, within the last two years, (see list in the blog post) you might be affected!

But unfortunately given the nature of this bug we have to assume that the certificates of our members may be affected, if they were used in an environment with a publicly accessible OpenSSL connection (e.g. Apache web server, mail server, Jabber server, ...). The bug has been open in OpenSSL for two years - from December 2011 and was introduced in stable releases starting with OpenSSL 1.0.1.

When an attacker can reach a vulnerable service he can abuse the TLS heartbeat extension to retrieve arbitrary chunks of memory by exploiting a missing bounds check. This can lead to disclosure of your private keys, resident session keys and other key material as well as  all volatile memory contents of the server process like passwords, transmitted user data (e.g. web content) as well as other potentially confidential information.

Exploiting this bug does not leave any noticeable traces, thus for any system which is (or has been) running a vulnerable version of OpenSSL you must assume  that at least your used server keys are compromised and therefore must be replaced by newly generated ones. Simply renewing existing certificates is not sufficient! - Please generate NEW keys with at least 2048 bit RSA or stronger!

As mentioned above this bug can be used to leak passwords and thus you should consider changing your login credentials to potentially compromised systems as well as any other system where those credentials might have been used as soon as possible.

An (incomplete) list of commonly used software which include or link to OpenSSL can be found at [5].


What to do?
===========
- Ensure that you upgrade your system to a fixed OpenSSL version (1.0.1g or above).
- Only then create new keys for your certificates.
- Revoke all certificates, which may be affected.
- Check what services you have used that may have been affected within the last two years.
- Wait until you think that those environments got fixed.
- Then (and only then) change your credentials for those services. If you do it too early, i.e. before the sites got fixed, your data may be leaked, again. So be careful when you do this.


CAcert's response to the bug:
=============================
- We updated most of the affected infrastructure systems and created new certificates for them. The remaining will follow, soon.
- We used this opportunity to upgrade to 4096 bit RSA keys signed with SHA-512. The new fingerprints can be found in the list in the blog post. ;-)
- With this email we contact all members, who had active server certificates within the last two years.
- We will keep you updated, in the blog.

A list of affected and fixed infrastructure systems and new information can be found at:

https://blog.cacert.org/2014/04/openssl-heartbleed-bug/


Links:
[1] http://heartbleed.com/
[2] https://www.openssl.org/news/secadv_20140407.txt
[3] https://security-tracker.debian.org/tracker/CVE-2014-0160
[4] http://www.golem.de/news/sicherheitsluecke-keys-auslesen-mit-openssl-1404-105685.html
[5] https://www.openssl.org/related/apps.html
EOF;

$lines_EN = wordwrap($lines_EN, 75, "\n");
$lines_EN = mb_convert_encoding($lines_EN, "HTML-ENTITIES", "UTF-8");


$lines_DE = <<<EOF
---
German Translation / Deutsche Übersetzung:


Liebes CAcert-Mitglied,

es wurde ein Bug in OpenSSL gefunden [4], der es einem Angreifer erlaubt beliebige Informationen jedes Prozesses zu erlangen, der OpenSSL nutzt. [2]

Wir schicken diese Mail an alle Mitglieder, die entweder die "Allgemeinen Ankündigungen" abonniert haben, oder von dem Bug besonders betroffen sein können, da sie Server-Zertifikate in der Zeit besessen haben, seitdem der Bug in die Releases von OpenSSL integriert wurde.

Diese Gefahr geht nicht von CAcert aus, wir möchten aber gefährdete Mitglieder entsprechend informieren.


Die gute Nachricht:
===================
Die von CAcert ausgestellten Zertifikate sind nicht kaputt und unsere zentralen Systeme waren auch nicht angreifbar und haben auch keine Schlüssel verraten.


Die schlechte Nachricht:
========================
Dennoch kann jeder betroffen sein!

Auch wenn keine Schlüssel durch CAcert preisgegeben wurden, können sie dennoch später kompromittiert worden sein, wenn auf Ihren Systemen eine angreifbaren Version von OpenSSL lief und die Schlüssel dort verwendet wurden.


Um ins Detail zu gehen:
=======================
Die zentralen Systeme und die Stammzertifikate von CAcert sind von diesem Problem nicht betroffen. Leider sind einige unserer Infrastruktur-Systeme durch den Fehler betroffen. Wir arbeiten daran diese zu beheben und haben dies auch schon für die meisten erledigt. Jeder, der sich auf diese Systeme in den letzten zwei Jahren eingeloggt hat kann betroffen sein!

Aufgrund der Art des Fehlers, müssen wir leider davon ausgehen, dass die Zertifikate unserer Mitglieder betroffen sind, wenn sie sich in eine Umgebung eingeloggt haben, die über öffentliche OpenSSL-Verbindungen zugänglich war (z.B. Apache Webserver, Mail-Server, Jabber-Server, ...). Dieser Fehler war zwei Jahre lang in OpenSSL - seit Dezember 2011 - und kam beginnend mit Version 1.0.1 in die stabilen Versionen.

Angreifer, die einen verwundbaren Service erreichen können, können die TLS-Erweiterung "heartbeat" ausnutzen, um beliebige Speicherbereiche zu auslesen, indem sie eine fehlende Bereichsprüfung ausnutzen. Das kann zur Offenlegung von privaten Schlüsseln, im Speicher abgelegten Sitzungsschlüsseln, sonstige Schlüssel genauso wie jeglicher weiterer Speicherinhalt des Server-Prozesses wie Passwörter oder übermittelte Benutzerdaten (z.B. Webinhalte) oder anderer vertrauliche Informationen führen.

Die Ausnutzung dieses Fehlers hinterlässt keine merklichen Spuren. Daher muss für jedes System, auf dem eine angreifbare Version von OpenSSL läuft (oder lief), angenommen werden, dass zumindest die verwendeten Server-Zertifikate kompromittiert sind und deswegen durch NEU generierte ersetzt werden müssen. Einfach die alten Zertifikate zu erneuern, reicht nicht aus! - Bitte NEUE Schlüssel mit 2048 Bit RSA oder stärker generieren!

Wie oben erwähnt kann dieser Fehler ausgenutzt werden, um Passwörter zu entwenden. Daher sollte jeder überlegen, alle Zugangsdaten zu möglicherweise betroffenen Systemen und allen Systemen bei denen diese sonst noch verwendet worden sein könnten, so bald wie möglich auszutauschen.

Eine (unvollständige) Liste an weit verbreiteter Software die OpenSSL verwendet kann z.B. unter folgendem Link gefunden werden. [5]


Was ist zu tun?
===============
- Als erstes müssen die eigenen Systeme auf eine fehlerbereinigte Version von OpenSSL aktualisiert werden (Version 1.0.1g oder neuer).
- Danach neue Schlüssel für die Zertifikate erstellen. Jetzt ist es sicher das zu tun.
- Alle möglicherweise betroffenen Zertifikate widerrufen.
- Überprüfen, welche fremden Dienste in den letzten zwei Jahren besucht worden sind.
- Warten, bis dort wahrscheinlich der Fehler behoben wurde.
- Dann (und erst dann) die Login-Daten für diese Dienste erneuern. Vorsicht: Wenn das zu früh getan wird, also wenn der Dienst noch nicht bereinigt wurde, können die Daten wieder abgegriffen werden.


CAcerts Maßnahmen als Antwort auf den Bug:
==========================================
- Wir haben so gut wie alle Infrastruktur-Systeme auf den neuesten OpenSSL-Stand gebracht und für diese neue Zertifikate zu generiert, die restlichen folgen so schnell wie möglich.
- Wir haben die Gelegenheit genutzt, um dabei auf 4096 Bit RSA-Schlüssel, die mit SHA-512 signiert sind, aufzurüsten.
- Mit dieser E-Mail kontaktieren wir alle Mitglieder, die in den letzten zwei Jahren aktive Server-Zertifikate hatten.
- Wir werden neue Informationen im Blog veröffentlichen.

Eine Liste der betroffenen und reparierten Infrastruktur-Systeme befindet sich unter:

https://blog.cacert.org/2014/04/openssl-heartbleed-bug/

Links:
[1] http://heartbleed.com/
[2] https://www.openssl.org/news/secadv_20140407.txt
[3] https://security-tracker.debian.org/tracker/CVE-2014-0160
[4] http://www.golem.de/news/sicherheitsluecke-keys-auslesen-mit-openssl-1404-105685.html
[5] https://www.openssl.org/related/apps.html
EOF;

$lines_DE = wordwrap($lines_DE, 75, "\n");
$lines_DE = mb_convert_encoding($lines_DE, "HTML-ENTITIES", "UTF-8");


// read last used id
$lastid = 0;
if (file_exists("send_heartbleed_lastid.txt"))
{
	$fp = fopen("send_heartbleed_lastid.txt", "r");
	$lastid = trim(fgets($fp, 4096));
	fclose($fp);
}

echo "ID now: $lastid\n";


$count = 0;

$query = "
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `alerts` as a
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and a.`memid` = u.`id`
			and a.`general` = 1
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `domains` as d, `domaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`domid` = d.`id` and d.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `domains` as d, `domlink` as dl, `domaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`id` = dl.`certid` and dl.`domid` = d.`id` and d.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `org` as o, `orgdomaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`orgid` = o.`orgid` and o.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `org` as o, `orgdomains` as d, `orgdomlink` as dl, `orgdomaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`id` = dl.`orgcertid` and dl.`orgdomid` = d.`id`
				and d.`orgid` = o.`orgid` and o.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	order by `id`";

$res = mysql_query($query);

while($row = mysql_fetch_assoc($res))
{
	$mailtxt = "Dear ${row["fname"]} ${row["lname"]},\n".$lines_EN."\n\n";
	switch ($row["language"])
	{
		case "de_DE":
		case "de":
			$mailtxt .= $lines_DE;
			break;
	}

	sendmail($row['email'], "[CAcert.org] Information about Heartbleed bug in OpenSSL 1.0.1 up to 1.0.1f", $mailtxt, "support@cacert.org", "", "", "CAcert", "returns@cacert.org", "");

	$fp = fopen("send_heartbleed_lastid.txt", "w");
	fputs($fp, $row["id"]."\n");
	fclose($fp);

	$count++;
	echo "Sent ${count}th mail. User ID: ${row["id"]}\n";

	sleep (1);
}
