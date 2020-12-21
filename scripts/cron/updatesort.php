#!/usr/bin/php -q
<? /*
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
	require_once(dirname(__FILE__).'/../../includes/mysql.php');
	require_once(dirname(__FILE__).'/../../includes/lib/account.php');


	// Recalculate assurer flag for all accounts
	if (!fix_assurer_flag()) {
		fwrite(STDERR, "ERROR on fixing the assurer flag. Continuing anyway");
	}


	$db_conn->query("update `locations` set `acount`=0");
	$query = "SELECT `users`.`locid` AS `locid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`locid` != 0 and users.listme=1
			GROUP BY `users`.`locid`";
	$res = $db_conn->query($query);
	while($row = $res->fetch_assoc())
	{
		$query = "update `locations` set `acount`='${row['total']}' where `id`='${row['locid']}'";
		echo $query."\n";
		$db_conn->query($query);
	}


	$db_conn->query("update `regions` set `acount`=0");
	$query = "SELECT `users`.`regid` AS `regid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`regid` != 0 and users.listme=1
			GROUP BY `users`.`regid`";
	$res = $db_conn->query($query);
	while($row = $res->fetch_assoc())
	{
		$query = "update `regions` set `acount`='${row['total']}' where `id`='${row['regid']}'";
		echo $query."\n";
		$db_conn->query($query);
	}




	$db_conn->query("update `countries` set `acount`=0");
	$query = "SELECT `users`.`ccid` AS `ccid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`ccid` != 0 and users.listme=1
			GROUP BY `users`.`ccid`";
	$res = $db_conn->query($query);
	while($row = $res->fetch_assoc())
	{
		$query = "update `countries` set `acount`='${row['total']}' where `id`='${row['ccid']}'";
		echo $query."\n";
		$db_conn->query($query);
	}


?>
