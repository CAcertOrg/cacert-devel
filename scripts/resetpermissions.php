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

require_once(dirname(__FILE__).'/../includes/mysql.php');

$flags = array('board', 'tverify');

foreach ($flags as $flag) {
	echo "Resetting $flag flag:\n";
	$query = "select `id`, `fname`, `lname`, `email` from `users`
			where `$flag` = 1";
	if(! $res = mysql_query($query) ) {
		fwrite(STDERR,
				"MySQL query for flag $flag failed:\n".
				"\"$query\"\n".
				mysql_error()
		);
	
		continue;
	}
	
	while ($row = mysql_fetch_assoc($res)) {
		echo "$row[fname] $row[lname] $row[email]";
		
		$update = "update `users` set `$flag` = 0 where `id` = $row[id]";
		if(! $res2 = mysql_query($update) ) {
			echo " NOT RESET!!!\n";
			fwrite(STDERR,
					"MySQL query for $flag flag reset on user $row[id] failed:\n".
					"\"$update\"\n".
					mysql_error()
			);
			
		} else {
			echo " reset.\n";
		}
	}
	
	echo "\n\n";
}