#!/usr/bin/php -q
<?php
/*
LibreSSL - CAcert web application
Copyright (C) 2004-2012  CAcert Inc.

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

require_once('../../includes/mysql.php');

$BOARD_PRIVATE = 'cacert-board-private@lists.cacert.org';

$flags = array(
	'admin' => 'Support Engineer',
	'orgadmin' => 'Organisation Assurer',
	'board' => 'Board Member',
	'ttpadmin' => 'Trusted Third Party Admin',
	'tverify' => 'Tverify Admin',
	'locadmin' => 'Location Admin'
	);

$adminlist = array();

foreach ($flags as $flag => $description) {
	$query = "select `fname`, `lname`, `email` from `users` where `$flag` = 1";
	if(! $res = mysql_query($query) ) {
		fwrite(STDERR,
				"MySQL query for flag $flag failed:\n".
				"\"$query\"\n".
				mysql_error()
			);
		
		continue;
	}
	
	$admins = array();
	$adminlist[$flag] = "";
	
	while ($row = mysql_fetch_assoc($res)) {
		$admins[] = $row;
		$adminlist[$flag] .= "$row[fname] $row[lname] $row[email]\n";
	}
	
	foreach ($admins as $admin) {
		$message = <<<EOF
Hello $admin[fname],

you get this message, because you are listed as $description on
CAcert.org. Please review the following list of persons with the same privilege
and report to the responsible team leader or board
($BOARD_PRIVATE) if you spot any errors.

$adminlist[$flag]


Best Regards,
CAcert Support
EOF;
		sendmail($admin['email'], "Permissions Review", $message, 'support@cacert.org');
	}
}



$message = <<<EOF
Dear Board Members,

it's time for the permission review again. Here is the list of privileged users
in the CAcert web application. Please review them and also ask the persons 
responsible for an up-to-date copy of access lists not directly recorded in the
web application (critical admins, software assessors etc.) 

EOF;

foreach ($flags as $flag => $description) {
	$message .= <<<EOF
List of ${description}s:
$adminlist[$flag]

EOF;
}

$message .= <<<EOF

Best Regards,
CAcert Support
EOF;

sendmail($BOARD_PRIVATE, "Permissions Review", $message, 'support@cacert.org');
