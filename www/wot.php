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
*/ ?>
<?
require_once("../includes/loggedin.php");
require_once("../includes/lib/l10n.php");
require_once("../includes/notary.inc.php");


function show_page($target,$message,$error)
{
	showheader(_("My CAcert.org Account!"));
	if ($error != "")
		$message=_("ERROR").": ".$error;
	if ($message != "")
		echo "<p><font color='orange' size='+1'>".$message."</font></p>";

	switch ($target)
	{
		case '0':
		case 'InfoPage':	includeit(0, "wot");
					break;
		case '1':
		case 'ListByCity':	includeit(1, "wot");
					break;
		case '2':
		case 'BecomeAssurer':	includeit(2, "wot");
					break;
		case '3':
		case 'TrustRules':	includeit(3, "wot");
					break;
		case '4':
		case 'ShowTTPInfo':	includeit(4, "wot");
					break;
		case '5';
		case 'EnterEmail':	includeit(5, "wot");
					break;
		case '6':
		case 'VerifyData':	includeit(6, "wot");
					break;
//		case '7':
//		case '???':		includeit(7, "wot");
//					break;
		case '8':
		case 'EnterMyInfo':	includeit(8, "wot");
					break;
		case '9':
		case 'ContactAssurer':	includeit(9, "wot");
					break;
		case '10':
		case 'MyPointsOld':	includeit(10, "wot");
					break;
//		case '11':
//		case 'OAInfo':		includeit(11, "wot");
//					break;
		case '12':
		case 'SearchAssurer':	includeit(12, "wot");
					break;
		case '13':
		case 'EnterMyCity':	includeit(13, "wot");
					break;
//		case '14':
//		case 'EnterEmail':	includeit(14, "wot");
//					break;
		case '15':
		case 'MyPointsNew':	includeit(15, "wot");
					break;
		case '16':
		case 'TTPForm':	includeit(16, "wot");
			break;
	}

	showfooter();
}

