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

require_once(dirname(__FILE__).'/../../includes/mysql.php');

$BOARD_PRIVATE = 'cacert-board-private@lists.cacert.org';
$ASSURANCE_OFFICER = 'ao@cacert.org';
$ORGANISATION_ASSURANCE_OFFICER = 'oao@cacert.org';


//defines to whom to send the lists
$flags = array(
	'admin' => array(
			'name'    => 'Support Engineer',
			'own'     => false, //Don't send twice
			'board'   => true,
			'support' => true,
			'ao'      => false,
			'oao'     => false
			),
	
	'orgadmin' => array(
			'name'    => 'Organisation Assurer',
			'own'     => true,
			'board'   => true,
			'support' => true,
			'ao'      => true,
			'oao'     => true
			),
	
	'board' => array(
			'name'    => 'Board Member',
			'own'     => false,
			'board'   => true,
			'support' => true,
			'ao'      => true,
			'oao'     => false
			),
	
	'ttpadmin' => array(
			'name'    => 'Trusted Third Party Admin',
			'own'     => true,
			'board'   => true,
			'support' => true,
			'ao'      => true,
			'oao'     => true
			),
	
	'tverify' => array(
			'name'    => 'Tverify Admin',
			'own'     => false,
			'board'   => true,
			'support' => true,
			'ao'      => true,
			'oao'     => false
			),
	
	'locadmin' => array(
			'name'    => 'Location Admin',
			'own'     => false,
			'board'   => true,
			'support' => true,
			'ao'      => false,
			'oao'     => false
			),
	);


// Build up list of various admins
$adminlist = array();
foreach ($flags as $flag => $flag_properties) {
	$query = "select `fname`, `lname`, `email` from `users` where `$flag` = 1";
	if(! $res = mysql_query($query) ) {
		fwrite(STDERR,
				"MySQL query for flag $flag failed:\n".
				"\"$query\"\n".
				mysql_error()
			);
		
		continue;
	}
	
	$adminlist[$flag] = array();
	
	while ($row = mysql_fetch_assoc($res)) {
		$adminlist[$flag][] = $row;
	}
	
	
	// Send mail to admins of this group if 'own' is set
	if ($flag_properties['own']) {
		foreach ($adminlist[$flag] as $admin) {
			$message = <<<EOF
Hello $admin[fname],

you get this message, because you are listed as $description on
CAcert.org. Please review the following list of persons with the same privilege
and report to the responsible team leader or board
($BOARD_PRIVATE) if you spot any errors.

EOF;
			
			foreach ($adminlist[$flag] as $colleague) {
				$message .= "$colleague[fname] $colleague[lname] $colleague[email]\n";
			}
			
			$message .= <<<EOF


Best Regards,
CAcert Support
EOF;
			
			sendmail($admin['email'], "Permissions Review", $message, 'support@cacert.org');
		}
	}
}



// Send to support engineers
$message = <<<EOF
Dear Support Engineers,

it's time for the permission review again. Here is the list of privileged users
in the CAcert web application. Please review them.


EOF;

foreach ($flags as $flag => $flag_properties) {
	if ($flag_properties['support']) {
		$message .= "List of $flag_properties[name]s:\n";
		foreach ($adminlist[$flag] as $colleague) {
			$message .= "$colleague[fname] $colleague[lname] $colleague[email]\n";
		}
	}
}

$message .= <<<EOF

Best Regards,
CAcert Support
EOF;

foreach ($adminlist['admin'] as $support_engineer) {
	sendmail(
			$support_engineer['email'],
			"Permissions Review",
			$message,
			'support@cacert.org');
}


// Send to one-email addresses
foreach (array(
			'ao' => array(
					'description' => 'Assurance Officer',
					'email' => $ASSURANCE_OFFICER),
			'oao' => array(
					'description' => 'Organisation Assurance Officer',
					'email' => $ORGANISATION_ASSURANCE_OFFICER),
			'board' => array(
					'description' => 'Board Members',
					'email' => $BOARD_PRIVATE)
		) as $key => $values) {
	$message = <<<EOF
Dear $values[description],

it's time for the permission review again. Here is the list of privileged users
in the CAcert web application. Please review them and also ask the persons 
responsible for an up-to-date copy of access lists not directly recorded in the
web application (critical admins, software assessors etc.) 


EOF;
	
	foreach ($flags as $flag => $flag_properties) {
		if ($flag_properties[$key]) {
			$message .= "List of $flag_properties[name]s:\n";
			foreach ($adminlist[$flag] as $colleague) {
				$message .= "$colleague[fname] $colleague[lname] $colleague[email]\n";
			}
		}
	}
	
	$message .= <<<EOF

Best Regards,
CAcert Support
EOF;

	sendmail($values['email'], "Permissions Review", $message, 'support@cacert.org');
}
