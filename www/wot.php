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
	}

	showfooter();
}

function send_reminder()
{
	$body = "";
	if($_POST['reminder-lang'] != "" && $_POST['reminder-lang'] != "en_AU")
        {
		$userlang = $_POST['reminder-lang'];
		$_SESSION['_config']['reminder-lang'] = $_POST['reminder-lang'];
		putenv("LANG=".$userlang);
		setlocale(LC_ALL, $userlang);

		$body .= $_SESSION['_config']['translations'][$userlang].":\n\n";
		$body .= sprintf(_("This is a short reminder that you filled out forms to become trusted with CAcert.org, and %s has attempted to issue you points. Please create your account at %s as soon as possible and then notify %s so that the points can be issued."), $_SESSION['profile']['fname']." (".$_SESSION['profile']['email'].")", "http://www.cacert.org", $_SESSION['profile']['fname'])."\n\n";
		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team");

		$body .= "\n\nEnglish:\n\n";
	}

	$body .= sprintf("This is a short reminder that you filled out forms to become trusted with CAcert.org, and %s has attempted to issue you points. Please create your account at %s as soon as possible and then notify %s so that the points can be issued.", $_SESSION['profile']['fname']." (".$_SESSION['profile']['email'].")", "http://www.cacert.org", $_SESSION['profile']['fname'])."\n\n";
	$body .= "Best regards"."\n";
	$body .= "CAcert Support Team";

	sendmail($_POST['email'], "[CAcert.org] "._("Reminder Notice"), $body, $_SESSION['profile']['email'], "", "", $_SESSION['profile']['fname']);

	if($_POST['reminder-lang'] != "" && $_POST['reminder-lang'] != "en_AU")
	{
        	$userlang = $_SESSION['profile']['language'];
		putenv("LANG=".$userlang);
		setlocale(LC_ALL, $userlang);
	}

	$_SESSION['_config']['remindersent'] = 1;
}





	require_once("../includes/loggedin.php");

	loadem("account");
	if(array_key_exists('date',$_POST) && $_POST['date'] != "")
		$_SESSION['_config']['date'] = $_POST['date'];

	if(array_key_exists('location',$_POST) && $_POST['location'] != "")
		$_SESSION['_config']['location'] = $_POST['location'];

	$oldid=array_key_exists('oldid',$_REQUEST)?intval($_REQUEST['oldid']):0;	

	if($oldid == 12)
		$id = $oldid;

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
		$query = "select * from `users` where `email`='".mysql_escape_string(stripslashes($_POST['email']))."' and `deleted`=0";
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

		$query = "select * from `notary` where `from`='".$_SESSION['profile']['id']."' and
							`to`='".$_SESSION['_config']['notarise']['id']."'";
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
		if(!array_key_exists('assertion',$_POST) || $_POST['assertion'] != 1 || !array_key_exists('rules',$_POST) || $_POST['rules'] != 1)
		{
			show_page("VerifyData","",_("You failed to check all boxes to validate your adherence to the rules and policies of CAcert"));
			exit;
		}

		if((!array_key_exists('certify',$_POST) || $_POST['certify'] != 1 )  && $_SESSION['profile']['ttpadmin'] != 1)
		{
			show_page("VerifyData","",_("You failed to check all boxes to validate your adherence to the rules and policies of CAcert"));
			exit;
		}

		if($_SESSION['profile']['ttpadmin'] != 1 && $_POST['location'] == "")
		{
			show_page("VerifyData","",_("You failed to enter a location of your meeting."));
			exit;
		}

		if($_REQUEST['points'] == "")
		{
			show_page("VerifyData","",_("You must enter the number of points you wish to allocate to this person."));
			exit;
		}

		$query = "select * from `users` where `id`='".$_SESSION['_config']['notarise']['id']."'";
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
		
		$query = "select sum(`points`) as `total` from `notary` where `to`='".$_SESSION['_config']['notarise']['id']."' group by `to`";
		$res = mysql_query($query);
		$drow = mysql_fetch_assoc($res);

		$_POST['expire'] = 0;

		if(($drow['total'] + $newpoints) > 100 && $max < 100)
			$newpoints = 100 - $drow['total'];
		if(($drow['total'] + $newpoints) > $max && $max >= 100)
			$newpoints = $max - $drow['total'];
		if($newpoints < 0)
			$newpoints = 0;
		
		if(mysql_escape_string(stripslashes($_POST['date'])) == "")
			$_POST['date'] = date("Y-m-d H:i:s");

		$query = "select * from `notary` where `from`='".$_SESSION['profile']['id']."' AND
						`to`='".$_SESSION['_config']['notarise']['id']."' AND
						`awarded`='$awarded' AND 
						`location`='".mysql_escape_string(stripslashes($_POST['location']))."' AND
						`date`='".mysql_escape_string(stripslashes($_POST['date']))."'";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
                        show_page("VerifyEmail","",_("Identical Assurance attempted, will not continue."));
			exit;
		}
	}

	if($oldid == 6)
	{
		$query = "insert into `notary` set `from`='".$_SESSION['profile']['id']."',
						`to`='".$_SESSION['_config']['notarise']['id']."',
						`points`='$newpoints', `awarded`='$awarded',
						`location`='".mysql_escape_string(stripslashes($_POST['location']))."',
						`date`='".mysql_escape_string(stripslashes($_POST['date']))."',
						`when`=NOW()";
		if($_SESSION['profile']['board'] == 1 && intval($_POST['expire']) > 0)
		{
			$query .= ",\n`method`='Temporary Increase'";
			$query .= ",\n`expire`=DATE_ADD(NOW(), INTERVAL '".intval($_POST['expire'])."' DAY)";
			$query .= ",\n`sponsor`='".intval($_POST['sponsor'])."'";
		} else if($_SESSION['profile']['board'] == 1) {
			$query .= ",\n`method`='".mysql_escape_string(stripslashes($_POST['method']))."'";
		} else if($_SESSION['profile']['ttpadmin'] == 1 && ($_POST['method'] == 'Trusted 3rd Parties' || $_POST['method'] == 'Trusted third Parties')) {
			$query .= ",\n`method`='Trusted Third Parties'";
		}
		mysql_query($query);
		fix_assurer_flag($_SESSION['_config']['notarise']['id']);
		
		if($_SESSION['profile']['points'] < 150)
		{
			$addpoints = 0;
			if($_SESSION['profile']['points'] < 149 && $_SESSION['profile']['points'] >= 100)
				$addpoints = 2;
			else if($_SESSION['profile']['points'] == 149 && $_SESSION['profile']['points'] >= 100)
				$addpoints = 1;
			$query = "insert into `notary` set `from`='".$_SESSION['profile']['id']."',
							`to`='".$_SESSION['profile']['id']."',
							`points`='$addpoints', `awarded`='$addpoints',
							`location`='".mysql_escape_string(stripslashes($_POST['location']))."',
							`date`='".mysql_escape_string(stripslashes($_POST['date']))."',
							`method`='Administrative Increase',
							`when`=NOW()";
			mysql_query($query);
			// No need to fix_assurer_flag here, this should only happen for assurers...
			$_SESSION['profile']['points'] += $addpoints;
		}

		if($_SESSION['_config']['notarise']['language'] != "")
		{
			$userlang = $_SESSION['_config']['notarise']['language'];
			putenv("LANG=".$userlang);
			setlocale(LC_ALL, $userlang);
		}

		$body  = sprintf(_("You are receiving this email because you have been assured by %s %s (%s)."), $_SESSION['profile']['fname'], $_SESSION['profile']['lname'], $_SESSION['profile']['email'])."\n\n";
		if($_POST['points'] != $newpoints)
			$body .= sprintf(_("You were issued %s points however the system has rounded this down to %s and you now have %s points in total."), $_POST['points'], $newpoints, ($newpoints + $drow['total']))."\n\n";
		else
			$body .= sprintf(_("You were issued %s points and you now have %s points in total."), $newpoints, ($newpoints + $drow['total']))."\n\n";

		if(($drow['total'] + $newpoints) < 100 && ($drow['total'] + $newpoints) >= 50)
		{
			$body .= _("You now have over 50 points, and can now have your name added to client certificates, and issue server certificates for up to 2 years.")."\n\n";
		}

		if(($drow['total'] + $newpoints) >= 100 && $newpoints > 0)
		{
//			$body .= _("You now have over 100 points and can start assuring others.")."\n\n";
			$body .= _("You have at least 100 Assurance Points, if you want to become an assurer try the")." ";
			$body .= _("Assurer Challenge")." ( https://cats.cacert.org )\n\n";
			$body .= _("To make it easier for others in your area to find you, it's helpful to list yourself as an assurer (this is voluntary), as well as a physical location where you live or work the most. You can flag your account to be listed, and add a comment to the display by going to:")."\n\n";
			$body .= "https://www.cacert.org/wot.php?id=8\n\n";
			$body .= _("You can list your location by going to:")."\n\n";
			$body .= "https://www.cacert.org/wot.php?id=13\n\n";
		}

		if($_SESSION['profile']['board'] == 1 && intval($_POST['expire']) > 0)
			$body .= sprintf(_("Please Note: this is a temporary increase for %s days only. After that time your points will be reduced to 150 points."), intval($_POST['expire']))."\n\n";

		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team");

		sendmail($_SESSION['_config']['notarise']['email'], "[CAcert.org] "._("You've been Assured."), $body, "support@cacert.org", "", "", "CAcert Website");

		putenv("LANG=".$_SESSION['profile']['language']);
		setlocale(LC_ALL, $_SESSION['profile']['language']);

		$body  = sprintf(_("You are receiving this email because you have assured %s %s (%s)."), $_SESSION['_config']['notarise']['fname'], $_SESSION['_config']['notarise']['lname'], $_SESSION['_config']['notarise']['email'])."\n\n";
		if($_POST['points'] != $newpoints)
			$body .= sprintf(_("You issued %s points however the system has rounded this down to %s and they now have %s points in total."), $_POST['points'], $newpoints, ($newpoints + $drow['total']))."\n\n";
		else
			$body .= sprintf(_("You issued %s points and they now have %s points in total."), $newpoints, ($newpoints + $drow['total']))."\n\n";

		if($_SESSION['profile']['board'] == 1 && intval($_POST['expire']) > 0)
			$body .= sprintf(_("Please Note: this is a temporary increase for %s days only. After that time their points will be reduced to 150 points."), intval($_POST['expire']))."\n\n";
		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team");

		sendmail($_SESSION['profile']['email'], "[CAcert.org] "._("You've Assured Another Member."), $body, "support@cacert.org", "", "", "CAcert Support");

		if($_SESSION['profile']['board'] == 1 && intval($_POST['expire']) > 0)
		{
			$body  = sprintf("%s %s (%s) has issued a temporary increase to 200 points for %s %s (%s) for %s days. This action was sponsored by %s %s (%s).", $_SESSION['profile']['fname'], $_SESSION['profile']['lname'], $_SESSION['profile']['email'], $_SESSION['_config']['notarise']['fname'], $_SESSION['_config']['notarise']['lname'], $_SESSION['_config']['notarise']['email'], intval($_POST['expire']), $sponsor['fname'], $sponsor['lname'], $sponsor['email'])."\n\n";

			sendmail("cacert-board@lists.cacert.org", "[CAcert.org] Temporary Increase Issued.", $body, "website@cacert.org", "", "", "CAcert Website");
		}

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

		$info = mysql_escape_string(strip_tags(stripslashes($_POST['contactinfo'])));
		$listme = intval($_POST['listme']);
		if($listme < 0 || $listme > 1)
			$listme = 0;

		$_SESSION['profile']['listme'] = $listme;
		$_SESSION['profile']['contactinfo'] = $info;

		$query = "update `users` set `listme`='$listme',`contactinfo`='$info' where `id`='".$_SESSION['profile']['id']."'";
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
			$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='$userid' and `listme`=1"));
	                $points = mysql_num_rows(mysql_query("select sum(`points`) as `total` from `notary`
						where `to`='".$user['id']."' group by `to` HAVING SUM(`points`) > 0"));
			if($points > 0)
			{
				sendmail($user['email'], "[CAcert.org] ".$_REQUEST['subject'], $_REQUEST['message'],
					$_SESSION['profile']['email'], "", "", $_SESSION['profile']['fname']." ".$_SESSION['profile']['lname']);
				show_page("ContactAssurer",_("Your email has been sent to")." ".$user['fname'].".<br />[ <a href='javascript:history.go(-2)'>"._("Go Back")."</a> ]","");
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
