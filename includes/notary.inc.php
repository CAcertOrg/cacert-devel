<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2011  CAcert Inc.

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

define('NULL_DATETIME', '0000-00-00 00:00:00');
define('THAWTE_REVOCATION_DATETIME', '2010-11-16 00:00:00');

	function query_init ($query)
	{
		return mysql_query($query);
	}

	function query_getnextrow ($res)
	{
		$row1 = mysql_fetch_assoc($res);
		return $row1;
	}

	function query_get_number_of_rows ($resultset)
	{
		return intval(mysql_num_rows($resultset));
	}

	function get_number_of_assurances ($userid)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE `method` = 'Face to Face Meeting' AND `from`='".intval($userid)."' and `deleted` = 0");
		$row = query_getnextrow($res);

		return intval($row['list']);
	}

	function get_number_of_ttpassurances ($userid)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE (`method`='Trusted Third Parties' or `method`='TTP-Assisted') AND `to`='".intval($userid)."' and `deleted` = 0");
		$row = query_getnextrow($res);

		return intval($row['list']);
	}

	function get_number_of_assurees ($userid)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE `method` = 'Face to Face Meeting' AND `to`='".intval($userid)."' and `deleted` = 0");
		$row = query_getnextrow($res);

		return intval($row['list']);
	}

	function get_top_assurer_position ($no_of_assurances)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE `method` = 'Face to Face Meeting' and `deleted` = 0
			GROUP BY `from` HAVING count(*) > '".intval($no_of_assurances)."'");
		return intval(query_get_number_of_rows($res)+1);
	}

	function get_top_assuree_position ($no_of_assurees)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE `method` = 'Face to Face Meeting' and `deleted` = 0
			GROUP BY `to` HAVING count(*) > '".intval($no_of_assurees)."'");
		return intval(query_get_number_of_rows($res)+1);
	}

	/**
	 * Get the list of assurances given by the user
	 * @param int $userid - id of the assurer
	 * @param int $log - if set to 1 also includes deleted assurances
	 * @return resource - a MySQL result set
	 */
	function get_given_assurances($userid, $log=0)
	{
		$deleted='';
		if ($log == 0) {
			$deleted = ' and `deleted` = 0 ';
		}
		$res = query_init("select * from `notary` where `from`='".intval($userid)."' and `from` != `to` $deleted order by `id` asc");
		return $res;
	}

	/**
	 * Get the list of assurances received by the user
	 * @param int $userid - id of the assuree
	 * @param int $log - if set to 1 also includes deleted assurances
	 * @return resource - a MySQL result set
	 */
	function get_received_assurances($userid, $log=0)
	{
		$deleted='';
		if ($log == 0) {
			$deleted = ' and `deleted` = 0 ';
		}
		$res = query_init("select * from `notary` where `to`='".intval($userid)."' and `from` != `to` $deleted order by `id` asc  ");
		return $res;
	}

	function get_given_assurances_summary ($userid)
	{
		$res = query_init ("select count(*) as number,points,awarded,method from notary where `from`='".intval($userid)."' and `deleted` = 0 group by points,awarded,method");
		return $res;
	}

	function get_received_assurances_summary ($userid)
	{
		$res = query_init ("select count(*) as number,points,awarded,method from notary where `to`='".intval($userid)."' and `deleted` = 0 group by points,awarded,method");
		return $res;
	}

	function get_user ($userid)
	{
		$res = query_init ("select * from `users` where `id`='".intval($userid)."'");
		return mysql_fetch_assoc($res);
	}

	function get_cats_state ($userid)
	{

		$res = query_init ("select * from `cats_passed` inner join `cats_variant` on `cats_passed`.`variant_id` = `cats_variant`.`id` and `cats_variant`.`type_id` = 1
			WHERE `cats_passed`.`user_id` = '".intval($userid)."'");
		return mysql_num_rows($res);
	}


	/**
	 * Calculate awarded points (corrects some issues like out of range points
	 * or points that were issued by means that have been deprecated)
	 *
	 * @param array $row - associative array containing the data from the
	 *     `notary` table
	 * @return int - the awarded points for this assurance
	 */
	function calc_awarded($row)
	{
		// Back in the old days there was no `awarded` column => is now zero,
		// there the `points` column contained that data
		$points = max(intval($row['awarded']), intval($row['points']));

		// Set negative points to zero, yes there are such things in the database
		$points = max($points, 0);

		switch ($row['method'])
		{
			// These programmes have been revoked
			case 'Thawte Points Transfer':	  // revoke all Thawte-points     (as per arbitration)
			case 'CT Magazine - Germany':	   // revoke c't		   (only one test-entry)
			case 'Temporary Increase':	      // revoke 'temporary increase'  (Current usage breaks audit aspects, needs to be reimplemented)
				$points = 0;
				break;

			case 'Administrative Increase':	 // ignore AI with 2 points or less (historical for experiance points, now other calculation)
				if ($points <= 2)	       // maybe limit to 35/50 pts in the future?
					$points = 0;
				break;

			// TTP assurances, limit to 35
			case 'TTP-Assisted':
				$points = min($points, 35);
				break;

				// TTP TOPUP, limit to 30
			case 'TOPUP':
				$points = min($points, 30);

			// All these should be preserved for the time being
			case 'Unknown':			 // to be revoked in the future? limit to max 50 pts?
			case 'Trusted Third Parties':	     // to be revoked in the future? limit to max 35 pts?
			case '':				// to be revoked in the future? limit to max 50 pts?
			case 'Face to Face Meeting': // normal assurances (and superassurances?), limit to 35/50 pts in the future?
				break;

			default:				// should never happen ... ;-)
				$points = 0;
		}

		return $points;
	}


	/**
	 * Calculate the experience points from a given Assurance
	 * @param array  $row - [inout] associative array containing the data from
	 *     the `notary` table, the keys 'experience' and 'calc_awarded' will be
	 *     added
	 * @param int    $sum_points - [inout] the sum of already counted assurance
	 *     points the assurer issued
	 * @param int    $sum_experience - [inout] the sum of already counted
	 *     experience points that were awarded to the assurer
	 */
	function calc_experience(&$row, &$sum_points, &$sum_experience)
	{
		$row['calc_awarded'] = calc_awarded($row);

		// Don't count revoked assurances even if we are displaying them
		if ($row['deleted'] !== NULL_DATETIME) {
			$row['experience'] = 0;
			return;
		}

		$experience = 0;
		if ($row['method'] == "Face to Face Meeting")
		{
			$experience = 2;
		}
		$sum_experience += $experience;
		$row['experience'] = $experience;

		$sum_points += $row['calc_awarded'];
	}

	/**
	 * Calculate the points received from a received Assurance
	 * @param array  $row - [inout] associative array containing the data from
	 *     the `notary` table, the keys 'experience' and 'calc_awarded' will be
	 *     added
	 * @param int    $sum_points - [inout] the sum of already counted assurance
	 *     points the assuree received
	 * @param int    $sum_experience - [inout] the sum of already counted
	 *     experience points that were awarded to the assurer
	 */
	function calc_assurances(&$row, &$sum_points, &$sum_experience)
	{
		$row['calc_awarded'] = calc_awarded($row);
		$experience = 0;

		// High point values mean that some of them are experience points
		if ($row['calc_awarded'] > 100)
		{
			$experience = $row['calc_awarded'] - 100;		// needs to be fixed in the future (limit 50 pts and/or no experience if pts > 100)
			$row['calc_awarded'] = 100;
		}

		switch ($row['method'])
		{
			case 'Thawte Points Transfer':
			case 'CT Magazine - Germany':
			case 'Temporary Increase':	      // Current usage of 'Temporary Increase' may break audit aspects, needs to be reimplemented
				$experience = 0;
				$row['deleted'] = THAWTE_REVOCATION_DATETIME;
				break;
		}

		// Don't count revoked assurances even if we are displaying them
		if ($row['deleted'] !== NULL_DATETIME) {
			$row['experience'] = 0;
			return;
		}

		$sum_experience += $experience;
		$row['experience'] = $experience;
		$sum_points += $row['calc_awarded'];
	}

	/**
	 * Generate a link to the support engineer page for the user with the name
	 * of the user as link text
	 * @param array $user - associative array containing the data from the
	 *     `user` table
	 * @return string
	 */
	function show_user_link($user)
	{
		$name = trim($user['fname'].' '.$user['lname']);
		$userid = intval($user['id']);

		if($name == "")
		{
			if ($userid == 0) {
				$name = _("System");
			} else {
				$name = _("Deleted account");
			}
		}
		else
		{
			$name = "<a href='wot.php?id=9&amp;userid=".$userid."'>".sanitizeHTML($name)."</a>";
		}

		return $name;
	}

	/**
	 * Generate a link to the support engineer page for the user with the email
	 * address as link text
	 * @param array $user - associative array containing the data from the
	 *     `user` table
	 * @return string
	 */
	function show_email_link($user)
	{
		$email = trim($user['email']);
		if($email != "") {
			$email = "<a href='account.php?id=43&amp;userid=".intval($user['id'])."'>".sanitizeHTML($email)."</a>";
		}
		return $email;
	}

	function get_assurer_ranking($userid,&$num_of_assurances,&$rank_of_assurer)
	{
		$num_of_assurances = get_number_of_assurances (intval($userid));
		$rank_of_assurer = get_top_assurer_position($num_of_assurances);
	}

	function get_assuree_ranking($userid,&$num_of_assurees,&$rank_of_assuree)
	{
		$num_of_assurees = get_number_of_assurees (intval($userid));
		$rank_of_assuree = get_top_assuree_position($num_of_assurees);
	}


// ************* html table definitions ******************

	function output_ranking($userid)
	{
		get_assurer_ranking($userid,$num_of_assurances,$rank_of_assurer);
		get_assuree_ranking($userid,$num_of_assurees,$rank_of_assuree);

?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
    	<td class="title"><?=_("Assurer Ranking")?></td>
    </tr>
    <tr>
	<td class="DataTD"><?=sprintf(_("You have made %s assurances which ranks you as the #%s top assurer."), intval($num_of_assurances), intval($rank_of_assurer) )?></td>
    </tr>
    <tr>
	<td class="DataTD"><?=sprintf(_("You have received %s assurances which ranks you as the #%s top assuree."), intval($num_of_assurees), intval($rank_of_assuree) )?></td>
    </tr>
</table>
<br/>
<?
	}

	/**
	 * Render header for the assurance table (same for given/received)
	 * @param string $title - The title for the table
	 * @param int    $support - set to 1 if the output is for the support interface
	 * @param int    $log - if set to 1 also includes deleted assurances
	 */
	function output_assurances_header($title, $support, $log)
	{
		if ($support == 1) {
			$log = 1;
		}

		$colspan = 7;
		if ($support == 1) {
			$colspan += 2;
		}
		if ($log == 1) {
			$colspan += 1;
		}
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
	<tr>
		<td colspan="<?=$colspan?>" class="title"><?=$title?></td>
	</tr>
	<tr>
		<td class="DataTD"><strong><?=_("ID")?></strong></td>
		<td class="DataTD"><strong><?=_("Date")?></strong></td>
<?
		if ($support == 1)
		{
?>
    	<td class="DataTD"><strong><?=_("When")?></strong></td>
    	<td class="DataTD"><strong><?=_("Email")?></strong></td>
<?
		}
?>
    	<td class="DataTD"><strong><?=_("Who")?></strong></td>
    	<td class="DataTD"><strong><?=_("Points")?></strong></td>
    	<td class="DataTD"><strong><?=_("Location")?></strong></td>
    	<td class="DataTD"><strong><?=_("Method")?></strong></td>
    	<td class="DataTD"><strong><?=_("Experience Points")?></strong></td>
<?
		if ($log == 1)
		{
?>
		<td class="DataTD"><strong><?=_("Revoked")?></strong></td>
<?
		}
?>
    </tr>
<?
	}

	/**
	 * Render footer for the assurance table (same for given/received)
	 * @param string $points_txt - Description for sum of assurance points
	 * @param int    $sumpoints - sum of assurance points
	 * @param string $experience_txt - Description for sum of experience points
	 * @param int    $sumexperience - sum of experience points
	 * @param int    $support - set to 1 if the output is for the support interface
	 * @param int    $log - if set to 1 also includes deleted assurances
	 */
	function output_assurances_footer(
			$points_txt,
			$sumpoints,
			$experience_txt,
			$sumexperience,
			$support,
			$log)
	{
?>
	<tr>
		<td colspan="<?=($support == 1) ? 5 : 3 ?>" class="DataTD"><strong><?=$points_txt?>:</strong></td>
		<td class="DataTD"><?=intval($sumpoints)?></td>
		<td class="DataTD">&nbsp;</td>
		<td class="DataTD"><strong><?=$experience_txt?>:</strong></td>
		<td class="DataTD"><?=intval($sumexperience)?></td>
<?
		if ($log == 1)
		{
?>
    	<td class="DataTD">&nbsp;</td>
<?
		}
?>
	</tr>
</table>
<br/>
<?
	}

	/**
	 * Render an assurance for a view
	 * @param array   $assurance - associative array containing the data from the `notary` table
	 * @param int     $userid - Id of the user whichs given/received assurances are displayed
	 * @param array   $other_user - associative array containing the other users data from the `users` table
	 * @param int     $support - set to 1 if the output is for the support interface
	 * @param string  $ticketno - ticket number currently set in the support interface
	 * @param int     $log - if set to 1 also includes deleted assurances
	 */
	function output_assurances_row(
			$assurance,
			$userid,
			$other_user,
			$support,
			$ticketno,
			$log)
	{
		$assuranceid = intval($assurance['id']);
		$date = $assurance['date'];
		$when = $assurance['when'];
		$awarded = intval($assurance['calc_awarded']);
		$points = intval($assurance['points']);
		$location = $assurance['location'];
		$method = $assurance['method'] ? _($assurance['method']) : '';
		$experience = intval($assurance['experience']);
		$revoked = $assurance['deleted'] !== NULL_DATETIME;

		$email = show_email_link($other_user);
		$name = show_user_link($other_user);

		if ($support == 1) {
			$log = 1;
		}

		$tdstyle="";
		$emopen="";
		$emclose="";

		if ($awarded == $points)
		{
			if ($awarded == 0)
			{
				if ($when < "2006-09-01")
				{
					$tdstyle="style='background-color: #ffff80'";
					$emopen="<em>";
					$emclose="</em>";
				}
			}
		}
?>
	<tr>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$assuranceid?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$date?><?=$emclose?></td>
<?
		if ($support == 1)
		{
?>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$when?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$email?><?=$emclose?></td>
<?
		}
?>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$name?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$revoked ? sprintf("<strong style='color: red'>%s</strong>",_("Revoked")) : $awarded?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$location?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$method?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$experience?$experience:'&nbsp;'?><?=$emclose?></td>
<?
		if ($log == 1)
		{
			if ($revoked == true)
			{
?>
		<td class="DataTD" <?=$tdstyle?>><?=$assurance['deleted']?></td>
<?
			} elseif ($support == 1) {
?>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><a href="account.php?id=43&amp;userid=<?=intval($userid)?>&amp;assurance=<?=intval($assuranceid)?>&amp;csrf=<?=make_csrf('admdelassurance')?>&amp;ticketno=<?=sanitizeHTML($ticketno)?>" onclick="return confirm('<?=sprintf(_("Are you sure you want to revoke the assurance with ID &quot;%s&quot;?"),$assuranceid)?>');"><?=_("Revoke")?></a><?=$emclose?></td>
<?
			} else {
?>
		<td class="DataTD" <?=$tdstyle?>>&nbsp;</td>
<?
			}
		}
?>
	</tr>
<?
	}

	function output_summary_header()
	{
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
	<td colspan="4" class="title"><?=_("Summary of your Points")?></td>
    </tr>
    <tr>
	<td class="DataTD"><strong><?=_("Description")?></strong></td>
	<td class="DataTD"><strong><?=_("Points")?></strong></td>
	<td class="DataTD"><strong><?=_("Countable Points")?></strong></td>
	<td class="DataTD"><strong><?=_("Remark")?></strong></td>
    </tr>
<?
	}

	function output_summary_footer()
	{
?>
</table>
<br/>
<?
	}

	function output_summary_row($title,$points,$points_countable,$remark)
	{
?>
    <tr>
	<td class="DataTD"><strong><?=$title?></strong></td>
	<td class="DataTD"><?=$points?></td>
	<td class="DataTD"><?=$points_countable?></td>
	<td class="DataTD"><?=$remark?></td>
    </tr>
<?
	}


// ************* output given assurances ******************

	/**
	 * Helper function to render assurances given by the user
	 * @param int  $userid
	 * @param int& $sum_points - [out] sum of given points
	 * @param int& $sum_experience - [out] sum of experience points gained
	 * @param int  $support - set to 1 if the output is for the support interface
	 * @param string $ticketno - the ticket number set in the support interface
	 * @param int  $log - if set to 1 also includes deleted assurances
	 */
	function output_given_assurances_content(
			$userid,
			&$sum_points,
			&$sum_experience,
			$support,
			$ticketno,
			$log)
	{
		$sum_points = 0;
		$sumexperience = 0;
		$res = get_given_assurances(intval($userid), $log);
		while($row = mysql_fetch_assoc($res))
		{
			$assuree = get_user(intval($row['to']));
			calc_experience($row, $sum_points, $sum_experience);
			output_assurances_row($row, $userid, $assuree, $support, $ticketno, $log);
		}
	}

// ************* output received assurances ******************

	/**
	 * Helper function to render assurances received by the user
	 * @param int  $userid
	 * @param int& $sum_points - [out] sum of received points
	 * @param int& $sum_experience - [out] sum of experience points the assurers gained
	 * @param int  $support - set to 1 if the output is for the support interface
	 * @param string $ticketno - the ticket number set in the support interface
	 * @param int  $log - if set to 1 also includes deleted assurances
	 */
	function output_received_assurances_content(
			$userid,
			&$sum_points,
			&$sum_experience,
			$support,
			$ticketno,
			$log)
	{
		$sum_points = 0;
		$sumexperience = 0;
		$res = get_received_assurances(intval($userid), $log);
		while($row = mysql_fetch_assoc($res))
		{
			$fromuser = get_user(intval($row['from']));
			calc_assurances($row, $sum_points, $sum_experience);
			output_assurances_row($row, $userid, $fromuser, $support, $ticketno, $log);
		}
	}

// ************* output summary table ******************

	function check_date_limit ($userid,$age)
	{
		$dob = date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")-$age));
		$res = query_init ("select id from `users` where `id`='".$userid."' and `dob` < '$dob'");
		return intval(query_get_number_of_rows($res));
	}

	function max_points($userid)
	{
		return output_summary_content ($userid,0);
	}

	function output_summary_content($userid,$display_output)
	{
		$sum_points = 0;
		$sum_experience = 0;
		$sum_experience_other = 0;
		$max_points = 100;
		$max_experience = 50;

		$experience_limit_reached_txt = _("Limit reached");

		if (check_date_limit($userid,18) != 1)
		{
			$max_experience = 10;
			$experience_limit_reached_txt = _("Limit given by PoJAM reached");
		}
		if (check_date_limit($userid,14) != 1)
		{
			$max_experience = 0;
			$experience_limit_reached_txt = _("Limit given by PoJAM reached");
		}

		$res = get_received_assurances_summary($userid);
		while($row = mysql_fetch_assoc($res))
		{
			$points = calc_awarded($row);

			if ($points > $max_points)			// limit to 100 points, above is experience (needs to be fixed)
			{
				$sum_experience_other = $sum_experience_other+($points-$max_points)*intval($row['number']);
				$points = $max_points;
			}
			$sum_points += $points*intval($row['number']);
		}

		$res = get_given_assurances_summary($userid);
		while($row = mysql_fetch_assoc($res))
		{
			switch ($row['method'])
			{
				case 'Face to Face Meeting':	 		// count Face to Face only
					$sum_experience += 2*intval($row['number']);
					break;
			}

		}

		if ($sum_points > $max_points)
			{
			$sum_points_countable = $max_points;
			$remark_points = _("Limit reached");
			}
		else
			{
			$sum_points_countable = $sum_points;
			$remark_points = "&nbsp;";
			}
		if ($sum_experience > $max_experience)
			{
			$sum_experience_countable = $max_experience;
			$remark_experience = $experience_limit_reached_txt;
			}
		else
			{
			$sum_experience_countable = $sum_experience;
			$remark_experience = "&nbsp;";
			}

		if ($sum_experience_countable + $sum_experience_other > $max_experience)
			{
			$sum_experience_other_countable = $max_experience-$sum_experience_countable;
			$remark_experience_other = $experience_limit_reached_txt;
			}
		else
			{
			$sum_experience_other_countable = $sum_experience_other;
			$remark_experience_other = "&nbsp;";
			}

		if ($sum_points_countable < $max_points)
			{
			if ($sum_experience_countable != 0)
				$remark_experience = _("Points on hold due to less assurance points");
			$sum_experience_countable = 0;
			if ($sum_experience_other_countable != 0)
				$remark_experience_other = _("Points on hold due to less assurance points");
			$sum_experience_other_countable = 0;
			}

		$issue_points = 0;
		$cats_test_passed = get_cats_state ($userid);
		if ($cats_test_passed == 0)
		{
			$issue_points_txt = "<strong style='color: red'>"._("You have to pass the CAcert Assurer Challenge (CATS-Test) to be an Assurer")."</strong>";
			if ($sum_points_countable < $max_points)
			{
				$issue_points_txt = "<strong style='color: red'>";
				$issue_points_txt .= sprintf(_("You need %s assurance points and the passed CATS-Test to be an Assurer"), intval($max_points));
				$issue_points_txt .= "</strong>";
			}
		}
		else
		{
			$experience_total = $sum_experience_countable+$sum_experience_other_countable;
			$issue_points_txt = "";
			if ($sum_points_countable == $max_points)
				$issue_points = 10;
			if ($experience_total >= 10)
				$issue_points = 15;
			if ($experience_total >= 20)
				$issue_points = 20;
			if ($experience_total >= 30)
				$issue_points = 25;
			if ($experience_total >= 40)
				$issue_points = 30;
			if ($experience_total >= 50)
				$issue_points = 35;
			if ($issue_points != 0)
				$issue_points_txt = sprintf(_("You may issue up to %s points"),$issue_points);
		}
		if ($display_output)
		{
			output_summary_row (_("Assurance Points you received"),$sum_points,$sum_points_countable,$remark_points);
			output_summary_row (_("Total Experience Points by Assurance"),$sum_experience,$sum_experience_countable,$remark_experience);
			output_summary_row (_("Total Experience Points (other ways)"),$sum_experience_other,$sum_experience_other_countable,$remark_experience_other);
			output_summary_row (_("Total Points"),"&nbsp;",$sum_points_countable + $sum_experience_countable + $sum_experience_other_countable,$issue_points_txt);
		}
		return $issue_points;
	}

	/**
	 * Render assurances given by the user
	 * @param int $userid
	 * @param int $support - set to 1 if the output is for the support interface
	 * @param string $ticketno - the ticket number set in the support interface
	 * @param int $log - if set to 1 also includes deleted assurances
	 */
	function output_given_assurances($userid, $support=0, $ticketno='', $log=0)
	{
		output_assurances_header(
				_("Assurance Points You Issued"),
				$support,
				$log);

		output_given_assurances_content(
				$userid,
				$sum_points,
				$sum_experience,
				$support,
				$ticketno,
				$log);

		output_assurances_footer(
				_("Total Points Issued"),
				$sum_points,
				_("Total Experience Points"),
				$sum_experience,
				$support,
				$log);
	}

	/**
	 * Render assurances received by the user
	 * @param int $userid
	 * @param int $support - set to 1 if the output is for the support interface
	 * @param string $ticketno - the ticket number set in the support interface
	 * @param int $log - if set to 1 also includes deleted assurances
	 */
	function output_received_assurances($userid, $support=0, $ticketno='', $log=0)
	{
		output_assurances_header(
				_("Assurance Points You Received"),
				$support,
				$log);

		output_received_assurances_content(
				$userid,
				$sum_points,
				$sum_experience,
				$support,
				$ticketno,
				$log);

		output_assurances_footer(
				_("Total Points Received"),
				$sum_points,
				_("Total Experience Points"),
				$sum_experience,
				$support,
				$log);
	}

	function output_summary($userid)
	{
		output_summary_header();
		output_summary_content($userid,1);
		output_summary_footer();
	}

	function output_end_of_page()
	{
?>
	<p>[ <a href='javascript:history.go(-1)'><?=_("Go Back")?></a> ]</p>
<?
	}

	//functions to do with recording user agreements
	/**
	 * write_user_agreement()
	 * writes a new record to the table user_agreement
	 *
	 * @param mixed $memid
	 * @param mixed $document
	 * @param mixed $method
	 * @param mixed $comment
	 * @param integer $active
	 * @param integer $secmemid
	 * @return
	 */
	function write_user_agreement($memid, $document, $method, $comment, $active=1, $secmemid=0){
	// write a new record to the table user_agreement
		$query="insert into `user_agreements` set `memid`=".intval($memid).", `secmemid`=".intval($secmemid).
			",`document`='".mysql_real_escape_string($document)."',`date`=NOW(), `active`=".intval($active).",`method`='".mysql_real_escape_string($method)."',`comment`='".mysql_real_escape_string($comment)."'" ;
		$res = mysql_query($query);
	}

	/**
	 * get_user_agreement_status()
	 *  returns 1 if the user has an entry for the given type in user_agreement, 0 if no entry is recorded
	 * @param mixed $memid
	 * @param string $type
	 * @return
	 */
	function get_user_agreement_status($memid, $type="CCA"){
		$query="SELECT u.`document` FROM `user_agreements` u
			WHERE u.`document` = '" . mysql_real_escape_string($type) . "' AND u.`memid`=" . intval($memid) ;
		$res = mysql_query($query);
		if(mysql_num_rows($res) <=0){
			return 0;
		}else{
			return 1;
		}
	}

	/**
	 * Get the first user_agreement entry of the requested type
	 * @param int $memid
	 * @param string $type - the type of user agreement, by default all
	 *     agreements are listed
	 * @param int $active - whether to get active or passive agreements:
	 *     0 := passive
	 *     1 := active
	 *     null := both
	 * @return array(string=>mixed) - an associative array containing
	 *     'document', 'date', 'method', 'comment', 'active'
	 */
	function get_first_user_agreement($memid, $type=null, $active=null){
		$filter = '';
		if (!is_null($type)) {
			$filter .= " AND u.`document` = '".mysql_real_escape_string($type)."'";
		}

		if (!is_null($active)) {
			$filter .= " AND u.`active` = ".intval($active);
		}

		$query="SELECT u.`document`, u.`date`, u.`method`, u.`comment`, u.`active` FROM `user_agreements` AS u
			WHERE u.`memid`=".intval($memid)."
				$filter
			ORDER BY u.`date` LIMIT 1";
		$res = mysql_query($query);
		if(mysql_num_rows($res) >0){
			$rec = mysql_fetch_assoc($res);
		}else{
			$rec=array();
		}
		return $rec;
	}

	/**
	 * Get the last user_agreement entry of the requested type
	 * @param int $memid
	 * @param string $type - the type of user agreement, by default all
	 *     agreements are listed
	 * @param int $active - whether to get active or passive agreements:
	 *     0 := passive,
	 *     1 := active,
	 *     null := both
	 * @return array(string=>mixed) - an associative array containing
	 *     'document', 'date', 'method', 'comment', 'active'
	 */
	function get_last_user_agreement($memid, $type=null, $active=null){
		$filter = '';
		if (!is_null($type)) {
			$filter .= " AND u.`document` = '".mysql_real_escape_string($type)."'";
		}

		if (!is_null($active)) {
			$filter .= " AND u.`active` = ".intval($active);
		}

		$query="SELECT u.`document`, u.`date`, u.`method`, u.`comment`, u.`active` FROM `user_agreements` AS u
			WHERE u.`memid`=".intval($memid)."
				$filter
			ORDER BY u.`date` DESC LIMIT 1";
		$res = mysql_query($query);
		if(mysql_num_rows($res) >0){
			$rec = mysql_fetch_assoc($res);
		}else{
			$rec=array();
		}
		return $rec;
	}

/**
 * Get the all user_agreement entries of the requested type
 * @param int $memid
 * @param string $type - the type of user agreement, by default all
 *     agreements are listed
 * @param int $active - whether to get an active or passive agreements:
 *     0 := passive,
 *     1 := active,
 *     null := both
 * @return resource - a mysql result set containing all agreements
 */
function get_user_agreements($memid, $type=null, $active=null){
	$filter = '';
	if (!is_null($type)) {
		$filter .= " AND u.`document` = '".mysql_real_escape_string($type)."'";
	}

	if (!is_null($active)) {
		$filter .= " AND u.`active` = ".intval($active);
	}

	$query="SELECT u.`document`, u.`date`, u.`method`, u.`comment`, u.`active` FROM `user_agreements` AS u
		WHERE u.`memid`=".intval($memid)."
			$filter
		ORDER BY u.`date`";
	return mysql_query($query);
}

	/**
	 * delete_user_agreement()
	 *  deletes all entries for a given type from user_agreement of a given user, if type is not given all
	 * @param mixed $memid
	 * @param string $type
	 * @return
	 */
	function delete_user_agreement($memid, $type=false){
		if ($type === false) {
			$filter = '';
		} else {
			$filter = " and `document` = '" . mysql_real_escape_string($type) . "'";
		}
		mysql_query("delete from `user_agreements` where `memid`=" . intval($memid) . $filter );
	}

	// functions for 6.php (assure somebody)

	function AssureHead($confirmation,$checkname)
	{
?>
<form method="post" action="wot.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="600">
	<tr>
		<td colspan="2" class="title"><?=$confirmation?></td>
	</tr>
	<tr>
		<td class="DataTD" colspan="2" align="left"><?=$checkname?></td>
	</tr>
<?
	}

	function AssureTextLine($field1,$field2)
	{
?>
	<tr>
		<td class="DataTD"><?=$field1.(empty($field1)?'':':')?></td>
		<td class="DataTD"><?=$field2?></td>
	</tr>
<?
	}

	function AssureBoxLine($type,$text,$checked)
	{
?>
	<tr>
		<td class="DataTD"><input type="checkbox" name="<?=$type?>" value="1" <?=$checked?"checked":""?>></td>
		<td class="DataTD"><?=$text?></td>
	</tr>
<?
	}

	function AssureMethodLine($text,$methods,$remark)
	{
		if (count($methods) != 1) {
?>
	<tr>
		<td class="DataTD"><?=$text.(empty($text)?'':':')?></td>
		<td class="DataTD">
			<select name="method">
<?
			foreach($methods as $val) {
?>
				<option value="<?=$val?>"><?=$val?></option>
<?
			}
?>
			</select>
			<br />
			<?=$remark?>
		</td>
	</tr>
<?
		} else {
?>
	<input type="hidden" name="method" value="<?=$methods[0]?>" />
<?
		}
	}

	function AssureInboxLine($type,$field,$value,$description)
	{
?>
	<tr>
		<td class="DataTD"><?=$field.(empty($field)?'':':')?></td>
		<td class="DataTD"><input type="text" name="<?=$type?>" value="<?=$value?>"><?=$description?></td>
	</tr>
<?
	}

	function AssureFoot($oldid,$confirm)
	{
?>
	<tr>
		<td class="DataTD" colspan="2">
			<input type="submit" name="process" value="<?=$confirm?>" />
			<input type="submit" name="cancel" value="<?=_("Cancel")?>" />
		</td>
	</tr>
</table>
<input type="hidden" name="pagehash" value="<?=$_SESSION['_config']['wothash']?>" />
<input type="hidden" name="oldid" value="<?=$oldid?>" />
</form>
<?
	}

	function account_email_delete($mailid){
	//deletes an email entry from an acount
	//revolkes all certifcates for that email address
	//called from www/account.php if($process != "" && $oldid == 2)
	//called from www/diputes.php if($type == "reallyemail") / if($action == "accept")
	//called from account_delete
		$mailid = intval($mailid);
		revoke_all_client_cert($mailid);
		$query = "update `email` set `deleted`=NOW() where `id`='$mailid'";
		mysql_query($query);
	}

	function account_domain_delete($domainid){
	//deletes an domain entry from an acount
	//revolkes all certifcates for that domain address
	//called from www/account.php if($process != "" && $oldid == 9)
	//called from www/diputes.php if($type == "reallydomain") / if($action == "accept")
	//called from account_delete
		$domainid = intval($domainid);
		revoke_all_server_cert($domainid);
		mysql_query(
			"update `domains`
			set `deleted`=NOW()
			where `id` = '$domainid'");
	}

	function account_delete($id, $arbno, $adminid){
	//deletes an account following the deleted account routnie V3
	// called from www/account.php if($oldid == 50 && $process != "")
	//change password
		$id = intval($id);
		$arbno = mysql_real_escape_string($arbno);
		$adminid = intval($adminid);
		$pool = 'abcdefghijklmnopqrstuvwxyz';
		$pool .= '0123456789!()§';
		$pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		srand ((double)microtime()*1000000);
		$password="";
		for($index = 0; $index < 30; $index++)
		{
			$password .= substr($pool,(rand()%(strlen ($pool))), 1);
		}
		mysql_query("update `users` set `password`=sha1('".$password."') where `id`='".$id."'");

	//create new mail for arbitration number
		$query = "insert into `email` set `email`='".$arbno."@cacert.org',`memid`='".$id."',`created`=NOW(),`modified`=NOW(), `attempts`=-1";
		mysql_query($query);
		$emailid = mysql_insert_id();

	//set new mail as default
		$query = "update `users` set `email`='".$arbno."@cacert.org' where `id`='".$id."'";
		mysql_query($query);

	//delete all other email address
		$query = "select `id` from `email` where `memid`='".$id."' and `id`!='".$emailid."'" ;
		$res=mysql_query($query);
		while($row = mysql_fetch_assoc($res)){
			account_email_delete($row['id']);
		}

	//delete all domains
		$query = "select `id` from `domains` where `memid`='".$id."'";
		$res=mysql_query($query);
		while($row = mysql_fetch_assoc($res)){
			account_domain_delete($row['id']);
		}

	//clear alert settings
		mysql_query(
			"update `alerts` set
				`general`='0',
				`country`='0',
				`regional`='0',
				`radius`='0'
			where `memid`='$id'");

	//set default location
		$query = "update `users` set `locid`='2256755', `regid`='243', `ccid`='12' where `id`='".$id."'";
		mysql_query($query);

	//clear listings
		$query = "update `users` set `listme`=' ',`contactinfo`=' ' where `id`='".$id."'";
		mysql_query($query);

	//set lanuage to default
		//set default language
		mysql_query("update `users` set `language`='en_AU' where `id`='".$id."'");
		//delete secondary langugaes
		mysql_query("delete from `addlang` where `userid`='".$id."'");

	//change secret questions
		for($i=1;$i<=5;$i++){
			$q="";
			$a="";
			for($index = 0; $index < 30; $index++)
			{
				$q .= substr($pool,(rand()%(strlen ($pool))), 1);
				$a .= substr($pool,(rand()%(strlen ($pool))), 1);
			}
			$query = "update `users` set `Q$i`='$q', `A$i`='$a' where `id`='".$id."'";
			mysql_query($query);
		}

	//change personal information to arbitration number and DOB=1900-01-01
		$query = "update `users` set `fname`='".$arbno."',
			`mname`='".$arbno."',
			`lname`='".$arbno."',
			`suffix`='".$arbno."',
			`dob`='1900-01-01'
			where `id`='".$id."'";
		mysql_query($query);

	//clear all admin and board flags
		mysql_query(
			"update `users` set
				`assurer`='0',
				`assurer_blocked`='0',
				`codesign`='0',
				`orgadmin`='0',
				`ttpadmin`='0',
				`locadmin`='0',
				`admin`='0',
				`adadmin`='0',
				`tverify`='0',
				`board`='0'
			where `id`='$id'");

	//block account
		mysql_query("update `users` set `locked`='1' where `id`='$id'");  //, `deleted`=Now()
	}


	function check_email_exists($email){
	// called from includes/account.php if($process != "" && $oldid == 1)
	// called from includes/account.php	if($oldid == 50 && $process != "")
		$email = mysql_real_escape_string($email);
		$query = "select 1 from `email` where `email`='$email' and `deleted`=0";
		$res = mysql_query($query);
		return mysql_num_rows($res) > 0;
	}

	function check_gpg_cert_running($uid,$cca=0){
		//if $cca =0 if just expired, =1 if CCA retention +3 month should be obeyed
		// called from includes/account.php	if($oldid == 50 && $process != "")
		$uid = intval($uid);
		if (0==$cca) {
			$query = "select 1 from `gpg` where `memid`='$uid' and `expire`>NOW()";
		}else{
			$query = "select 1 from `gpg` where `memid`='$uid' and `expire`>(NOW()-90*86400)";
		}
		$res = mysql_query($query);
		return mysql_num_rows($res) > 0;
	}

	function check_client_cert_running($uid,$cca=0){
		//if $cca =0 if just expired, =1 if CCA retention +3 month should be obeyed
		// called from includes/account.php	if($oldid == 50 && $process != "")
		$uid = intval($uid);
		if (0==$cca) {
			$query1 = "select 1 from `emailcerts` where `memid`='$uid' and `expire`>NOW() and `revoked`<`created`";
			$query2 = "select 1 from `emailcerts` where `memid`='$uid' and `revoked`>NOW()";
		}else{
			$query1 = "select 1 from `emailcerts` where `memid`='$uid' and `expire`>(NOW()-90*86400)  and `revoked`<`created`";
			$query2 = "select 1 from `emailcerts` where `memid`='$uid' and `revoked`>(NOW()-90*86400)";
		}
		$res = mysql_query($query1);
		$r1 = mysql_num_rows($res)>0;
		$res = mysql_query($query2);
		$r2 = mysql_num_rows($res)>0;
		return !!($r1 || $r2);
	}

	function check_server_cert_running($uid,$cca=0){
		//if $cca =0 if just expired, =1 if CCA retention +3 month should be obeyed
		// called from includes/account.php	if($oldid == 50 && $process != "")
		$uid = intval($uid);
		if (0==$cca) {
			$query1 = "
				select 1 from `domaincerts` join `domains`
					on `domaincerts`.`domid` = `domains`.`id`
				where `domains`.`memid` = '$uid'
					and `domaincerts`.`expire` > NOW()
					and `domaincerts`.`revoked` < `domaincerts`.`created`";
			$query2 = "
				select 1 from `domaincerts` join `domains`
					on `domaincerts`.`domid` = `domains`.`id`
				where `domains`.`memid` = '$uid'
					and `revoked`>NOW()";
		}else{
			$query1 = "
				select 1 from `domaincerts` join `domains`
					on `domaincerts`.`domid` = `domains`.`id`
				where `domains`.`memid` = '$uid'
					and `expire`>(NOW()-90*86400)
					and `revoked`<`created`";
			$query2 = "
				select 1 from `domaincerts` join `domains`
					on `domaincerts`.`domid` = `domains`.`id`
				where `domains`.`memid` = '$uid'
					and `revoked`>(NOW()-90*86400)";
		}
		$res = mysql_query($query1);
		$r1 = mysql_num_rows($res)>0;
		$res = mysql_query($query2);
		$r2 = mysql_num_rows($res)>0;
		return !!($r1 || $r2);
	}

	function check_is_orgadmin($uid){
		// called from includes/account.php	if($oldid == 50 && $process != "")
		$uid = intval($uid);
		$query = "select 1 from `org` where `memid`='$uid' and `deleted`=0";
		$res = mysql_query($query);
		return mysql_num_rows($res) > 0;
	}


	// revokation of certificates
	function revoke_all_client_cert($mailid){
		//revokes all client certificates for an email address
		$mailid = intval($mailid);
		$query = "select `emailcerts`.`id`
			from `emaillink`,`emailcerts` where
			`emaillink`.`emailid`='$mailid' and `emaillink`.`emailcertsid`=`emailcerts`.`id` and `emailcerts`.`revoked`=0
			group by `emailcerts`.`id`";
		$dres = mysql_query($query);
		while($drow = mysql_fetch_assoc($dres)){
			mysql_query("update `emailcerts` set `revoked`='1970-01-01 10:00:01', `disablelogin`=1 where `id`='".$drow['id']."'");
		}
	}

	function revoke_all_server_cert($domainid){
		//revokes all server certs for an domain
		$domainid = intval($domainid);
		$query =
			"select `domaincerts`.`id`
				from `domaincerts`
				where `domaincerts`.`domid` = '$domainid'
			union distinct
			select `domaincerts`.`id`
				from `domaincerts`, `domlink`
				where `domaincerts`.`id` = `domlink`.`certid`
				and `domlink`.`domid` = '$domainid'";
		$dres = mysql_query($query);
		while($drow = mysql_fetch_assoc($dres))
		{
			mysql_query(
			"update `domaincerts`
				set `revoked`='1970-01-01 10:00:01'
				where `id` = '".$drow['id']."'
				and `revoked` = 0");
		}
	}

	function revoke_all_private_cert($uid){
		//revokes all certificates linked to a personal accounts
		//gpg revokation needs to be added to a later point
		$uid=intval($uid);
		$query = "select `id` from `email` where `memid`='".$uid."'";
		$res=mysql_query($query);
		while($row = mysql_fetch_assoc($res)){
			revoke_all_client_cert($row['id']);
		}


		$query = "select `id` from `domains` where `memid`='".$uid."'";
		$res=mysql_query($query);
		while($row = mysql_fetch_assoc($res)){
			revoke_all_server_cert($row['id']);
		}
	}

	/**
	 * check_date_format()
	 * checks if the date is entered in the right date format YYYY-MM-DD and
	 * if the date is after the 1st January of the given year
	 *
	 * @param mixed $date
	 * @param integer $year
	 * @return
	 */
	function check_date_format($date, $year=2000){
		if (!strpos($date,'-')) {
			return FALSE;
		}
		$arr=explode('-',$date);

		if ((count($arr)!=3)) {
			return FALSE;
		}
		if (intval($arr[0])<=$year) {
			return FALSE;
		}
		if (intval($arr[1])>12 or intval($arr[1])<=0) {
			return FALSE;
		}
		if (intval($arr[2])>31 or intval($arr[2])<=0) {
			return FALSE;
		}

		return checkdate( intval($arr[1]), intval($arr[2]), intval($arr[0]));

	}

	/**
	 * check_date_difference()
	 * returns false if the date is larger then today + time diffrence
	 *
	 * @param mixed $date
	 * @param integer $diff
	 * @return
	 */
	function check_date_difference($date, $diff=1){
		return (strtotime($date)<=time()+$diff*86400);
	}


	/**
	 * get_array_from_ini()
	 *  gets an array from an ini file and trims all entries
	 * @param mixed $inifile, path and filename of the ini file
	 * @return
	 */
	function get_array_from_ini($inifile){
		$array = parse_ini_file('../config/ttp.ini');
		ksort($array);
		foreach($array as $key => $value)
		{
			unset($array[$key]);
			$array[trim($key)] = trim($value);
		}
		return	$array;
	}

	/**
	*  create_selectbox_HTML()
	 *
	 * @param mixed $name, name for the select element
	 * @param mixed $options, array with the data for the dropdown
	 * @param string $value, TRUE if the value for the option should be added
	 * @param string $firstline, if the should be a first line like´Choose country
	 * @param string $selected, if selection matches option key the
	 *         entry is preselected in the dropdownbox
	 * @return
	 */
	function create_selectbox_HTML($name, array $options, $firstline = '', $value='', $selected = ''){
		$return_str='<select name="' . $name . '">';
		if (''!= $firstline) {
			$return_str .= '<option>' . $firstline .'</option>';
		}
		foreach ($options as $key => $avalue) {
			$return_str.='<option';
			if ($value) {
				$return_str.=' value="'.$avalue.'"';
			}
			if ($key==$selected){
				$return_str.=' selected="selected"';
			}
			$return_str.='>'.$key.'</option>';
		}
		$return_str.='</select>';
		return	$return_str;
	}

/**
 * Write some information to the adminlog
 *
 * @param int $uid - id of the user account
 * @param int $adminid - id of the admin
 * @param string $type - the operation that was performed on the user account
 * @param string $info - the ticket / arbitration number or other information
 * @return bool - true := success, false := error
 */
function write_se_log($uid, $adminid, $type, $info){
	//records all support engineer actions changing a user account
	$uid = intval($uid);
	$adminid = intval($adminid);
	$type = mysql_real_escape_string($type);
	$info = mysql_real_escape_string($info);
	$query="insert into `adminlog` (`when`, `uid`, `adminid`,`type`,`information`) values
		(Now(), $uid, $adminid, '$type', '$info')";
	return mysql_query($query);
}

/**
 * Check if the entered information is a valid ticket or arbitration number
 * @param string $ticketno
 * @return bool
 */
function valid_ticket_number($ticketno){
	//a arbitration case
	//d dispute action
	//s support case
	//m board motion
	$pattern='/[adsmADSM]\d{8}\.\d+/';
	if (preg_match($pattern, $ticketno)) {
		return true;
	}
	return false;
}

// function for handling account/43.php
/**
 * Get all data of an account given by the id from the `users` table
 * @param int $userid - account id
 * @param int $deleted - states if deleted data should be visible , default = 0 - not visible
 * @return resource - a mysql result set
 */
function get_user_data($userid, $deleted=0){
	$userid = intval($userid);
	$filter='';
	if (0==$deleted) {
		$filter .=' and `users`.`deleted`=0';
	}
	$query = "select * from `users` where `users`.`id`='$userid' ".$filter;
	return mysql_query($query);
}

/**
 * Get the alert settings for a user
 * @param int $userid for the requested account
 * @return array - associative array
 */
function get_alerts($userid){
	return mysql_fetch_assoc(mysql_query("select * from `alerts` where `memid`='".intval($userid)."'"));
}

/**
 * Get all email addresses linked to the account
 * @param int    $userid
 * @param string $exclude - if given the email address will be excluded
 * @param int    $deleted - states if deleted data should be visible, default = 0 - not visible
 * @return resource - a mysql result set
 */
function get_email_addresses($userid, $exclude, $deleted=0){
	//should be entered in account/2.php
	$userid = intval($userid);
	$filter='';
	if (0==$deleted) {
		$filter .= ' and `deleted`=0';
	}
	if ($exclude) {
		$filter .= " and `email`!='".mysql_real_escape_string($exclude)."'";
	}
	$query = "select * from `email` where `memid`='".$userid."' and `hash`='' ".$filter." order by `created`";
	return mysql_query($query);
}

/**
 * Get all domains linked to the account
 * @param int $userid
 * @param int $deleted - states if deleted data should be visible, default = 0 - not visible
 * @return resource - a mysql result set
 */
function get_domains($userid, $deleted=0){
	//should be entered in account/9.php
	$userid = intval($userid);
	$filter='';
	if (0==$deleted) {
		$filter .= ' and `deleted`=0';
	}
	$query = "select * from `domains` where `memid`='".$userid."' and `hash`=''".$filter." order by `created`";
	return mysql_query($query);
}

/**
 * Get all training results for the account
 * @param int $userid
 * @return resource - a mysql result set
 */
function get_training_results($userid){
	//should be entered in account/55.php
	$userid = intval($userid);
	$query = "SELECT `CP`.`pass_date`, `CT`.`type_text`, `CV`.`test_text` ".
		" FROM `cats_passed` AS CP, `cats_variant` AS CV, `cats_type` AS CT ".
		" WHERE `CP`.`variant_id`=`CV`.`id` AND `CV`.`type_id`=`CT`.`id` AND `CP`.`user_id` ='".$userid."'".
		" ORDER BY `CP`.`pass_date`";
	return mysql_query($query);
}

/**
 * Get all SE log entries for the account
 * @param int $userid
 * @return resource - a mysql result set
 */
function get_se_log($userid){
	$userid = intval($userid);
	$query = "SELECT `adminlog`.`when`, `adminlog`.`type`, `adminlog`.`information`, `users`.`fname`, `users`.`lname`
		FROM `adminlog`, `users`
		WHERE `adminlog`.`adminid` = `users`.`id` and `adminlog`.`uid`=".$userid."
		ORDER BY `adminlog`.`when`";
	return mysql_query($query);
}

/**
 * Get all client certificates linked to the account
 * @param int $userid
 * @param int $viewall - states if expired certs should be visible, default = 0 - not visible
 * @return resource - a mysql result set
 */
function get_client_certs($userid, $viewall=0){
	//add to account/5.php
	$userid = intval($userid);
	$query = "select UNIX_TIMESTAMP(`emailcerts`.`created`) as `created`,
		UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
		UNIX_TIMESTAMP(`emailcerts`.`expire`) as `expired`,
		`emailcerts`.`expire`,
		`emailcerts`.`revoked` as `revoke`,
		UNIX_TIMESTAMP(`emailcerts`.`revoked`) as `revoked`,
		`emailcerts`.`id`,
		`emailcerts`.`CN`,
		`emailcerts`.`serial`,
		`emailcerts`.`disablelogin`,
		`emailcerts`.`description`
		from `emailcerts`
		where `emailcerts`.`memid`='".$userid."'";
	if($viewall == 0)
	{
		$query .= " AND `emailcerts`.`revoked`=0 AND `emailcerts`.`renewed`=0";
		$query .= " HAVING `timeleft` > 0";
	}
	$query .= " ORDER BY `emailcerts`.`modified` desc";
	return mysql_query($query);
}

/**
 * Get all server certs linked to the account
 * @param int $userid
 * @param int $viewall - states if expired certs should be visible, default = 0 - not visible
 * @return resource - a mysql result set
 */
function get_server_certs($userid, $viewall=0){
	//add to account/12.php
	$userid = intval($userid);
	$query = "select UNIX_TIMESTAMP(`domaincerts`.`created`) as `created`,
			UNIX_TIMESTAMP(`domaincerts`.`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`domaincerts`.`expire`) as `expired`,
			`domaincerts`.`expire`,
			`domaincerts`.`revoked` as `revoke`,
			UNIX_TIMESTAMP(`revoked`) as `revoked`,
			`domaincerts`.`CN`,
			`domaincerts`.`serial`,
			`domaincerts`.`id`,
			`domaincerts`.`description`
			from `domaincerts`,`domains`
			where `domains`.`memid`='".$userid."' and `domaincerts`.`domid`=`domains`.`id`";
	if($viewall == 0)
	{
		$query .= " AND `domaincerts`.`revoked`=0 AND `domaincerts`.`renewed`=0";
		$query .= " HAVING `timeleft` > 0";
	}
	$query .= " ORDER BY `domaincerts`.`modified` desc";
	return mysql_query($query);
}

/**
 * Get all gpg certs linked to the account
 * @param int $userid
 * @param int $viewall - states if expired certs should be visible, default = 0 - not visible
 * @return resource - a mysql result set
 */
function get_gpg_certs($userid, $viewall=0){
	//add to gpg/2.php
	$userid = intval($userid);
	$query = $query = "select UNIX_TIMESTAMP(`issued`) as `issued`,
			UNIX_TIMESTAMP(`expire`) - UNIX_TIMESTAMP() as `timeleft`,
			UNIX_TIMESTAMP(`expire`) as `expired`,
			`expire`, `id`, `level`, `email`, `keyid`, `description`
			from `gpg` where `memid`='".$userid."'";
	if ($viewall == 0) {
		$query .= " HAVING `timeleft` > 0";
	}
	$query .= " ORDER BY `issued` desc";
	return mysql_query($query);
}



/**
 * Show the table header to the email table for the admin log
 */
function output_log_email_header(){
	?>
	<tr>
		<td class="DataTD bold"><?= _("Email, primary bold") ?></td>
		<td class="DataTD bold"><?= _("Created") ?></td>
		<td class="DataTD bold"><?= _("Deleted") ?></td>
	</tr>

	<?
}
/**
 * Show all email data for the admin log
 * @param array  $row - associative array containing the column data
 * @param string $primary - if given the primary address is highlighted
 */
function output_log_email($row, $primary){
	$style = '';
	if ($row['deleted'] !== NULL_DATETIME) {
		$style = ' deletedemailaddress';
	} elseif ($primary == $row['email']) {
		$style = ' primaryemailaddress';
	}
	?>
	<tr>
		<td class="DataTD<?=$style?>"><?=$row['email']?></td>
		<td class="DataTD<?=$style?>"><?=$row['created']?></td>
		<td class="DataTD<?=$style?>"><?=$row['deleted']?></td>
	</tr>
	<?
}

/**
 * Show the table header to the domains table for the admin log
 */
function output_log_domains_header(){
	?>
	<tr>
		<td class="DataTD bold"><?= _("Domain") ?></td>
		<td class="DataTD bold"><?= _("Created") ?></td>
		<td class="DataTD bold"><?= _("Deleted") ?></td>
	</tr>

	<?
}

/**
 * Show the domain data for the admin log
 * @param array $row - associative array containing the column data
 */
function output_log_domains($row){
	$italic='';
	if ($row['deleted'] !== NULL_DATETIME) {
		$italic=' italic';
	}
	?>
	<tr>
		<td class="DataTD<?=$italic?>"><?=$row['domain']?></td>
		<td class="DataTD<?=$italic?>"><?=$row['created']?></td>
		<td class="DataTD<?=$italic?>"><?=$row['deleted']?></td>
	</tr>
	<?
}

/**
 * Show the table header to the user agreement table for the admin log
 */
function output_log_agreement_header(){
	?>
	<tr>
		<td class="DataTD bold"><?= _("Agreement") ?></td>
		<td class="DataTD bold"><?= _("Date") ?></td>
		<td class="DataTD bold"><?= _("Method") ?></td>
		<td class="DataTD bold"><?= _("Active ") ?></td>
	</tr>
	<?
}

/**
 * Show the agreement data for the admin log
 * @param array $row - associative array containing the column data
 */
function output_log_agreement($row){
	?>
	<tr>
		<td class="DataTD" ><?=$row['document']?></td>
		<td class="DataTD" ><?=$row['date']?></td>
		<td class="DataTD" ><?=$row['method']?></td>
		<td class="DataTD"><?= ($row['active']==0)? _('passive'):_('active')?></td>
	</tr>
	<?
}

/**
 * Show the table header to the training table
 */
function output_log_training_header(){
	//should be entered in account/55.php
	?>
	<tr>
		<td class="DataTD bold"><?= _("Agreement") ?></td>
		<td class="DataTD bold"><?= _("Test") ?></td>
		<td class="DataTD bold"><?= _("Variant") ?></td>
	</tr>
	<?
}

/**
 * Show the training data
 * @param array $row - associative array containing the column data
 */
function output_log_training($row){
	//should be entered in account/55.php
	?>
	<tr>
		<td class="DataTD"><?=$row['pass_date']?></td>
		<td class="DataTD"><?=$row['type_text']?></td>
		<td class="DataTD"><?=$row['test_text']?></td>
	</tr>
	<?
}

/**
 * Show the table header to the SE log table for the admin log
 * @param int $support - if support = 1 more information is visible
 */
function output_log_se_header($support=0){
	?>
	<tr>
		<td class="DataTD bold"><?= _("Date") ?></td>
		<td class="DataTD bold"><?= _("Type") ?></td>
		<?
		if (1 == $support) {
			?>
			<td class="DataTD bold"><?= _("Information") ?></td>
			<td class="DataTD bold"><?= _("Admin") ?></td>
			<?
		}
		?>
	</tr>
	<?
}

/**
 * Show the SE log data for the admin log
 * @param array $row - associative array containing the column data
 * @param int   $support - if support = 1 more information is visible
 */
function output_log_se($row, $support=0){
	//should be entered in account/55.php
	?>
	<tr>
		<td class="DataTD"><?=$row['when']?></td>
		<td class="DataTD"><?=$row['type']?></td>
		<?
		if (1 == $support) {
			?>
			<td class="DataTD"><?=$row['information']?></td>
			<td class="DataTD"><?=$row['fname'].' '.$row['lname']?></td>
			<?
		}
		?>
	</tr>
	<?
}

/**
 * Shows the table header to the client cert table
 * @param int  $support - if support = 1 some columns ar not visible
 * @param bool $readonly - whether elements to modify data should be hidden, default is `true`
 */
function output_client_cert_header($support=0, $readonly=true){
	//should be added to account/5.php
	?>
	<tr>
		<?
		if (!$readonly) {
			?>
			<td class="DataTD"><?=_("Renew/Revoke/Delete")?></td>
			<?
		}
		?>
		<td class="DataTD"><?=_("Status")?></td>
		<td class="DataTD"><?=_("Email Address")?></td>
		<td class="DataTD"><?=_("SerialNumber")?></td>
		<td class="DataTD"><?=_("Revoked")?></td>
		<td class="DataTD"><?=_("Expires")?></td>
		<td class="DataTD"><?=_("Login")?></td>
		<?
		if (1 != $support) {
			?>
			<td colspan="2" class="DataTD"><?=_("Comment *")?></td>
			<?
		}
		?>
	</tr>
	<?
}

/**
 * Show the client cert data
 * @param array $row - associative array containing the column data
 * @param int   $support - if support = 1 some columns are not visible
 * @param bool  $readonly - whether elements to modify data should be hidden, default is `true`
 */
function output_client_cert($row, $support=0, $readonly=true){
	//should be entered in account/5.php
	$verified="";
	if ($row['timeleft'] > 0) {
		$verified = _("Valid");
	} else {
		$verified = _("Expired");
	}

	if ($row['expired'] == 0) {
		$verified = _("Pending");
	}

	if ($row['revoked'] == 0) {
		$row['revoke'] = _("Not Revoked");
	} else {
		$verified = _("Revoked");
	}

	?>
	<tr>
	<?
	if (!$readonly) {
		if ($verified === _("Pending")) {
			?>
			<td class="DataTD">
				<input type="checkbox" name="delid[]" value="<?=intval($row['id'])?>">
			</td>
			<?

		} elseif ($verified === _("Revoked")) {
			?>
			<td class="DataTD">&nbsp;</td>
			<?

		} else {
			?>
			<td class="DataTD">
				<input type="checkbox" name="revokeid[]" value="<?=intval($row['id'])?>">
			</td>
			<?
		}
	}

	?>
	<td class="DataTD"><?=$verified?></td>
	<?

	if ($verified === _("Pending")) {
		?>
		<td class="DataTD"><?=(trim($row['CN'])=="" ? _("empty") : htmlspecialchars($row['CN']))?></td>
		<?
	} else {
		?>
		<td class="DataTD">
			<a href="account.php?id=6&amp;cert=<?=intval($row['id'])?>">
				<?=(trim($row['CN'])=="" ? _("empty") : htmlspecialchars($row['CN']))?>
			</a>
		</td>
		<?
	}

	?>
	<td class="DataTD"><?=$row['serial']?></td>
	<td class="DataTD"><?=$row['revoke']?></td>
	<td class="DataTD"><?=$row['expire']?></td>
	<td class="DataTD">
		<input type="checkbox" name="disablelogin_<?=intval($row['id'])?>" value="1" <?=$row['disablelogin']?"":"checked='checked'"?> <?=$readonly?'disabled="disabled"':''?>/>
		<input type="hidden" name="cert_<?=intval($row['id'])?>" value="1" />
	</td>
	<?

	if (1 != $support) {
		?>
		<td class="DataTD">
			<input name="comment_<?=intval($row['id'])?>" type="text" value="<?=htmlspecialchars($row['description'])?>" />
		</td>
		<?
		if (!$readonly) {
			?>
			<td class="DataTD">
				<input type="checkbox" name="check_comment_<?=intval($row['id'])?>" />
			</td>
			<?
		}
	}

	?>
	</tr>
	<?
}

/**
 * Show the table header to the server cert table
 * @param int  $support - if support = 1 some columns ar not visible
 * @param bool $readonly - whether elements to modify data should be hidden, default is `true`
 */
function output_server_certs_header($support=0, $readonly=true){
	//should be entered in account/12.php
	?>
	<tr>
	<?
		if (!$readonly) {
			?>
			<td class="DataTD"><?=_("Renew/Revoke/Delete")?></td>
			<?
		}
		?>
		<td class="DataTD"><?=_("Status")?></td>
		<td class="DataTD"><?=_("CommonName")?></td>
		<td class="DataTD"><?=_("SerialNumber")?></td>
		<td class="DataTD"><?=_("Revoked")?></td>
		<td class="DataTD"><?=_("Expires")?></td>
		<?
		if (1 != $support) {
			?>
			<td colspan="2" class="DataTD"><?=_("Comment *")?></td>
			<?
		}
	?>
	</tr>
	<?
}

/**
 * Show the server cert data
 * @param array $row - associative array containing the column data
 * @param int   $support - if support = 1 some columns are not visible
 * @param bool  $readonly - whether elements to modify data should be hidden, default is `true`
 */
function output_server_certs($row, $support=0, $readonly=true){
	//should be entered in account/12.php
	$verified="";
	if ($row['timeleft'] > 0) {
		$verified = _("Valid");
	} else {
		$verified = _("Expired");
	}

	if ($row['expired'] == 0) {
		$verified = _("Pending");
	}

	if ($row['revoked'] == 0) {
		$row['revoke'] = _("Not Revoked");
	} else {
		$verified = _("Revoked");
	}

	?>
	<tr>
	<?
	if (!$readonly) {
		if ($verified === _("Pending")) {
			?>
			<td class="DataTD">
				<input type="checkbox" name="delid[]" value="<?=intval($row['id'])?>"/>
			</td>
			<?
		} elseif($verified === _("Revoked")) {
			?>
			<td class="DataTD">&nbsp;</td>
			<?
		} else {
			?>
			<td class="DataTD">
				<input type="checkbox" name="revokeid[]" value="<?=intval($row['id'])?>"/>
			</td>
			<?
		}
	}

	?>
	<td class="DataTD"><?=$verified?></td>
	<?

	if ($verified === _("Pending")) {
		?>
		<td class="DataTD"><?=htmlspecialchars($row['CN'])?></td>
		<?
	} else {
		?>
		<td class="DataTD">
			<a href="account.php?id=15&amp;cert=<?=intval($row['id'])?>">
				<?=htmlspecialchars($row['CN'])?>
			</a>
		</td>
		<?
	}

	?>
	<td class="DataTD"><?=$row['serial']?></td>
	<td class="DataTD"><?=$row['revoke']?></td>
	<td class="DataTD"><?=$row['expire']?></td>
	<?

	if (1 != $support) {
		?>
		<td class="DataTD">
			<input name="comment_<?=intval($row['id'])?>" type="text" value="<?=htmlspecialchars($row['description'])?>" />
		</td>
		<?
		if (!$readonly) {
			?>
			<td class="DataTD">
				<input type="checkbox" name="check_comment_<?=intval($row['id'])?>" />
			</td>
			<?
		}
	}

	?>
	</tr>
	<?
}

/**
 * Show the table header to the gpg cert table
 * @param int  $support - if support = 1 some columns ar not visible
 * @param bool $readonly - whether elements to modify data should be hidden, default is `true`
 */
function output_gpg_certs_header($support=0, $readonly=true){
	// $readonly is currently ignored but kept for consistency
	?>
	<tr>
		<td class="DataTD"><?=_("Status")?></td>
		<td class="DataTD"><?=_("Email Address")?></td>
		<td class="DataTD"><?=_("Expires")?></td>
		<td class="DataTD"><?=_("Key ID")?></td>
		<?
		if (1 != $support) {
			?>
			<td colspan="2" class="DataTD"><?=_("Comment *")?></td>
			<?
		}
	?>
	</tr>
	<?
}

/**
 * Show the gpg cert data
 * @param array $row - associative array containing the column data
 * @param int   $support - if support = 1 some columns are not visible
 * @param bool  $readonly - whether elements to modify data should be hidden, default is `true`
 */
function output_gpg_certs($row, $support=0, $readonly=true){
	//should be entered in account/55.php
	$verified="";
	if ($row['timeleft'] > 0) {
		$verified = _("Valid");
	} else {
		$verified = _("Expired");
	}

	if ($row['expired'] == 0) {
		$verified = _("Pending");
	}

	?>
	<tr>
		<td class="DataTD"><?=$verified?></td>
	<?

	if($verified == _("Pending")) {
		?>
		<td class="DataTD"><?=htmlspecialchars($row['email'])?></td>
		<?
	} else {
		?>
		<td class="DataTD">
			<a href="gpg.php?id=3&amp;cert=<?=intval($row['id'])?>">
				<?=htmlspecialchars($row['email'])?>
			</a>
		</td>
		<?
	}

	?>
	<td class="DataTD"><?=$row['expire']?></td>
	<?

	if($verified == _("Pending")) {
		?>
		<td class="DataTD"><?=htmlspecialchars($row['keyid'])?></td>
		<?
	} else {
		?>
		<td class="DataTD">
			<a href="gpg.php?id=3&amp;cert=<?=intval($row['id'])?>">
				<?=htmlspecialchars($row['keyid'])?>
			</a>
		</td>
		<?
	}

	if (1 != $support) {
		?>
		<td class="DataTD">
			<input name="comment_<?=intval($row['id'])?>" type="text" value="<?=htmlspecialchars($row['description'])?>" />
		</td>
		<?
		if (!$readonly) {
			?>
			<td class="DataTD">
				<input type="checkbox" name="check_comment_<?=intval($row['id'])?>" />
			</td>
			<?
		}
	}

	?>
	</tr>
	<?
}
