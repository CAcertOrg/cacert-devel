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

	$startdate = date("Y-m-d", mktime(0, 0, 0, 1, 1, 2002));
	$enddate = date("Y-m-d", mktime(0, 0, 0, 1, 1, date("Y") + 1));

	$stats['aussurers_with_test'] = number_format(assurer_count($startdate, $enddate,1));

	$stats['assurer_candidates'] = number_format(assurer_count($startdate, $enddate,0) - $stats['aussurers_with_test']);


	$stats['points_issued'] = number_format(tc(
		"select sum(greatest(`points`, `awarded`)) as `count` from `notary`
			where `deleted` = 0
			and `method` = 'Face to Face Meeting'"));

	$totalcerts = 0;
	$totalusers = 0;
	$totalcandidates = 0;
	$totalassurers = 0;

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

		$totalcandidates += $candidates = assurer_count($first, $next_month, 0);
		$totalassurers += $assurers = assurer_count($first, $next_month, 1);

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
		$tmp_arr['new_candidates'] = number_format($candidates);
		$tmp_arr['new_assurers'] = number_format($assurers);
		$tmp_arr['new_certificates'] = number_format($certs);

		$stats['growth_last_12m'][] = $tmp_arr;
	}
	$stats['growth_last_12m_total'] = array(
			'new_users' => number_format($totalusers),
			'new_candidates' => number_format($totalcandidates),
			'new_assurers' => number_format($totalassurers),
			'new_certificates' => number_format($totalcerts),
		);

	$totalcerts = 0;
	$totalusers = 0;
	$totalcandidates = 0;
	$totalassurers = 0;

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

		$totalcandidates += $candidates = assurer_count($first, $next_year, 0);
		$totalassurers += $assurers = assurer_count($first, $next_year, 1);

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
		$tmp_arr['new_candidates'] = number_format($candidates);
		$tmp_arr['new_assurers'] = number_format($assurers);
		$tmp_arr['new_certificates'] = number_format($certs);

		$stats['growth_last_years'][] = $tmp_arr;
	}
	$stats['growth_last_years_total'] = array(
			'new_users' => number_format($totalusers),
			'new_candidates' => number_format($totalcandidates),
			'new_assurers' => number_format($totalassurers),
			'new_certificates' => number_format($totalcerts),
		);

	return $stats;
}

/**
 * assurer_count()
 *   returns the number of new assurer in period given through from and to, type is defining the CATS type that is used as definition to be assurer
 * @param mixed $from
 * @param mixed $to
 * @param integer $type
 * @return
 */
function assurer_count($from, $to, $type = 1){
    if ($type == 0) {
        $atype = "";
        $btype = "";
    } else {
        $atype = " AND n.`to` in (SELECT c.user_id FROM cats_passed as c, cats_variant as v WHERE c.variant_id = v.id and v.type_id = $type and pass_date < @a) ";
        $btype = " AND n.`to` in (SELECT c.user_id FROM cats_passed as c, cats_variant as v WHERE c.variant_id = v.id and v.type_id = $type and pass_date < @b) ";
    }

    $query1 = "SET @a = '$from';";

    $query2 = "SET @b = '$to';";

    $query3 = "CREATE TEMPORARY TABLE a
            SELECT n.`to`, sum(n.awarded) as `received_pts`, max(n.`when`) as `last_assurance`
            FROM cacert.notary as n
            WHERE 1
                AND n.`from` != n.`to`
                AND (n.`deleted` = '0000-00-00 00:00:00' OR n.`deleted` >= @a)
                AND n.`when` < @a
                $atype
            GROUP by n.`to`
            HAVING 1
                AND `received_pts` >= 100
            ORDER by `last_assurance` DESC;";

    $query4 = "CREATE TEMPORARY TABLE b
            SELECT n.`to`, sum(n.awarded) as `received_pts`, max(n.`when`) as `last_assurance`
            FROM cacert.notary as n
            WHERE 1
                AND n.`from` != n.`to`
                AND (n.`deleted` = '0000-00-00 00:00:00' OR n.`deleted` >= @b)
                AND n.`when` < @b
                $btype
            GROUP by n.`to`
            HAVING 1
                AND `received_pts` >= 100
            ORDER by `last_assurance` DESC;";

    $query5 = "SELECT count(*) as `count` FROM b WHERE b.`to` NOT IN (SELECT a.`to` FROM a);";

    $query6 = "DROP TEMPORARY TABLE a;";

    $query7 = "DROP TEMPORARY TABLE b;";


    sql_query($query1);
    sql_query($query2);
    sql_query($query3);
    sql_query($query4);

    $row = mysql_fetch_assoc(sql_query($query5));

    sql_query($query6);
    sql_query($query7);

    return(intval($row['count']));
}


$stats = getDataFromLive();
if (! updateCache($stats) ) {
	fwrite(STDERR,
		"An error occured. The statistics were not successfully updated!");
	die(1);
}
