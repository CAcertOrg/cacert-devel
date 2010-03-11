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
        loadem("index");
        showheader(_("Welcome to CAcert.org"));

	function tc($sql)
	{
		$row = mysql_fetch_assoc($sql);
		return($row['count']);
	}
?>
<h1>CAcert.org <?=_("Statistics")?></h1>

<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title">CAcert.org <?=_("Statistics")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Verified Users")?>:</td>
    <td class="DataTD"><?=number_format(tc(mysql_query("select count(`id`) as `count` from `users` where `verified`=1")))?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Verified Emails")?>:</td>
    <td class="DataTD"><?=number_format(tc(mysql_query("select count(`id`) as `count` from `email` where `hash`='' and `deleted`=0")))?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Verified Domains")?>:</td>
    <td class="DataTD"><?=number_format(tc(mysql_query("select count(`id`) as `count` from `domains` where `hash`='' and `deleted`=0")))?></td>
  </tr>
<?
	$certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts`"));
	$certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts`"));
	$certs += tc(mysql_query("select count(`id`) as `count` from `gpg`"));
	$certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts`"));
	$certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts`"));
?>
  <tr>
    <td class="DataTD"><?=_("Certificates Issued")?>:</td>
    <td class="DataTD"><?=number_format($certs)?></td>
  </tr>
<?
      $certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts` where `revoked`=0 and `expire`>NOW()"));
      $certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts` where `revoked`=0 and `expire`>NOW()"));
      $certs += tc(mysql_query("select count(`id`) as `count` from `gpg` where `expire`<=NOW()"));
      $certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts` where `revoked`=0 and `expire`>NOW()"));
      $certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts` where `revoked`=0 and `expire`>NOW()"));
      $assurercandidates = tc(mysql_query("select count(*) as `count` from `users` where ".
                                    "not exists(select 1 from `cats_passed` as `cp`, `cats_variant` as `cv` where `cp`.`user_id`=`users`.`id` and `cp`.`variant_id`=`cv`.`id` and `cv`.`type_id`=1) and ".
                                    "(select sum(`points`) from `notary` where `to`=`users`.`id`) >= 100"));
      $realassurers = tc(mysql_query("select count(*) as `count` from `users` where ".
                                    "exists(select 1 from `cats_passed` as `cp`, `cats_variant` as `cv` where `cp`.`user_id`=`users`.`id` and `cp`.`variant_id`=`cv`.`id` and `cv`.`type_id`=1) and ".
                                    "(select sum(`points`) from `notary` where `to`=`users`.`id`) >= 100"));
?>
  <tr>
    <td class="DataTD"><?=_("Valid Certificates")?>:</td>
    <td class="DataTD"><?=number_format($certs)?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Assurances Made")?>:</td>
    <td class="DataTD"><?=number_format(tc(mysql_query("select count(`id`) as `count` from `notary`")))?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Users with 1-49 Points")?>:</td>
    <td class="DataTD"><?=number_format(mysql_num_rows(mysql_query("select `to` from `notary` group by `to` having sum(`points`) > 0 and sum(`points`) < 50")))?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Users with 50-99 Points")?>:</td>
    <td class="DataTD"><?=number_format(mysql_num_rows(mysql_query("select `to` from `notary` group by `to` having sum(`points`) >= 50 and sum(`points`) < 100")))?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Assurer Candidates")?>:</td>
    <td class="DataTD"><?=number_format($assurercandidates)?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Assurers with test")?>:</td>
    <td class="DataTD"><?=number_format($realassurers)?></td>
  </tr>
  <tr><? $drow = mysql_fetch_assoc(mysql_query("select sum(`points`) as `points` from `notary`")); ?>
    <td class="DataTD"><?=_("Points Issued")?>:</td>
    <td class="DataTD"><?=number_format($drow['points'])?></td>
  </tr>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
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
  $totalusers=0;
  $totassurers=0;
  $totalcerts=0;
  for($i = 0; $i < 12; $i++) {
    $date = date("Y-m", mktime(0,0,0,date("m") - $i,1,date("Y")));
    $totalusers += $users = tc(mysql_query("select count(`id`) as `count` from `users` where `created` like '$date%' and `verified`=1"));
    $totassurers += $assurers = mysql_num_rows(mysql_query("select `to` from `notary` where `when` like '$date%' and `method`!='Administrative Increase' group by `to` having sum(`points`) >= 100"));
    $certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts` where `created` like '$date%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts` where `created` like '$date%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `gpg` where `issued` like '$date%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts` where `created` like '$date%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts` where `created` like '$date%'"));
    $totalcerts += $certs;
?>
  <tr>
    <td class="DataTD"><?=$date?></td>
    <td class="DataTD"><?=number_format($users)?></td>
    <td class="DataTD"><?=number_format($assurers)?></td>
    <td class="DataTD"><?=number_format($certs)?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD">N/A</td>
    <td class="DataTD"><?=number_format($totalusers)?></td>
    <td class="DataTD"><?=number_format($totassurers)?></td>
    <td class="DataTD"><?=number_format($totalcerts)?></td>
  </tr>
</table>
<br>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
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
    $totalcerts = $totalusers = $totassurers = 0;
    for($i = date("Y"); $i >= 2002; $i--) {
    $totalusers += $users = tc(mysql_query("select count(`id`) as `count` from `users` where `created` like '$i%' and `verified`=1"));
    $totassurers += $assurers = mysql_num_rows(mysql_query("select `to` from `notary` where `when` like '$i%' and `method`!='Administrative Increase' group by `to` having sum(`points`) >= 100"));
    $certs = tc(mysql_query("select count(`id`) as `count` from `domaincerts` where `created` like '$i%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `emailcerts` where `created` like '$i%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `gpg` where `issued` like '$i%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `orgdomaincerts` where `created` like '$i%'"));
    $certs += tc(mysql_query("select count(`id`) as `count` from `orgemailcerts` where `created` like '$i%'"));
    $totalcerts += $certs;
?>
  <tr>
    <td class="DataTD"><?=$i?></td>
    <td class="DataTD"><?=number_format($users)?></td>
    <td class="DataTD"><?=number_format($assurers)?></td>
    <td class="DataTD"><?=number_format($certs)?></td>
  </tr>
<? } ?>
  <tr>
    <td class="DataTD">N/A</td>
    <td class="DataTD"><?=number_format($totalusers)?></td>
    <td class="DataTD"><?=number_format($totassurers)?></td>
    <td class="DataTD"><?=number_format($totalcerts)?></td>
  </tr>
</table>
<br>
<? showfooter(); ?>

