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

We want to inform you about some recent policy changes within CAcert.

On 2014-07-28, CAcert's policy group voted to accept a new version of the CAcert Community Agreement (short: CCA) into DRAFT status. This makes it a binding policy from that date.

The CCA is the core document that an user has to accept to become and to remain a member of the CAcert community. The previous CCA version has been in place for about five years, the policy group saw some need to update it and to improve and clarify some topics.

You can find the current version of the CCA at:
http://www.cacert.org/policy/CAcertCommunityAgreement.php

The changes are currently highlighted in blue. You can find a summary of the major changes, at the end of this mail.

Currently the changes are in DRAFT status, which makes them binding, but also gives some room to address them. We plan to change the status to POLICY on about 2014-10-15.

If you do not accept the CCA changes, please send an email to support@cacert.org no later than 2014-10-15 to request termination of your CAcert membership. Terminating your membership will cause the revocation of all your certificates, disabling your login to the CAcert web interface and the anonymisation of your personal data.

If you accept the CCA changes, no action is needed.

Moreover, we want to inform you about a software change that is planned to be installed, soon. When it is installed, the CAcert website will check, if the user has already accepted the CCA at each login. If this is not the case, the user will be presented with the option to either accept the CCA or to leave the page.

By doing this we will ensure that only users who have accepted the CCA, and by this may be called members of CAcert community will be able to login and issue new certificates. This change will only affect users who have neither issued a new certificate nor participated in assurances for some while.

The reason for this change is that every member who has agreed to the CCA, should be able to rely on the fact that all other users of CAcert have also agreed to it. For historic reasons this is not always the case, yet.

Last but not least, the following policies were set to POLICY status on 2014-08-14. They have all been in DRAFT status without any further changes for at least a year:

 * Policy on Policy ("PoP" => COD1)
 * Configuration-Control Specification ("CCS" => COD2)
 * Certification Practice Statement ("CPS" => COD6)
 * Dispute Resolution Policy ("DRP" => COD7)
 * Security Policy ("SP" => COD8)
 * Organisation Assurance Policy ("OAP" => COD11)
 * Root Distribution License ("RDL" => COD14)
 * Organisation Assurance Subsidary Policy - Germany (COD11.DE)
 * Organisation Assurance Subsidary Policy - Europe (COD11.EU)
 * Organisation Assurance Subsidary Policy - Australia (COD11.AU)
 * TTP-Assisted Assurance Policy ("TTP-Assist" => COD13.2)

We are working hard to update all the documents so that they show their policy status. As the content has not been changed and will remain the same, we decided to inform you about this even though we have not finished, yet.

You can find the above polices at:
https://svn.cacert.org/CAcert/Policies/ControlledDocumentList.html

As most of those polices have not been reviewed for a while, you probably will see some more updates to CAcert policies coming soon. Those changes will be an important step to be able to pass an audit, which is one of our current goals.

Every CAcert member interested in participating in the design of our policies, is invited to join our policy group mailing list at:
https://lists.cacert.org/wws/info/cacert-policy

Major changes for the CCA:
 * The CCA was changed to clearly be a general terms and conditions what makes it easier to join and exit as CAcert member. For CAcert it was obvious to do the change, because all CAcert members sign the same conditions without the possibility to strike or add personal clauses.
 * More ways to accept the CCA were added.
 * Termination of membership was clarified some more. Some other options beside the ruling of an Arbitrator were cautiously added.
 * You have a new obligation to answer in arbitration cases. This seems to be obvious, but you never signed it before. In the past this was derived from some points within our Dispute Resolution Policy (DRP).
 * Sharing of accounts and credentials was banned more clearly. Also the obligation to only use a certificate in the appropriate contexts was added. It was already part of the Certification Practice Statement (CPS).
 * Some kinds of contributions as personal data are now excepted from the non-exclusive non-restrictive non-revocable transfer of licence to CAcert.
 * Official communication with CAcert was simplified.
 * Some deprecated references were removed.

A version with all changes can be found at:
https://svn.cacert.org/CAcert/Policies/CAcertCommunityAgreement_20140708.html

Sincerely,
Eva Stöwe
CAcert Policy Officer
EOF;

$lines_EN = wordwrap($lines_EN, 75, "\n");
$lines_EN = mb_convert_encoding($lines_EN, "HTML-ENTITIES", "UTF-8");


// read last used id
$lastid = 0;
if (file_exists("send_policy_cca20140915_lastid.txt"))
{
	$fp = fopen("send_policy_cca20140915_lastid.txt", "r");
	$lastid = trim(fgets($fp, 4096));
	fclose($fp);
}

echo "ID now: $lastid\n";


$count = 0;

$query = "

	SELECT `fname`, `lname`, `email`
	FROM `users`
	WHERE `deleted` = '0000-00-00 00:00:00'
	AND `modified` != '0000-00-00 00:00:00'
	AND `verified` = '1'

	ORDER BY `id`";

$res = mysql_query($query);

while($row = mysql_fetch_assoc($res))
{
	$mailtxt = "Dear ${row["fname"]} ${row["lname"]},\n".$lines_EN."\n\n";

	sendmail($row['email'], "[CAcert.org] CAcert Community Agreement (CCA)", $mailtxt, "support@cacert.org", "", "", "CAcert", "returns@cacert.org", "");

	$fp = fopen("send_policy_cca20140915_lastid.txt", "w");
	fputs($fp, $row["id"]."\n");
	fclose($fp);

	$count++;
	echo "Sent ${count}th mail. User ID: ${row["id"]}\n";

	if(0 == $count % 5) {
		sleep (1);
	}
}
