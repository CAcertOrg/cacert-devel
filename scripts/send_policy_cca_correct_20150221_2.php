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

We have to inform you that there was an incorrect version of the CAcert Community Agreement (CCA) on the main website of CAcert for some a couple of days.

The CCA is the general terms and conditions for CAcert. We may only issue certificates and sign PGP keys to those who have accepted those conditions.

As you have accepted the CCA during that period, we want to give you a link to the correct version, to ensure that you are not confused about the different versions.

The correct version of the CCA can be found at:
http://www.cacert.org/policy/CAcertCommunityAgreement.html

Sincerely,
Eva Stöwe
CAcert Policy Officer
EOF;

$lines_EN = wordwrap($lines_EN, 75, "\n");
$lines_EN = mb_convert_encoding($lines_EN, "HTML-ENTITIES", "UTF-8");


// read last used id
$lastid = 0;
if (file_exists("send_policy_cca_correct_20150221_2_lastid.txt"))
{
	$fp = fopen("send_policy_cca_correct_20150221_2_lastid.txt", "r");
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
	WHERE	user_agreements.date >= '2015-01-08 14:29:00'
	AND	user_agreements.date <= '2015-01-15 10:48:00'
	AND	user_agreements.document = 'CCA'
	AND	users.id IN (
			SELECT users.id
			FROM  user_agreements
			LEFT JOIN users ON users.id = user_agreements.memid
			WHERE        user_agreements.date < '2015-01-08 14:29:00'
			AND        user_agreements.document = 'CCA')
	GROUP BY users.id";

$res = mysqli_query($_SESSION['mconn'], $query);

while($row = mysqli_fetch_assoc($res))
{
	$mailtxt = "Dear ${row["fname"]} ${row["lname"]},\n".$lines_EN."\n\n";

	sendmail($row['email'], "[CAcert.org] CAcert Community Agreement (CCA)", $mailtxt, "support@cacert.org", "", "", "CAcert", "returns@cacert.org", "");

	$fp = fopen("send_policy_cca_correct_20150221_2_lastid.txt", "w");
	fputs($fp, $row["id"]."\n");
	fclose($fp);

	$count++;
	echo "Sent ${count}th mail. User ID: ${row["id"]}\n";

	if(0 == $count % 5) {
		sleep (1);
	}
}
