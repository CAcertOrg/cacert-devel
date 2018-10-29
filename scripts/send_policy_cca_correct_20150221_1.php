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

We have to inform you that there was an incorrect version of the CAcert Community Agreement (CCA) on the main website of CAcert for a couple of days.

The CCA provides the general terms and conditions for all CAcert members. We may only issue certificates and sign PGP keys to those who have accepted these terms and conditions.

Unfortunately, you are among those who have accepted the CCA while the incorrect version was online.

The correct version of the CCA can be found at:
http://www.cacert.org/policy/CAcertCommunityAgreement.html

If you agree to this version, you do not need to do anything. 

If you do not accept this version, please send an email to support@cacert.org no later than 2015-03-08 to request termination of your CAcert membership. Terminating your membership will cause the revocation of all your certificates, the disabling of your login to the CAcert web interface and the anonymisation of your personal data.

Most of the differences will probably not affect you. but they can be seen at:
http://svn.cacert.org/CAcert/Policies/CAcertCommunityAgreement_20140708.html

Major changes are:
 *  The CCA was changed to clearly be a general terms and conditions what  makes it easier to join and exit as CAcert member. For CAcert it was  obvious to do the change, because all CAcert members sign the same  conditions without the possibility to strike or add personal clauses.
 * More ways to accept the CCA were added.
 *  Termination of membership was clarified some more. Some other options  beside the ruling of an Arbitrator were cautiously added.
 *  You have a new obligation to answer in arbitration cases. This seems to  be obvious, but you never signed it before. In the past this was  derived from some points within our Dispute Resolution Policy (DRP).
 *  Sharing of accounts and credentials was banned more clearly. Also the  obligation to only use a certificate in the appropriate contexts was  added. It was already part of the Certification Practice Statement  (CPS).
 * Some kinds of contributions as personal data are now excepted from the non-exclusive non-restrictive non-revocable transfer of licence to CAcert.
 * Official communication with CAcert was simplified.
 * Some deprecated references were removed.

Sincerely,
Eva Stöwe
CAcert Policy Officer
EOF;

$lines_EN = wordwrap($lines_EN, 75, "\n");
$lines_EN = mb_convert_encoding($lines_EN, "HTML-ENTITIES", "UTF-8");


// read last used id
$lastid = 0;
if (file_exists("send_policy_cca_correct_20150221_1_lastid.txt"))
{
	$fp = fopen("send_policy_cca_correct_20150221_1_lastid.txt", "r");
	$lastid = trim(fgets($fp, 4096));
	fclose($fp);
}

echo "ID now: $lastid\n";


$count = 0;

$query = "

	SELECT
        	users.id,
        	users.fname,
        	users.lname,
        	users.email,
        	COUNT(*) AS agreement_count
	FROM user_agreements
	LEFT JOIN users ON users.id = user_agreements.memid
	WHERE        user_agreements.date >= '2015-01-08 14:29:00'
	AND        user_agreements.date <= '2015-01-15 10:48:00'
	AND        user_agreements.document = 'CCA'
	AND         users.id NOT IN (
                SELECT user_agreements.memid
                FROM  user_agreements
                WHERE        user_agreements.date < '2015-01-08 14:29:00'
                AND        user_agreements.document = 'CCA')
	GROUP BY users.id";

$res = mysql_query($query);

while($row = mysql_fetch_assoc($res))
{
	$mailtxt = "Dear ${row["fname"]} ${row["lname"]},\n".$lines_EN."\n\n";

	sendmail($row['email'], "[CAcert.org] CAcert Community Agreement (CCA)", $mailtxt, "support@cacert.org", "", "", "CAcert", "returns@cacert.org", "");

	$fp = fopen("send_policy_cca_correct_20150221_1_lastid.txt", "w");
	fputs($fp, $row["id"]."\n");
	fclose($fp);

	$count++;
	echo "Sent ${count}th mail. User ID: ${row["id"]}\n";

	if(0 == $count % 5) {
		sleep (1);
	}
}
