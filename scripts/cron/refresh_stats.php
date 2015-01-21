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

/**
 * Wrapper around mysql_query() to provide some error handling. Prints an error
 * message and dies if query fails
 *
 * @param string $sql
 * 		the SQL statement to execute
 * @return resource|boolean
 * 		the MySQL result set
 */
function sql_query($sql) {
	$res = mysql_query($sql);
	if (!$res) {
		fwrite(STDERR, "MySQL query failed:\n\"$sql\"\n".mysql_error());
		die(1);
	}

	return $res;
}

function tc($sql) {
	$row = mysql_fetch_assoc(sql_query($sql));
	return(intval($row['count']));
}

/**
* writes new data to cache, create cache or update existing cache, set current
* time stamp
* @return boolean
*/
function updateCache($stats) {
	$timestamp = time();
	$sql = "insert into `statscache` (`timestamp`, `cache`) values
	('$timestamp', '".mysql_real_escape_string(serialize($stats))."')";
	sql_query($sql);

	// Make sure the new statistic was inserted successfully
	$res = sql_query(
		"select 1 from `statscache` where `timestamp` = '$timestamp'");
	if (mysql_num_rows($res) !== 1) {
		fwrite(STDERR, "Error on inserting the new statistic");
		return false;
	}

	sql_query("delete from `statscache` where `timestamp` != '$timestamp'");
	return true;
}

