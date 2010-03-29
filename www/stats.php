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
	define('MAX_CACHE_TTL', 36000);

	loadem("index");
	showheader(_("Welcome to CAcert.org"));

	function tc($sql)
	{
		$row = mysql_fetch_assoc($sql);
		return($row['count']);
	}

	/**
	 * writes new data to cache, create cache or update existing cache, set current
	 * time stamp
	 * @return boolean
	 */
	function updateCache($stats) {
		$sql = 'insert into statscache (timestamp, cache) values ("' . time() . '", ' .
			'"' . mysql_real_escape_string(serialize($stats)) . '")';
		mysql_query($sql);
	}

    /**
     * get statistics data from current cache, return result of getDataFromLive if no cache file exists
     * @return array
     */
	function getData() {
		$sql = 'select * from statscache order by timestamp desc limit 1';
		$res = mysql_query($sql);
		if ($res && mysql_numrows($res) > 0) {
			$ar = mysql_fetch_assoc($res);
			$stats = unserialize($ar['cache']);
			$stats['timestamp'] = $ar['timestamp'];
			if ($ar['timestamp'] + MAX_CACHE_TTL < time())
			{
				$stats=getDataFromLive();
				updateCache($stats);
			}
			return $stats;
		}
		$stats=getDataFromLive();
		updateCache($stats);
		return $stats;
	}

	/**
     * get statistics data from live tables, takes a long time so please try to use the
     * cache
     * @return array
     */
	function getDataFromLive() {
        $stats = array();
		$stats['verified_users'] = number_format(tc(mysql_query("select count(`id`) as `count` from `users` where `verified`=1")));
		$stats['verified_emails'] = number_format(tc(mysql_query("select count(`id`) as `count` from `email` where `hash`='' and `deleted`=0")));
		$stats['verified_domains'] = number_format(tc(mysql_query("select count(`id`) as `count` from `domains` where `hash`='' and `deleted`=0")));
		$certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts`"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts`"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `gpg`"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts`"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts`"));
		$stats['verified_certificates'] = number_format($certs);
		$certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts` where `revoked`=0 and `expire`>NOW()"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts` where `revoked`=0 and `expire`>NOW()"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `gpg` where `expire`<=NOW()"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts` where `revoked`=0 and `expire`>NOW()"));
		$certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts` where `revoked`=0 and `expire`>NOW()"));
		$stats['valid_certificates'] = number_format($certs);
		$stats['assurances_made'] = number_format(tc(mysql_query("select count(`id`) as `count` from `notary`")));
		$stats['users_1to49'] = number_format(mysql_num_rows(mysql_query("select `to` from `notary` group by `to` having sum(`points`) > 0 and sum(`points`) < 50")));
		$stats['users_50to99'] = number_format(mysql_num_rows(mysql_query("select `to` from `notary` group by `to` having sum(`points`) >= 50 and sum(`points`) < 100")));
		$stats['assurer_candidates'] = number_format(tc(mysql_query("select count(*) as `count` from `users` where ".
                                    "not exists(select 1 from `cats_passed` as `cp`, `cats_variant` as `cv` where `cp`.`user_id`=`users`.`id` and `cp`.`variant_id`=`cv`.`id` and `cv`.`type_id`=1) and ".
                                    "(select sum(`points`) from `notary` where `to`=`users`.`id`) >= 100")));
		$stats['aussurers_with_test'] = number_format(tc(mysql_query("select count(*) as `count` from `users` where ".
                                    "exists(select 1 from `cats_passed` as `cp`, `cats_variant` as `cv` where `cp`.`user_id`=`users`.`id` and `cp`.`variant_id`=`cv`.`id` and `cv`.`type_id`=1) and ".
                                    "(select sum(`points`) from `notary` where `to`=`users`.`id`) >= 100")));
		$stats['points_issued'] = number_format(tc(mysql_query("select sum(`points`) as `count` from `notary`")));

		$totalusers=0;
		$totassurers=0;
		$totalcerts=0;
		for($i = 0; $i < 12; $i++) {
			$tmp_arr = array();
			$tmp_arr['date'] = date("Y-m", mktime(0,0,0,date("m") - $i,1,date("Y")));
			$date = date("Y-m", mktime(0,0,0,date("m") - $i,1,date("Y")));
			$totalusers += $users = tc(mysql_query("select count(`id`) as `count` from `users` where `created` like '$date%' and `verified`=1"));
			$totassurers += $assurers = mysql_num_rows(mysql_query("select `to` from `notary` where `when` like '$date%' and `method`!='Administrative Increase' group by `to` having sum(`points`) >= 100"));
			$certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts` where `created` like '$date%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts` where `created` like '$date%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `gpg` where `issued` like '$date%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts` where `created` like '$date%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts` where `created` like '$date%'"));
			$totalcerts += $certs;

			$tmp_arr['new_users'] = number_format($users);
			$tmp_arr['new_assurers'] = number_format($assurers);
			$tmp_arr['new_certificates'] = number_format($certs);

			$stats['growth_last_12m'][] = $tmp_arr;
		}
		$stats['growth_last_12m_total'] = array('new_users' => number_format($totalusers),
												'new_assurers' => number_format($totassurers),
												'new_certificates' => number_format($totalcerts));

		$totalcerts = 0;
		$totalusers = 0;
		$totassurers = 0;
		for($i = date("Y"); $i >= 2002; $i--) {
			$tmp_arr = array();
			$tmp_arr['date'] = $i;
			$totalusers += $users = tc(mysql_query("select count(`id`) as `count` from `users` where `created` like '$i%' and `verified`=1"));
			$totassurers += $assurers = mysql_num_rows(mysql_query("select `to` from `notary` where `when` like '$i%' and `method`!='Administrative Increase' group by `to` having sum(`points`) >= 100"));
			$certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts` where `created` like '$i%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts` where `created` like '$i%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `gpg` where `issued` like '$i%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts` where `created` like '$i%'"));
			$certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts` where `created` like '$i%'"));
			$totalcerts += $certs;

			$tmp_arr['new_users'] = number_format($users);
			$tmp_arr['new_assurers'] = number_format($assurers);
			$tmp_arr['new_certificates'] = number_format($certs);

			$stats['growth_last_years'][] = $tmp_arr;
		}
		$stats['growth_last_years_total'] = array('new_users' => number_format($totalusers),
												  'new_assurers' => number_format($totassurers),
												  'new_certificates' => number_format($totalcerts));

		return $stats;
	}

	$stats = getData();
