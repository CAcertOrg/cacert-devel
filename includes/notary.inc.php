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

	function get_number_of_ttpassurances ($userid)
	{
		$res = query_init ("SELECT count(*) AS `list` FROM `notary`
			WHERE (`method`='Trusted Third Parties' or `method`='TTP-Assisted') AND `to`='".intval($userid)."' ");
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

	function get_received_assurances ($userid, $support)
	{
		$where="";
		if ($support==2) {
			$where=" and (`method`='TTP-Assisted' or `method`='TTP TOPUP')";
		}
		$res = query_init ("select * from `notary` where `to`='".intval($userid)."' and `from` != `to` ".$where." order by `id` asc ");
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
		$apoints = max($row['points'], $row['awarded']);
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
<?
	}
?>
    </tr>
    <tr>
    	<td class="DataTD"><strong><?=_("ID")?></strong></td>
    	<td class="DataTD"><strong><?=_("Date")?></strong></td>
<?
	if ($support == "1")
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
		<td<?=($support == "1")?' colspan="5"':' colspan="3"'?> class="DataTD"><strong><?=$points_txt?>:</strong></td>
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
<?
			} else {
?>
		<td class="DataTD" <?=$tdstyle?>><?=$emopen?><a href="account.php?id=43&amp;userid=<?=intval($userid)?>&amp;assurance=<?=intval($assuranceid)?>&amp;csrf=<?=make_csrf('admdelassurance')?>" onclick="return confirm('<?=sprintf(_("Are you sure you want to revoke the assurance with ID &quot;%s&quot;?"),$assuranceid)?>');"><?=_("Revoke")?></a><?=$emclose?></td>
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
		$res = get_received_assurances(intval($userid), $support);
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
			case 'TTP-Assisted':	     // TTP assurances, limit to 35
			case 'TOPUP':	     // TOPUP to be delevoped in the future, limit to 30
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

	function output_given_assurances($userid,$support=0)
	{
		output_assurances_header(_("Assurance Points You Issued"),$support);
		output_given_assurances_content($userid,$points,$sum_experience,$support);
		output_assurances_footer(_("Total Points Issued"),$points,_("Total Experience Points"),$sum_experience,$support);
	}

	function output_received_assurances($userid,$support=0)
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

	function get_user_agreement_status($memid, $type="CCA"){
	//returns 0 - no user agreement, 1- at least one entry
		$query="SELECT u.`document` FROM `user_agreements` u
			WHERE u.`document` = '".$type."' AND (u.`memid`=".$memid." or u.`secmemid`=".$memid.")" ;
		$res = mysql_query($query);
		if(mysql_num_rows($res) <=0){
			return 0;
		}else{
			return 1;
		}
	}

	function get_first_user_agreement($memid, $active=1, $type="CCA"){
	//returns an array (`document`,`date`,`method`, `comment`,`active`)
		if($active==1){
			$filter="u.`memid`=".$memid;
		}else{
			$filter="u.`secmemid`=".$memid;
		}
		$query="SELECT u.`document`, u.`date`, u.`method`, u.`comment`, u.`active` FROM `user_agreements` u
			WHERE u.`document` = '".$type."' AND ".$filter."
			ORDER BY u.`date` Limit 1;";
		$res = mysql_query($query);
		if(mysql_num_rows($res) >0){
			$row = mysql_fetch_assoc($res);
			$rec['document']= $row['document'];
			$rec['date']= $row['date'];
			$rec['method']= $row['method'];
			$rec['comment']= $row['comment'];
			$rec['active']= $row['active'];
		}else{
			$rec=array();
		}
		return $rec;
	}

	function get_last_user_agreement($memid, $type="CCA"){
	//returns an array (`document`,`date`,`method`, `comment`,`active`)
		$query="(SELECT u.`document`, u.`date`, u.`method`, u.`comment`, 1 as `active` FROM user_agreements u WHERE u.`document` = '".$type."' AND (u.`memid`=".$memid." ) order by `date` desc limit 1)
			union
			(SELECT u.`document`, u.`date`, u.`method`, u.`comment`, 0 as `active` FROM user_agreements u WHERE u.`document` = '".$type."' AND ( u.`secmemid`=".$memid.")) order by `date` desc limit 1" ;
		$res = mysql_query($query);
		if(mysql_num_rows($res) >0){
			$row = mysql_fetch_assoc($res);
			$rec['document']= $row['document'];
			$rec['date']= $row['date'];
			$rec['method']= $row['method'];
			$rec['comment']= $row['comment'];
			$rec['active']= $row['active'];
		}else{
			$rec=array();
		}
		return $rec;
	}

	function delete_user_agreement($memid, $type="CCA"){
	//deletes all entries to an user for the given type of user agreements
		mysql_query("delete from `user_agreements` where `memid`='".$memid."'");
		mysql_query("delete from `user_agreements` where `secmemid`='".$memid."'");
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
		<td class="DataTD"><?=$field1.(empty($field1)?'':':')?>:</td>
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
	<input type="hidden" name="<?=$val?>" value="<?=$methods[0]?>" />
<?
		}
	}

	function AssureInboxLine($type,$field,$value,$description)
	{
?>
	<tr>
		<td class="DataTD"><?=$field.(empty($field)?'':':')?>:</td>
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
		$query = "select `fname`,`mname`,`lname`,`suffix`,`dob` from `users` where `id`='$userid'";
		$details = mysql_fetch_assoc(mysql_query($query));
		$query = "insert into `adminlog` set `when`=NOW(),`old-lname`='${details['lname']}',`old-dob`='${details['dob']}',
			`new-lname`='$arbno',`new-dob`='1900-01-01',`uid`='$id',`adminid`='".$adminid."'";
		mysql_query($query);
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
