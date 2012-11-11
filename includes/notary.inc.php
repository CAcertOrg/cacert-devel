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
		     	WHERE `method` = 'Face to Face Meeting' AND `from`='".intval($userid)."' ");
		$row = query_getnextrow($res);

		return intval($row['list']);
	}

	function get_number_of_assurees ($userid)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE `method` = 'Face to Face Meeting' AND `to`='".intval($userid)."' ");
		$row = query_getnextrow($res);

		return intval($row['list']);
	}

	function get_top_assurer_position ($no_of_assurances)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary` 
			WHERE `method` = 'Face to Face Meeting' 
			GROUP BY `from` HAVING count(*) > '".intval($no_of_assurances)."'");
		return intval(query_get_number_of_rows($res)+1);
	}

	function get_top_assuree_position ($no_of_assurees)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE `method` = 'Face to Face Meeting'
			GROUP BY `to` HAVING count(*) > '".intval($no_of_assurees)."'");
		return intval(query_get_number_of_rows($res)+1);
	}

	function get_given_assurances ($userid)
	{
		$res = query_init ("select * from `notary` where `from`='".intval($userid)."' and `from` != `to` order by `id` asc");
		return $res;
	}

	function get_received_assurances ($userid)
	{
		$res = query_init ("select * from `notary` where `to`='".intval($userid)."' and `from` != `to` order by `id` asc ");
		return $res;
	}

	function get_given_assurances_summary ($userid)
	{
		$res = query_init ("select count(*) as number,points,awarded,method from notary where `from`='".intval($userid)."' group by points,awarded,method");
		return $res;
	}
	
	function get_received_assurances_summary ($userid)
	{
		$res = query_init ("select count(*) as number,points,awarded,method from notary where `to`='".intval($userid)."' group by points,awarded,method");
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

	function calc_experience ($row,&$points,&$experience,&$sum_experience,&$revoked)
	{
                $apoints = max($row['points'],$row['awarded']);
		$points += $apoints;
		$experience = "&nbsp;";
		$revoked = false;				# to be coded later (after DB-upgrade)
		if ($row['method'] == "Face to Face Meeting")
		{
			$sum_experience = $sum_experience +2;
			$experience = "2";
		}
		return $apoints;
	}

	function calc_assurances ($row,&$points,&$experience,&$sumexperience,&$awarded,&$revoked)
	{
		$awarded = calc_points($row);
		$revoked = false;

		if ($awarded > 100)
		{
			$experience = $awarded - 100;		// needs to be fixed in the future (limit 50 pts and/or no experience if pts > 100)
			$awarded = 100;
		}
		else
			$experience = 0;	

		switch ($row['method'])
		{
			case 'Thawte Points Transfer':
			case 'CT Magazine - Germany':
			case 'Temporary Increase':	      // Current usage of 'Temporary Increase' may break audit aspects, needs to be reimplemented
				$awarded=sprintf("<strong style='color: red'>%s</strong>",_("Revoked"));
				$experience=0;
				$revoked=true;
				break;
			default:
				$points += $awarded;
		}
		$sumexperience = $sumexperience + $experience;
	}


	function show_user_link ($name,$userid)
	{
		$name = trim($name);
		if($name == "")
		{
			if ($userid == 0)
				$name = _("System");
			else
				$name = _("Deleted account");
		}
		else
			$name = "<a href='wot.php?id=9&amp;userid=".intval($userid)."'>".sanitizeHTML($name)."</a>";
		return $name;
	}

	function show_email_link ($email,$userid)
	{
		$email = trim($email);
		if($email != "")
			$email = "<a href='account.php?id=43&amp;userid=".intval($userid)."'>".sanitizeHTML($email)."</a>";
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

	function output_assurances_header($title,$support)
	{
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
    <tr>
<?
	if ($support == "1")
	{
?>
    	<td colspan="10" class="title"><?=$title?></td>
<?
	} else {
?>
    	<td colspan="7" class="title"><?=$title?></td>
<?	}
?>
    </tr>
    <tr>
    	<td class="DataTD"><strong><?=_("ID")?></strong></td>
    	<td class="DataTD"><strong><?=_("Date")?></strong></td>
<?
	if ($support == "1")
	{
?>
    	<td class="DataTD"><strong><?=_("When entered")?></strong></td>
    	<td class="DataTD"><strong><?=_("Email")?></strong></td>
<?	} ?>
    	<td class="DataTD"><strong><?=_("Who")?></strong></td>
    	<td class="DataTD"><strong><?=_("Points")?></strong></td>
    	<td class="DataTD"><strong><?=_("Location")?></strong></td>
    	<td class="DataTD"><strong><?=_("Method")?></strong></td>
    	<td class="DataTD"><strong><?=_("Experience Points")?></strong></td>
<?
	if ($support == "1")
	{
?>
	<td class="DataTD"><strong><?=_("Revoke")?></strong></td>
<?
	}
?>
    </tr>
<?
	}

	function output_assurances_footer($points_txt,$points,$experience_txt,$sumexperience,$support)
	{
?>
    <tr>
    	<td class="DataTD" colspan="5"><strong><?=$points_txt?>:</strong></td>
    	<td class="DataTD"><?=$points?></td>
    	<td class="DataTD">&nbsp;</td>
    	<td class="DataTD"><strong><?=$experience_txt?>:</strong></td>
    	<td class="DataTD"><?=$sumexperience?></td>
<?
	if ($support == "1")
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

	function output_assurances_row($assuranceid,$date,$when,$email,$name,$awarded,$points,$location,$method,$experience,$userid,$support,$revoked)
	{

	$tdstyle="";
	$emopen="";
	$emclose="";

	if ($awarded == $points)
	{
		if ($awarded == "0")
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
	if ($support == "1")
	{
?>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$when?><?=$emclose?></td>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$email?><?=$emclose?></td>
<?	} 
?>
	<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$name?><?=$emclose?></td>
	<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$awarded?><?=$emclose?></td>
	<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$location?><?=$emclose?></td>
	<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$method?><?=$emclose?></td>
	<td class="DataTD" <?=$tdstyle?>><?=$emopen?><?=$experience?><?=$emclose?></td>
<?
	if ($support == "1")
	{
		if ($revoked == true)
		{
?>
			<td class="DataTD" <?=$tdstyle?>>&nbsp;</td>
<?		} else {
?>
			<td class="DataTD" <?=$tdstyle?>><?=$emopen?><a href="account.php?id=43&amp;userid=<?=intval($userid)?>&amp;assurance=<?=intval($assuranceid)?>&amp;csrf=<?=make_csrf('admdelassurance')?>" onclick="return confirm('<?=_("Are you sure you want to revoke this assurance?")?>');"><?=_("Revoke")?></a><?=$emclose?></td>
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

	function output_given_assurances_content($userid,&$points,&$sum_experience,$support)
	{
		$points = 0;
		$sumexperience = 0;
		$res = get_given_assurances(intval($userid));
		while($row = mysql_fetch_assoc($res))
		{
			$fromuser = get_user (intval($row['to'])); 
			$apoints = calc_experience ($row,$points,$experience,$sum_experience,$revoked);
			$name = show_user_link ($fromuser['fname']." ".$fromuser['lname'],intval($row['to']));
			$email = show_email_link ($fromuser['email'],intval($row['to']));
			output_assurances_row (intval($row['id']),$row['date'],$row['when'],$email,$name,$apoints,intval($row['points']),$row['location'],$row['method']==""?"":_(sprintf("%s", $row['method'])),$experience,$userid,$support,$revoked);
		}
	}

// ************* output received assurances ******************

	function output_received_assurances_content($userid,&$points,&$sum_experience,$support)
	{
		$points = 0;
		$sumexperience = 0;
		$res = get_received_assurances(intval($userid));
		while($row = mysql_fetch_assoc($res))
		{
			$fromuser = get_user (intval($row['from']));
			calc_assurances ($row,$points,$experience,$sum_experience,$awarded,$revoked);
			$name = show_user_link ($fromuser['fname']." ".$fromuser['lname'],intval($row['from']));
			$email = show_email_link ($fromuser['email'],intval($row['from']));
			output_assurances_row (intval($row['id']),$row['date'],$row['when'],$email,$name,$awarded,intval($row['points']),$row['location'],$row['method']==""?"":_(sprintf("%s", $row['method'])),$experience,$userid,$support,$revoked);
		}
	}

// ************* output summary table ******************

	function check_date_limit ($userid,$age)
	{
		$dob = date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")-$age));
		$res = query_init ("select id from `users` where `id`='".$userid."' and `dob` < '$dob'");
		return intval(query_get_number_of_rows($res));
	}

	function calc_points($row)
	{
		$awarded = intval($row['awarded']);
		if ($awarded == "")
			$awarded = 0;
		if (intval($row['points']) < $awarded)
			$points = $awarded;      // if 'sum of added points' > 100, awarded shows correct value
		else
			$points = intval($row['points']);       // on very old assurances, awarded is '0' instead of correct value
		switch ($row['method'])
		{
			case 'Thawte Points Transfer':	  // revoke all Thawte-points     (as per arbitration)
			case 'CT Magazine - Germany':	   // revoke c't		   (only one test-entry)
			case 'Temporary Increase':	      // revoke 'temporary increase'  (Current usage breaks audit aspects, needs to be reimplemented)
				$points = 0;
				break;
			case 'Administrative Increase':	 // ignore AI with 2 points or less (historical for experiance points, now other calculation)
				if ($points <= 2)	       // maybe limit to 35/50 pts in the future?
					$points = 0;
				break;
			case 'Unknown':			 // to be revoked in the future? limit to max 50 pts?
			case 'Trusted Third Parties':	     // to be revoked in the future? limit to max 35 pts?
			case '':				// to be revoked in the future? limit to max 50 pts?
			case 'Face to Face Meeting':	    // normal assurances, limit to 35/50 pts in the future?
				break;
			default:				// should never happen ... ;-)
				$points = 0;
		}
		if ($points < 0)				// ignore negative points (bug needs to be fixed)
			$points = 0;
		return $points;
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
			$points = calc_points ($row);

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

	function output_given_assurances($userid,$support)
	{
		output_assurances_header(_("Assurance Points You Issued"),$support);
		output_given_assurances_content($userid,$points,$sum_experience,$support);
		output_assurances_footer(_("Total Points Issued"),$points,_("Total Experience Points"),$sum_experience,$support);
	}

	function output_received_assurances($userid,$support)
	{
		output_assurances_header(_("Your Assurance Points"),$support);
		output_received_assurances_content($userid,$points,$sum_experience,$support);
		output_assurances_footer(_("Total Assurance Points"),$points,_("Total Experience Points"),$sum_experience,$support);
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
?>