/**
* get statistics data from live tables, takes a long time so please try to use the
* cache
* @return array
*/
function getDataFromLive() {
	echo "Calculating current statistics\n";

	$stats = array();
	$stats['verified_users'] = number_format(tc(
		"select count(*) as `count` from `users`
			where `verified` = 1
			and `deleted` = 0
			and `locked` = 0"));

	$stats['verified_emails'] = number_format(tc(
		"select count(*) as `count` from `email`
			where `hash` = '' and `deleted` = 0"));

	$stats['verified_domains'] = number_format(tc(
		"select count(*) as `count` from `domains`
			where `hash` = '' and `deleted` = 0"));

	$certs = tc("select count(*) as `count` from `domaincerts`
			where `expire` != 0");
	$certs += tc("select count(*) as `count` from `emailcerts`
			where `expire` != 0");
	$certs += tc("select count(*) as `count` from `gpg`
			where `expire` != 0");
	$certs += tc("select count(*) as `count` from `orgdomaincerts`
			where `expire` != 0");
	$certs += tc("select count(*) as `count` from `orgemailcerts`
			where `expire` != 0");
	$stats['verified_certificates'] = number_format($certs);

	$certs = tc("select count(*) as `count` from `domaincerts`
		where `revoked` = 0 and `expire` > NOW()");
	$certs += tc("select count(*) as `count` from `emailcerts`
		where `revoked` = 0 and `expire` > NOW()");
	$certs += tc("select count(*) as `count` from `gpg`
		where `expire` > NOW()");
	$certs += tc("select count(*) as `count` from `orgdomaincerts`
		where `revoked` = 0 and `expire` > NOW()");
	$certs += tc("select count(*) as `count` from `orgemailcerts`
		where `revoked` = 0 and `expire` > NOW()");
	$stats['valid_certificates'] = number_format($certs);

	$stats['assurances_made'] = number_format(tc(
		"select count(*) as `count` from `notary`
			where (`method` = '' or `method` = 'Face to Face Meeting')
			and `deleted` = 0"));

	$stats['users_1to49'] = number_format(tc(
		"select count(*) as `count` from (
			select 1 from `notary`
				where `deleted` = 0
				group by `to`
				having sum(`points`) > 0 and sum(`points`) < 50
			) as `low_points`"));

	$stats['users_50to99'] = number_format(tc(
		"select count(*) as `count` from (
			select 1 from `notary`
				where `deleted` = 0
				group by `to`
				having sum(`points`) >= 50 and sum(`points`) < 100
			) as `high_points`"));

	$stats['assurer_candidates'] = number_format(tc(
		"select count(*) as `count` from `users`
			where (
				select sum(`points`) from `notary`
					where `to`=`users`.`id`
					and `deleted` = 0
				) >= 100
			and not exists(
				select 1 from `cats_passed` as `cp`, `cats_variant` as `cv`
					where `cp`.`user_id`=`users`.`id`
					and `cp`.`variant_id`=`cv`.`id`
					and `cv`.`type_id`=1
				)"
		));

	$stats['aussurers_with_test'] = number_format(tc(
		"select count(*) as `count` from `users`
			where (
				select sum(`points`) from `notary`
					where `to`=`users`.`id`
					and `deleted` = 0
				) >= 100
			and exists(
				select 1 from `cats_passed` as `cp`, `cats_variant` as `cv`
					where `cp`.`user_id`=`users`.`id`
					and `cp`.`variant_id`=`cv`.`id`
					and `cv`.`type_id`=1
				)"
		));

	$stats['points_issued'] = number_format(tc(
		"select sum(greatest(`points`, `awarded`)) as `count` from `notary`
			where `deleted` = 0
			and `method` = 'Face to Face Meeting'"));

	$totalusers=0;
	$totassurers=0;
	$totalcerts=0;
	for($i = 0; $i < 12; $i++) {
		$first_ts = mktime(0, 0, 0, date("m") - $i, 1, date("Y"));
		$next_month_ts =  mktime(0, 0, 0, date("m") - $i + 1, 1, date("Y"));
		$first = date("Y-m-d", $first_ts);
		$next_month = date("Y-m-d", $next_month_ts);

		echo "Calculating statistics for month $first\n";

		$totalusers += $users = tc(
			"select count(*) as `count` from `users`
				where `created` >= '$first' and `created` < '$next_month'
				and `verified` = 1
				and `deleted` = 0
				and `locked` = 0");

		$totassurers += $assurers = tc(
			"select count(*) as `count` from (
				select 1 from `notary`
					where `when` >= '$first' and `when` < '$next_month'
					and `method`!='Administrative Increase'
					and `deleted` = 0
					group by `to` having sum(`points`) >= 100
				) as `assurer_candidates`");

		$certs = tc(
			"select count(*) as `count` from `domaincerts`
				where `created` >= '$first' and `created` < '$next_month'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `emailcerts`
				where `created` >= '$first' and `created` < '$next_month'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `gpg`
				where `issued` >= '$first' and `issued` < '$next_month'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `orgdomaincerts`
				where `created` >= '$first' and `created` < '$next_month'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `orgemailcerts`
				where `created` >= '$first' and `created` < '$next_month'
				and `expire` != 0");
		$totalcerts += $certs;

		$tmp_arr = array();
		$tmp_arr['date'] = date("Y-m", $first_ts);
		$tmp_arr['new_users'] = number_format($users);
		$tmp_arr['new_assurers'] = number_format($assurers);
		$tmp_arr['new_certificates'] = number_format($certs);

		$stats['growth_last_12m'][] = $tmp_arr;
	}
	$stats['growth_last_12m_total'] = array(
			'new_users' => number_format($totalusers),
			'new_assurers' => number_format($totassurers),
			'new_certificates' => number_format($totalcerts),
		);

	$totalcerts = 0;
	$totalusers = 0;
	$totassurers = 0;
	for($i = date("Y"); $i >= 2002; $i--) {
		$first_ts = mktime(0, 0, 0, 1, 1, $i);
		$next_year_ts =  mktime(0, 0, 0, 1, 1, $i + 1);
		$first = date("Y-m-d", $first_ts);
		$next_year = date("Y-m-d", $next_year_ts);

		echo "Calculating statistics for year $i\n";

		$totalusers += $users = tc(
			"select count(*) as `count` from `users`
				where `created` >= '$first' and `created` < '$next_year'
				and `verified` = 1
				and `deleted` = 0
				and `locked` = 0");

		$totassurers += $assurers = tc(
			"select count(*) as `count` from (
				select 1 from `notary`
					where `when` >= '$first' and `when` < '$next_year'
					and `method`!='Administrative Increase'
					and `deleted` = 0
					group by `to` having sum(`points`) >= 100
				) as `assurer_candidates`");

		$certs = tc(
			"select count(*) as `count` from `domaincerts`
				where `created` >= '$first' and `created` < '$next_year'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `emailcerts`
				where `created` >= '$first' and `created` < '$next_year'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `gpg`
				where `issued` >= '$first' and `issued` < '$next_year'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `orgdomaincerts`
				where `created` >= '$first' and `created` < '$next_year'
				and `expire` != 0");
		$certs += tc(
			"select count(*) as `count` from `orgemailcerts`
				where `created` >= '$first' and `created` < '$next_year'
				and `expire` != 0");
		$totalcerts += $certs;

		$tmp_arr = array();
		$tmp_arr['date'] = $i;
		$tmp_arr['new_users'] = number_format($users);
		$tmp_arr['new_assurers'] = number_format($assurers);
		$tmp_arr['new_certificates'] = number_format($certs);

		$stats['growth_last_years'][] = $tmp_arr;
	}
	$stats['growth_last_years_total'] = array(
			'new_users' => number_format($totalusers),
			'new_assurers' => number_format($totassurers),
			'new_certificates' => number_format($totalcerts),
		);

	return $stats;
}


$stats = getDataFromLive();
if (! updateCache($stats) ) {
	fwrite(STDERR,
		"An error occured. The statistics were not successfully updated!");
	die(1);
}
