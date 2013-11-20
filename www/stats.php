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

    /**
     * get statistics data from current cache, return result of getDataFromLive if no cache file exists
     * @return array
     */
	function getData() {
		$sql = 'select * from `statscache` order by `timestamp` desc limit 1';
		$res = mysql_query($sql);
		if ($res && mysql_numrows($res) > 0) {
			$ar = mysql_fetch_assoc($res);
			$stats = unserialize($ar['cache']);
			$stats['timestamp'] = $ar['timestamp'];
			return $stats;
		}

		return null;
	}

	$stats = getData();
	if ($stats === null) {
		echo '<p>', _("Error while retrieving the statistics!"), '</p>';
		showfooter();
		die();
	}
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
    <td class="DataTD"><?=_("Total")?></td>
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
    <td class="DataTD"><?=_("Total")?></td>
    <td class="DataTD"><?=$stats['growth_last_years_total']['new_users'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years_total']['new_assurers'];?></td>
    <td class="DataTD"><?=$stats['growth_last_years_total']['new_certificates'];?></td>
  </tr>
</table>
<br>

<div style="text-align: center;font-size: small;"><?
	printf(_("Last updated: %s"), date('Y-m-d H:i:s', $stats['timestamp']));?>
</div>

<? showfooter(); ?>