?>
<h1>CAcert.org <?=_("Statistics")?></h1>

<table align="center" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title">CAcert.org <?=_("Statistics")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Verified Users")?>:</td>
    <td class="DataTD"><?=$stats['verified_users'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Verified Emails")?>:</td>
    <td class="DataTD"><?=$stats['verified_emails'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Verified Domains")?>:</td>
    <td class="DataTD"><?=$stats['verified_domains'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Certificates Issued")?>:</td>
    <td class="DataTD"><?=$stats['verified_certificates'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Valid Certificates")?>:</td>
    <td class="DataTD"><?=$stats['valid_certificates'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Assurances Made")?>:</td>
    <td class="DataTD"><?=$stats['assurances_made'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Users with 1-49 Points")?>:</td>
    <td class="DataTD"><?=$stats['users_1to49'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Users with 50-99 Points")?>:</td>
    <td class="DataTD"><?=$stats['users_50to99'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Assurer Candidates")?>:</td>
    <td class="DataTD"><?=$stats['assurer_candidates'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Assurers with test")?>:</td>
    <td class="DataTD"><?=$stats['aussurers_with_test'];?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Points Issued")?>:</td>
    <td class="DataTD"><?=$stats['points_issued'];?></td>
  </tr>
</table>
<br>
<table align="center" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="4" class="title">CAcert.org <?=_("Growth in the last 12 months")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Date")?></b>
    <td class="DataTD"><b><?=_("New Users")?></b>
    <td class="DataTD"><b><?=_("New Assurers")?></b>
    <td class="DataTD"><b><?=_("New Certificates")?></b>
  </tr>
<?
	for($i = 0; $i < 12; $i++) {
?>
  <tr>
    <td class="DataTD"><?=$stats['growth_last_12m'][$i]['date'];?></td>
    <td class="DataTD"><?=$stats['growth_last_12m'][$i]['new_users'];?></td>
    <td class="DataTD"><?=$stats['growth_last_12m'][$i]['new_assurers'];?></td>
    <td class="DataTD"><?=$stats['growth_last_12m'][$i]['new_certificates'];?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD">N/A</td>
    <td class="DataTD"><?=$stats['growth_last_12m_total']['new_users'];?></td>
    <td class="DataTD"><?=$stats['growth_last_12m_total']['new_assurers'];?></td>
    <td class="DataTD"><?=$stats['growth_last_12m_total']['new_certificates'];?></td>
  </tr>
</table>
<br>
<table align="center" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="4" class="title">CAcert.org <?=_("Growth by year")?></td>
  </tr>
  <tr>
    <td class="DataTD"><b><?=_("Date")?></b>
    <td class="DataTD"><b><?=_("New Users")?></b>
    <td class="DataTD"><b><?=_("New Assurers")?></b>
    <td class="DataTD"><b><?=_("New Certificates")?></b>
  </tr>
<?
	for($i = 0; $i < count($stats['growth_last_years']); $i++) {
?>
  <tr>
    <td class="DataTD"><?=$stats['growth_last_years'][$i]['date'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years'][$i]['new_users'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years'][$i]['new_assurers'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years'][$i]['new_certificates'];?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD">N/A</td>
    <td class="DataTD"><?=$stats['growth_last_years_total']['new_users'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years_total']['new_assurers'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years_total']['new_certificates'];?></td>
  </tr>
</table>
<br>
<?php
	if (isset($stats['timestamp'])) {
?>
<div style="text-align: center;font-size: small;"><?=_("Statistical data from cache, created at ") . date('Y-m-d H:i:s', $stats['timestamp']);?></div>
<?php
	}
?>
<? showfooter(); ?>