function send_reminder()
{
	$body = "";
	$my_translation = L10n::get_translation();

	$_SESSION['_config']['reminder-lang'] = $_POST['reminder-lang'];

	$reminder_translations[] = $_POST['reminder-lang'];
	if ( !in_array("en", $reminder_translations, $strict=true) ) {
		$reminder_translations[] = "en";
	}

	foreach ($reminder_translations as $translation) {
		L10n::set_translation($translation);

		$body .= L10n::$translations[$translation].":\n\n";
		$body .= sprintf(_("This is a short reminder that you filled out forms to become trusted with CAcert.org, and %s has attempted to issue you points. Please create your account at %s as soon as possible and then notify %s so that the points can be issued."), $_SESSION['profile']['fname']." (".$_SESSION['profile']['email'].")", "http://www.cacert.org", $_SESSION['profile']['fname'])."\n\n";
		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team")."\n\n";
	}

	L10n::set_translation($reminder_translations[0]); // for the subject
	sendmail($_POST['email'], "[CAcert.org] "._("Reminder Notice"), $body, $_SESSION['profile']['email'], "", "", $_SESSION['profile']['fname']);

	L10n::set_translation($my_translation);

	$_SESSION['_config']['remindersent'] = 1;
	$_SESSION['_config']['error'] = _("A reminder notice has been sent.");
}

	loadem("account");
	if(array_key_exists('date',$_POST) && $_POST['date'] != "")
		$_SESSION['_config']['date'] = $_POST['date'];

	if(array_key_exists('location',$_POST) && $_POST['location'] != "")
		$_SESSION['_config']['location'] = $_POST['location'];

	$oldid=array_key_exists('oldid',$_REQUEST)?intval($_REQUEST['oldid']):0;

	if($oldid == 12)
		$id = $oldid;

	if($oldid == 4)
	{
		if ($_POST['ttp']!='') {
			//This mail does not need to be translated
			$body = "Hi TTP adminstrators,\n\n";
			$body .= "User ".$_SESSION['profile']['fname']." ".
				$_SESSION['profile']['lname']." with email address '".
				$_SESSION['profile']['email']."' is requesting a TTP assurances for ".
				mysql_escape_string(stripslashes($_POST['country'])).".\n\n";
			if ($_POST['ttptopup']=='1') {
				$body .= "The user is also requesting TTP TOPUP.\n\n";
			}else{
				$body .= "The user is NOT requesting TTP TOPUP.\n\n";
			}
			$body .= "The user received ".intval($_SESSION['profile']['points'])." assurance points up to today.\n\n";
			$body .= "Please start the TTP assurance process.";
			sendmail("support@cacert.org", "[CAcert.org] TTP request.", $body, "support@cacert.org", "", "", "CAcert Website");

			//This mail needs to be translated
			$body  =_("You are receiving this email because you asked for TTP assurance.")."\n\n";
			if ($_POST['ttptopup']=='1') {
				$body .=_("You are requesting TTP TOPUP.")."\n\n";
			}else{
				$body .=_("You are NOT requesting TTP TOPUP.")."\n\n";
			}
			$body .= _("Best regards")."\n";
			$body .= _("CAcert Support Team");

			sendmail($_SESSION['profile']['email'], "[CAcert.org] "._("You requested TTP assurances"), $body, "support@cacert.org", "", "", "CAcert Support");

		}

	}

	if(($id == 5 || $oldid == 5 || $id == 6 || $oldid == 6))
		if (!is_assurer($_SESSION['profile']['id']))
			{
				show_page ("Exit","",get_assurer_reason($_SESSION['profile']['id']));
				exit;
			}

	if($oldid == 6 && intval($_SESSION['_config']['notarise']['id']) <= 0)
	{
		show_page ("EnterEmail","",_("Something went wrong. Please enter the email address again"));
		exit;
	}
	if($oldid == 5 && array_key_exists('reminder',$_POST) && $_POST['reminder'] != "")
	{
		send_reminder();
		show_page ("EnterEmail",_("A reminder notice has been sent."),"");
		exit;
	}

	if($oldid == 5)
	{
		$query = "select * from `users` where `email`='".mysql_real_escape_string(stripslashes($_POST['email']))."' and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) != 1)
		{
			$_SESSION['_config']['noemailfound'] = 1;
			show_page("EnterEmail","",_("I'm sorry, there was no email matching what you entered in the system. Please double check your information."));
			exit;
		} else
		{
			$_SESSION['_config']['noemailfound'] = 0;
			$_SESSION['_config']['notarise'] = mysql_fetch_assoc($res);
			if ($_SESSION['_config']['notarise']['verified'] == 0)
			{
				show_page("EnterEmail","",_("User is not yet verified. Please try again in 24 hours!"));
				exit;
			}
			if ($_SESSION['profile']['ttpadmin'] != 1) {
				$_SESSION['assuresomeone']['year'] = mysql_real_escape_string(stripslashes($_POST['year']));
				$_SESSION['assuresomeone']['month'] = mysql_real_escape_string(stripslashes($_POST['month']));
				$_SESSION['assuresomeone']['day'] = mysql_real_escape_string(stripslashes($_POST['day']));
				$dob = $_SESSION['assuresomeone']['year'] . '-' . sprintf('%02d',$_SESSION['assuresomeone']['month']) . '-' . sprintf('%02d', $_SESSION['assuresomeone']['day']);

				if (	$_SESSION['_config']['notarise']['dob'] != $dob) {
					show_page("EnterEmail","",_("The data entered is not matching with an account."));
					exit;
				}
			}
		}
		$query = "select * from `users` where `email`='".mysql_real_escape_string(stripslashes($_POST['email']))."' and `locked`=1";
		$res = mysql_query($query);
		if(mysql_num_rows($res) >= 1)
		{
			$_SESSION['_config']['noemailfound'] = 0;
			show_page("EnterEmail","",_("This account is locked and can not be assured. For more information ask support@cacert.org."));
			exit;
		}
	}

	if($oldid == 5 || $oldid == 6)
	{
		$id=6;
//		$oldid=0;
		if(array_key_exists('cancel',$_REQUEST) && $_REQUEST['cancel'] != "")
		{
			show_page("EnterEmail","","");
			exit;
		}
		if($_SESSION['_config']['notarise']['id'] == $_SESSION['profile']['id'])
		{
			show_page("EnterEmail","",_("You are never allowed to Assure yourself!"));
			exit;
		}

		$query = "select * from `notary` where `from`='".intval($_SESSION['profile']['id'])."' and
			`to`='".intval($_SESSION['_config']['notarise']['id'])."' and `deleted` = 0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			show_page("EnterEmail","",_("You are only allowed to Assure someone once!"));
			exit;
		}
	}

	if($oldid == 6)
	{
		$iecho= "c";
		//date checks
		if(trim($_REQUEST['date']) == '')
		{
			show_page("VerifyData","",_("You must enter the date when you met the assuree."));
			exit;
		}

		if(!check_date_format(trim($_REQUEST['date'])))
		{
			show_page("VerifyData","",_("You must enter the date in this format: YYYY-MM-DD."));
			exit;
		}

		if(!check_date_difference(trim($_REQUEST['date'])))
		{
			show_page("VerifyData","",_("You must not enter a date in the future."));
			exit;
		}

		//proof of identity check and accept arbitration, implements CCA
		if(!array_key_exists('assertion',$_POST) || $_POST['assertion'] != 1)
		{
			show_page("VerifyData","",_("You failed to check all boxes to validate your adherence to the rules and policies of CAcert"));
			exit;
		}

		//proof of CCA agreement by assuree after 2010-01-01
		if((!array_key_exists('CCAAgreed',$_POST) || $_POST['CCAAgreed'] != 1) and (check_date_format(trim($_REQUEST['date']),2010)))
		{
			show_page("VerifyData","",_("You failed to check all boxes to validate your adherence to the rules and policies of CAcert"));
			exit;
		}

		//assurance done according to rules
		if(!array_key_exists('rules',$_POST) || $_POST['rules'] != 1)
		{
			show_page("VerifyData","",_("You failed to check all boxes to validate your adherence to the rules and policies of CAcert"));
			exit;
		}

		//met assuree in person, not appliciable for TTP / TTP Topup assurances
		if((!array_key_exists('certify',$_POST) || $_POST['certify'] != 1 )  && $_REQUEST['method'] != "Trusted 3rd Parties")
		{
			show_page("VerifyData","",_("You failed to check all boxes to validate your adherence to the rules and policies of CAcert"));
			exit;
		}

		//check location, min 3 characters
		if(!array_key_exists('location',$_POST) || trim($_POST['location']) == "")
		{
			show_page("VerifyData","",_("You failed to enter a location of your meeting."));
			exit;
		}

		if(strlen(trim($_REQUEST['location']))<=2)
		{
			show_page("VerifyData","",_("You must enter a location with at least 3 characters eg town and country."));
			exit;
		}

		//check for points in range 0-35, for nucleus 35 + 15 temporary
		if($_REQUEST['points'] == "" || !is_numeric($_REQUEST['points']))
		{
			show_page("VerifyData","",_("You must enter the number of points you wish to allocate to this person."));
			exit;
		}

		if($_REQUEST['points'] <0 || ($_REQUEST['points']>35))
		{
			show_page("VerifyData","",_("The number of points you entered are out of the range given by policy."));
			exit;
		}

		$query = "select * from `users` where `id`='".intval($_SESSION['_config']['notarise']['id'])."'";
		$res = mysql_query($query);
		$row = mysql_fetch_assoc($res);
		$name = $row['fname']." ".$row['mname']." ".$row['lname']." ".$row['suffix'];
		if($_SESSION['_config']['wothash'] != md5($name."-".$row['dob']) || $_SESSION['_config']['wothash'] != $_REQUEST['pagehash'])
		{
			show_page("VerifyData","",_("Race condition discovered, user altered details during assurance procedure. PLEASE MAKE SURE THE NEW DETAILS BELOW MATCH THE ID DOCUMENTS."));
			exit;
		}
	}


	if($oldid == 6)
	{
		$max =  maxpoints();

		$awarded = $newpoints = intval($_POST['points']);
		if($newpoints > $max)
			$newpoints = $awarded = $max;
		if($newpoints < 0)
			$newpoints = $awarded = 0;

		$query = "select sum(`points`) as `total` from `notary` where `to`='".intval($_SESSION['_config']['notarise']['id'])."' and `deleted` = 0 group by `to`";
		$res = mysql_query($query);
		$drow = mysql_fetch_assoc($res);
		$oldpoints = intval($drow['total']);

		$_POST['expire'] = 0;

		if(($oldpoints + $newpoints) > 100 && $max < 100)
			$newpoints = 100 - $oldpoints;
		if(($oldpoints + $newpoints) > $max && $max >= 100)
			$newpoints = $max - $oldpoints;
		if($newpoints < 0)
			$newpoints = 0;

		if(mysql_real_escape_string(stripslashes($_POST['date'])) == "")
			$_POST['date'] = date("Y-m-d H:i:s");

		$query = "select * from `notary` where `from`='".intval($_SESSION['profile']['id'])."' AND
						`to`='".intval($_SESSION['_config']['notarise']['id'])."' AND
						`awarded`='".intval($awarded)."' AND
						`location`='".mysql_real_escape_string(stripslashes($_POST['location']))."' AND
						`date`='".mysql_real_escape_string(stripslashes($_POST['date']))."' AND
						`deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			show_page("VerifyEmail","",_("Identical Assurance attempted, will not continue."));
			exit;
		}
	}

	if($oldid == 6)
	{
		$query = "insert into `notary` set `from`='".intval($_SESSION['profile']['id'])."',
						`to`='".intval($_SESSION['_config']['notarise']['id'])."',
						`points`='".intval($newpoints)."', `awarded`='".intval($awarded)."',
						`location`='".mysql_real_escape_string(stripslashes($_POST['location']))."',
						`date`='".mysql_real_escape_string(stripslashes($_POST['date']))."',
						`when`=NOW()";
		//record active acceptance by Assurer
		if (check_date_format(trim($_REQUEST['date']),2010)) {
			write_user_agreement($_SESSION['profile']['id'], "CCA", "assurance", "Assuring", 1, $_SESSION['_config']['notarise']['id']);
			write_user_agreement($_SESSION['_config']['notarise']['id'], "CCA", "assurance", "Being assured", 0, $_SESSION['profile']['id']);
		}
		if($_SESSION['profile']['ttpadmin'] >= 1 && $_POST['method'] == 'TTP-Assisted') {
			$query .= ",\n`method`='TTP-Assisted'";
		}
		if($_SESSION['profile']['ttpadmin'] == 2 && $_POST['method'] == 'TTP-TOPUP') {
			$query .= ",\n`method`='TTP-TOPUP'";
		}
		mysql_query($query);
		fix_assurer_flag($_SESSION['_config']['notarise']['id']);
		include_once("../includes/notary.inc.php");

		if($_SESSION['profile']['points'] < 150)
		{
			$addpoints = 0;
			if($_SESSION['profile']['points'] < 149 && $_SESSION['profile']['points'] >= 100)
				$addpoints = 2;
			else if($_SESSION['profile']['points'] == 149 && $_SESSION['profile']['points'] >= 100)
				$addpoints = 1;
			$query = "insert into `notary` set `from`='".intval($_SESSION['profile']['id'])."',
							`to`='".intval($_SESSION['profile']['id'])."',
							`points`='".intval($addpoints)."', `awarded`='".intval($addpoints)."',
							`location`='".mysql_real_escape_string(stripslashes($_POST['location']))."',
							`date`='".mysql_real_escape_string(stripslashes($_POST['date']))."',
							`method`='Administrative Increase',
							`when`=NOW()";
			mysql_query($query);

			// No need to fix_assurer_flag here, this should only happen for assurers...
			$_SESSION['profile']['points'] += $addpoints;
		}

		$my_translation = L10n::get_translation();
		L10n::set_translation($_SESSION['_config']['notarise']['language']);

		$assurer =  $_SESSION['profile']['fname'].' '.$_SESSION['profile']['lname'];
		$body  = sprintf(_("You are receiving this email because you have been assured by %s (%s)."), $assurer, $_SESSION['profile']['email'])."\n\n";
		if(($oldpoints + $newpoints) >= 100)
			$body .= sprintf(_("You were issued %s points. However the system only counts up to 100 assurance points."), $awarded)."\n\n";
		else
			$body .= sprintf(_("You were issued %s points and you now have %s points in total."), $awarded, ($newpoints + $oldpoints))."\n\n";

		if(($oldpoints + $newpoints) < 100 && ($oldpoints + $newpoints) >= 50)
		{
			$body .= _("You now have over 50 points, and can now have your name added to client certificates, and issue server certificates for up to 2 years.")."\n\n";
		}

		if(($oldpoints + $newpoints) >= 100 && $newpoints > 0)
		{
			$body .= _("You have at least 100 Assurance Points, if you want to become an assurer try the Assurer Challenge")." ( https://cats.cacert.org )\n\n";
			$body .= _("To make it easier for others in your area to find you, it's helpful to list yourself as an assurer (this is voluntary), as well as a physical location where you live or work the most. You can flag your account to be listed, and add a comment to the display by going to:")."\n";
			$body .= "https://www.cacert.org/wot.php?id=8\n\n";
			$body .= _("You can list your location by going to:")."\n";
			$body .= "https://www.cacert.org/wot.php?id=13\n\n";
		}

		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team");

		sendmail($_SESSION['_config']['notarise']['email'], "[CAcert.org] "._("You've been Assured."), $body, "support@cacert.org", "", "", "CAcert Website");

		L10n::set_translation($my_translation);

		$assuree = $_SESSION['_config']['notarise']['fname'].' '.$_SESSION['_config']['notarise']['lname'];
		$body  = sprintf(_("You are receiving this email because you have assured %s (%s)."), $assuree, $_SESSION['_config']['notarise']['email'])."\n\n";
		$body .= sprintf(_("You issued %s points."), $awarded)."\n\n";

		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team");

		sendmail($_SESSION['profile']['email'], "[CAcert.org] "._("You've Assured Another Member."), $body, "support@cacert.org", "", "", "CAcert Support");

		showheader(_("My CAcert.org Account!"));
		echo "<p>"._("Shortly you and the person you were assuring will receive an email confirmation. There is no action on your behalf required to complete this.")."</p>";
?><form method="post" action="wot.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Assure Someone")?></td>
  </tr>
  <tr>
    <td class="DataTD"><?=_("Email")?>:</td>
    <td class="DataTD"><input type="text" name="email" id="email" value=""></td>
  </tr>
  <tr>
	<td class="DataTD">
		<?=_("Date of Birth")?><br/>
		(<?=_("yyyy/mm/dd")?>)</td>
	<td class="DataTD">
		<input type="text" name="year" value="<?=array_key_exists('year',$_SESSION['assuresomeone']) ? sanitizeHTML($_SESSION['assuresomeone']['year']):""?>" size="4" autocomplete="off"></nobr>
		<select name="month">
			<?
for($i = 1; $i <= 12; $i++)
{
	echo "<option value='$i'";
	if(array_key_exists('month',$_SESSION['assuresomeone']) && $_SESSION['assuresomeone']['month'] == $i)
		echo " selected=\"selected\"";
	echo ">".ucwords(strftime("%B", mktime(0,0,0,$i,1,date("Y"))))." ($i)</option>\n";
}
			?>
		</select>
		<select name="day">
			<?
for($i = 1; $i <= 31; $i++)
{
	echo "<option";
	if(array_key_exists('day',$_SESSION['assuresomeone']) && $_SESSION['assuresomeone']['day'] == $i)
		echo " selected=\"selected\"";
	echo ">$i</option>";
}
			?>
		</select>
	</td>
  </tr>

	<tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Next")?>"></td>
  </tr>
</table>
<input type="hidden" name="oldid" value="5">
</form>
<SCRIPT LANGUAGE="JavaScript">
//<![CDATA[
	function my_init()
	{
		document.getElementById("email").focus();
	}

	window.onload = my_init();
//]]>
</script>
<?
		showfooter();
		exit;
	}

	if($oldid == 8)
	{
		csrf_check("chgcontact");

		$info = mysql_real_escape_string(strip_tags(stripslashes($_POST['contactinfo'])));
		$listme = intval($_POST['listme']);
		if($listme < 0 || $listme > 1)
			$listme = 0;

		$_SESSION['profile']['listme'] = $listme;
		$_SESSION['profile']['contactinfo'] = $info;

		$query = "update `users` set `listme`='$listme',`contactinfo`='$info' where `id`='".intval($_SESSION['profile']['id'])."'";
		mysql_query($query);

		showheader(_("My CAcert.org Account!"));
		echo "<p>"._("Your account information has been updated.")."</p>";
		showfooter();
		exit;
	}

	if($oldid == 9 && $_REQUEST['userid'] > 0 && $_SESSION['profile']['id'] > 0)
	{
		if($_SESSION['_config']['pagehash'] != $_REQUEST['pageid'])
		{
			$oldid=0;
			$id = 9;
			show_page("ContactAssurer","",_("It looks like you were trying to contact multiple people, this isn't allowed due to data security reasons."));
			exit;
		} else {
			$body = $_REQUEST['message'];
			$subject = $_REQUEST['subject'];
			$userid = intval($_REQUEST['userid']);
			$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($userid)."' and `listme`=1"));
			$points = mysql_num_rows(mysql_query("select sum(`points`) as `total` from `notary`
						where `to`='".intval($user['id'])."' and `deleted` = 0 group by `to` HAVING SUM(`points`) > 0"));
			if($points > 0)
			{
				$my_translation = L10n::get_translation();
				L10n::set_translation($user['language']);

				$subject = "[CAcert.org] ".sprintf(_("Message from %s"),
						$_SESSION['profile']['fname']);

				$body  = sprintf(_("Hi %s,"), $user['fname'])."\n\n";
				$body .= sprintf(_("%s %s has sent you a message via the ".
						"contact an Assurer form on CAcert.org."),
						$_SESSION['profile']['fname'],
						$_SESSION['profile']['lname'])."\n\n";
				$body .= sprintf(_("Subject: %s"), $_REQUEST['subject'])."\n";
				$body .= _("Message:")."\n";
				$body .= $_REQUEST['message']."\n\n";
				$body .= "------------------------------------------------\n\n";
				$body .= _("Please note, that this is NOT a message on behalf ".
						"of CAcert but another CAcert community member. If ".
						"you suspect that the contact form might have been ".
						"abused, please write to support@cacert.org")."\n\n";
				$body .= _("Best regards")."\n";
				$body .= _("Your CAcert Community");

				sendmail($user['email'], $subject, $body,
						$_SESSION['profile']['email'], //from
						"", //replyto
						"", //toname
						$_SESSION['profile']['fname']." ".
							$_SESSION['profile']['lname']); //fromname

				L10n::set_translation($my_translation);

				showheader(_("My CAcert.org Account!"));?>
				<p>
					<? printf(_("Your email has been sent to %s."), sanitizeHTML($user['fname'])); ?>
				</p>
				<p>[ <a href='javascript:history.go(-2)'><?= _("Go Back") ?></a> ]</p>
				<?
				showfooter();
				exit;
			} else {
				show_page(0,"",_("Sorry, I was unable to locate that user."));
				exit;
			}

		}
	}
	if($oldid == 9)
	{
		$oldid=0;
		$id = 9;
		show_page("ContactAssurer","",_("There was an error and I couldn't proceed"));
		exit;
	}

//	showheader(_("My CAcert.org Account!"));
//	echo "ID now = ".$id."/".$oldid.">>".$iecho;
//	includeit($id, "wot");
//	showfooter();
show_page ($id,"","");
?>
