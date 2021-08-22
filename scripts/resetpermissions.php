#!/usr/bin/php -q
<?php
/*
LibreSSL - CAcert web application
Copyright (C) 2004-2020  CAcert Inc.

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

require_once(dirname(__FILE__).'/../includes/mysql.php');

$flags = array('board', 'tverify');

foreach ($flags as $flag) {
	echo "Resetting $flag flag:\n";
	$query = "select `id`, `fname`, `lname`, `email` from `users`
			where `$flag` = 1";
	if(! $res = $db_conn->query($query) ) {
		fwrite(STDERR,
				"MySQL query for flag $flag failed:\n".
				"\"$query\"\n".
				$db_conn->error
		);
	
		continue;
	}
	
	while ($row = $res->fetch_assoc()) {
		echo "$row[fname] $row[lname] $row[email]";
		
		$update = "update `users` set `$flag` = 0 where `id` = $row[id]";
		if(! $res2 = $db_conn->query($update) ) {
			echo " NOT RESET!!!\n";
			fwrite(STDERR,
					"MySQL query for $flag flag reset on user $row[id] failed:\n".
					"\"$update\"\n".
					$db_conn->error
			);
			
		} else {
			
			$message = <<<EOF
Hi $row[fname],

As per Arbitration a20110118.1 [1] the $flag permission has been removed
from your account.

[1] https://wiki.cacert.org/Arbitrations/a20110118.1

Best Regards,
CAcert Support
EOF;
			sendmail($row['email'], "Permissions have been reset", $message, 'support@cacert.org');
			
			echo " reset.\n";
		}
	}
	
	echo "\n\n";
}