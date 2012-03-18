#!/usr/bin/php -q
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
*/
	require_once(dirname(__FILE__).'/../../includes/mysql.php');



	/* Set assurer flag for accounts who miss it

	   See also includes/lib/account.php, function fix_assurer_flag($userID)

	   We may have some performance problems here, there are 150k assurances and 220k users
	   in the production database. The exists-clause on cats_passed should be a good filter... */

	$query = "select `n`.`to` as `uid` from `notary` as `n`, `users` as `u` ".
	         "  where `n`.`to`=`u`.`id` and `u`.`assurer`<>'1' ".
	         "    and (`n`.`expire` > now() OR `n`.`expire` IS NULL) ".
	         "    and exists(select 1 from `cats_passed` as `cp`, `cats_variant` as `cv` ".
	         "                 where `cp`.`variant_id`=`cv`.`id` and `cv`.`type_id` = 1 and `cp`.`user_id`=`n`.`to`)".
	         "  group by `n`.`to` having sum(`n`.`points`)>=100";

	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$query = "update users set `assurer`='1' where `id`='${row['uid']}'";
		//echo $query."\n";
		mysql_query($query);
	}

	/* Remove assurer flag from accounts not eligible.

	   Also a bit performance critical, but assurer flag is only set at 5k accounts

	*/
    $query = "select `u`.id as `uid` from `users` as `u` " .
	         "  where `u`.`assurer` = '1' ".
	         "    and (not exists(select 1 from `cats_passed` as `cp`, `cats_variant` as `cv` ".
	         "                     where `cp`.`variant_id`=`cv`.`id` and `cv`.`type_id` = 1 and `cp`.`user_id`=`u`.`id`) ".
	         "         or (select sum(`n`.`points`) from `notary` as `n` where `n`.`to`=`u`.`id` and (`n`.`expire` > now() OR `n`.`expire` IS NULL)) < 100) ";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$query = "update users set `assurer`='0' where `id`='${row['uid']}'";
		//echo $query."\n";
		mysql_query($query);
	}

	mysql_query("update `locations` set `acount`=0");
	$query = "SELECT `users`.`locid` AS `locid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`locid` != 0 and users.listme=1
			GROUP BY `users`.`locid`";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$query = "update `locations` set `acount`='${row['total']}' where `id`='${row['locid']}'";
		echo $query."\n";
		mysql_query($query);
	}


	mysql_query("update `regions` set `acount`=0");
	$query = "SELECT `users`.`regid` AS `regid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`regid` != 0 and users.listme=1
			GROUP BY `users`.`regid`";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$query = "update `regions` set `acount`='${row['total']}' where `id`='${row['regid']}'";
		echo $query."\n";
		mysql_query($query);
	}




	mysql_query("update `countries` set `acount`=0");
	$query = "SELECT `users`.`ccid` AS `ccid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`ccid` != 0 and users.listme=1
			GROUP BY `users`.`ccid`";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$query = "update `countries` set `acount`='${row['total']}' where `id`='${row['ccid']}'";
		echo $query."\n";
		mysql_query($query);
	}


?>
