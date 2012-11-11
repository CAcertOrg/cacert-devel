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

require_once('../includes/lib/l10n.php');

        $id = 0; if(array_key_exists("id",$_REQUEST)) $id=intval($_REQUEST['id']);
        $oldid = 0; if(array_key_exists("oldid",$_REQUEST)) $oldid=intval($_REQUEST['oldid']);
        $process = ""; if(array_key_exists("process",$_REQUEST)) $process=$_REQUEST['process'];

        if($id == 2)
                $id = 0;

        $_SESSION['_config']['errmsg'] = "";

	if($id == 17 || $id == 20)
	{
		include_once("../pages/index/$id.php");
		exit;
	}

	loadem("index");

	$_SESSION['_config']['hostname'] = $_SERVER['HTTP_HOST'];

	if(($oldid == 6 || $id == 6) && intval($_SESSION['lostpw']['user']['id']) < 1)
	{
		$oldid = 0;
		$id = 5;
	}

	if($oldid == 6 && $process != "")
	{
		$body = "";
		$answers = 0;
		$qs = array();
		$id = $oldid;
		$oldid = 0;
		if(array_key_exists('Q1',$_REQUEST) && $_REQUEST['Q1'])
		{
			$_SESSION['lostpw']['A1'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A1']))));

			if(stripslashes(strtolower($_SESSION['lostpw']['A1'])) == strtolower($_SESSION['lostpw']['user']['A1']))
				$answers++;
			$body .= "System: ".$_SESSION['lostpw']['user']['A1']."\nEntered: ".stripslashes(strip_tags($_SESSION['lostpw']['A1']))."\n";
		}
		if(array_key_exists('Q2',$_REQUEST) && $_REQUEST['Q2'])
		{
			$_SESSION['lostpw']['A2'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A2']))));

			if(stripslashes(strtolower($_SESSION['lostpw']['A2'])) == strtolower($_SESSION['lostpw']['user']['A2']))
				$answers++;
			$body .= "System: ".$_SESSION['lostpw']['user']['A2']."\nEntered: ".stripslashes(strip_tags($_SESSION['lostpw']['A2']))."\n";
		}
		if(array_key_exists('Q3',$_REQUEST) && $_REQUEST['Q3'])
		{
			$_SESSION['lostpw']['A3'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A3']))));

			if(stripslashes(strtolower($_SESSION['lostpw']['A3'])) == strtolower($_SESSION['lostpw']['user']['A3']))
				$answers++;
			$body .= "System: ".$_SESSION['lostpw']['user']['A3']."\nEntered: ".stripslashes(strip_tags($_SESSION['lostpw']['A3']))."\n";
		}
		if(array_key_exists('Q4',$_REQUEST) && $_REQUEST['Q4'])
		{
			$_SESSION['lostpw']['A4'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A4']))));

			if(stripslashes(strtolower($_SESSION['lostpw']['A4'])) == strtolower($_SESSION['lostpw']['user']['A4']))
				$answers++;
			$body .= "System: ".$_SESSION['lostpw']['user']['A4']."\nEntered: ".stripslashes(strip_tags($_SESSION['lostpw']['A4']))."\n";
		}
		if(array_key_exists('Q5',$_REQUEST) && $_REQUEST['Q5'])
		{
			$_SESSION['lostpw']['A5'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A5']))));

			if(stripslashes(strtolower($_SESSION['lostpw']['A5'])) == strtolower($_SESSION['lostpw']['user']['A5']))
				$answers++;
			$body .= "System: ".$_SESSION['lostpw']['user']['A5']."\nEntered: ".stripslashes(strip_tags($_SESSION['lostpw']['A5']))."\n";
		}

		$_SESSION['lostpw']['pw1'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['newpass1']))));
		$_SESSION['lostpw']['pw2'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['newpass2']))));

		if($answers < $_SESSION['lostpw']['total'] || $answers < 3)
		{
			$body = "Someone has just attempted to update the pass phrase on the following account:\n".
				"Username(ID): ".$_SESSION['lostpw']['user']['email']."(".$_SESSION['lostpw']['user']['id'].")\n".
				"email: ".$_SESSION['lostpw']['user']['email']."\n".
				"IP/Hostname: ".$_SERVER['REMOTE_ADDR'].(array_key_exists('REMOTE_HOST',$_SERVER)?"/".$_SERVER['REMOTE_HOST']:"")."\n".
				"---------------------------------------------------------------------\n".$body.
				"---------------------------------------------------------------------\n";
			sendmail("support@cacert.org", "[CAcert.org] Requested Pass Phrase Change", $body,
				$_SESSION['lostpw']['user']['email'], "", "", $_SESSION['lostpw']['user']['fname']);
			$_SESSION['_config']['errmsg'] = _("You failed to get all answers correct or you didn't configure enough lost password questions for your account. System admins have been notified.");
		} else if($_SESSION['lostpw']['pw1'] != $_SESSION['lostpw']['pw2'] || $_SESSION['lostpw']['pw1'] == "") {
			$_SESSION['_config']['errmsg'] = _("New Pass Phrases specified don't match or were blank.");
		} else if(strlen($_SESSION['lostpw']['pw1']) < 6) {
			$_SESSION['_config']['errmsg'] = _("The Pass Phrase you submitted was too short. It must be at least 6 characters.");
		} else {
			$score = checkpw($_SESSION['lostpw']['pw1'], $_SESSION['lostpw']['user']['email'], $_SESSION['lostpw']['user']['fname'],
				$_SESSION['lostpw']['user']['mname'], $_SESSION['lostpw']['user']['lname'], $_SESSION['lostpw']['user']['suffix']);
			if($score < 3)
			{
				$_SESSION['_config']['errmsg'] = sprintf(_("The Pass Phrase you submitted failed to contain enough differing characters and/or contained words from your name and/or email address. Only scored %s points out of 6."), $score);
			} else {
				$query = "update `users` set `password`=sha1('".$_SESSION['lostpw']['pw1']."')
						where `id`='".intval($_SESSION['lostpw']['user']['id'])."'";
				mysql_query($query) || die(mysql_error());
				showheader(_("Welcome to CAcert.org"));
				echo _("Your Pass Phrase has been changed now. You can now login with your new password.");
				showfooter();
				exit;
			}
		}		
	}

	if($oldid == 5 && $process != "")
	{
		$email = $_SESSION['lostpw']['email'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['email']))));
		$_SESSION['lostpw']['day'] = intval($_REQUEST['day']);
		$_SESSION['lostpw']['month'] = intval($_REQUEST['month']);
		$_SESSION['lostpw']['year'] = intval($_REQUEST['year']);
		$dob = $_SESSION['lostpw']['year']."-".$_SESSION['lostpw']['month']."-".$_SESSION['lostpw']['day'];
		$query = "select * from `users` where `email`='$email' and `dob`='$dob'";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$id = $oldid;
			$oldid = 0;
			$_SESSION['_config']['errmsg'] = _("Unable to match your details with any user accounts on file");
		} else {
			$id = 6;
			$_SESSION['lostpw']['user'] = mysql_fetch_assoc($res);
		}
	}

	if($id == 4 && $_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'])
	{
		include_once("../includes/lib/general.php");
		$user_id = get_user_id_from_cert($_SERVER['SSL_CLIENT_M_SERIAL'],
				$_SERVER['SSL_CLIENT_I_DN_CN']);
		
		if($user_id >= 0)
		{
			$_SESSION['profile'] = mysql_fetch_assoc(mysql_query(
				"select * from `users` where 
				`id`='$user_id' and `deleted`=0 and `locked`=0"));
			
			if($_SESSION['profile']['id'] != 0)
			{
				$_SESSION['profile']['loggedin'] = 1;
				header("location: https://".$_SERVER['HTTP_HOST']."/account.php");
				exit;
			} else {
				$_SESSION['profile']['loggedin'] = 0;
			}
		}
	}

	if($id == 4 && array_key_exists('profile',$_SESSION) && array_key_exists('loggedin',array($_SESSION['profile'])) && $_SESSION['profile']['loggedin'] == 1)
	{
		header("location: https://".$_SERVER['HTTP_HOST']."/account.php");
		exit;
	}

	function getOTP64($otp)
	{
		$lookupChar = "123456789abcdefhkmnprstuvwxyzABCDEFGHKMNPQRSTUVWXYZ=+[]&@#*!-?%:";

		for($i = 0; $i < 6; $i++)
			$val[$i] = hexdec(substr($otp, $i * 2, 2));

		$tmp1 = $val[0] >> 2;
		$OTP = $lookupChar[$tmp1 & 63];
		$tmp2 = $val[0] - ($tmp1 << 2);
		$tmp1 = $val[1] >> 4;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 63];
		$tmp2 = $val[1] - ($tmp1 << 4);
		$tmp1 = $val[2] >> 6;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 63];
		$tmp2 = $val[2] - ($tmp1 << 6);
		$OTP .= $lookupChar[$tmp2 & 63];
		$tmp1 = $val[3] >> 2;
		$OTP .= $lookupChar[$tmp1 & 63];
		$tmp2 = $val[3] - ($tmp1 << 2);
		$tmp1 = $val[4] >> 4;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 63];
		$tmp2 = $val[4] - ($tmp1 << 4);
		$tmp1 = $val[5] >> 6;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 63];
		$tmp2 = $val[5] - ($tmp1 << 6);
		$OTP .= $lookupChar[$tmp2 & 63];

		return $OTP;
	}

	function getOTP32($otp)
	{
		$lookupChar = "0123456789abcdefghkmnoprstuvwxyz";

		for($i = 0; $i < 7; $i++)
			$val[$i] = hexdec(substr($otp, $i * 2, 2));

		$tmp1 = $val[0] >> 3;
		$OTP = $lookupChar[$tmp1 & 31];
		$tmp2 = $val[0] - ($tmp1 << 3);
		$tmp1 = $val[1] >> 6;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 31];
		$tmp2 = ($val[1] - ($tmp1 << 6)) >> 1;
		$OTP .= $lookupChar[$tmp2 & 31];
		$tmp2 = $val[1] - (($val[1] >> 1) << 1);
		$tmp1 = $val[2] >> 4;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 31];
		$tmp2 = $val[2] - ($tmp1 << 4);
		$tmp1 = $val[3] >> 7;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 31];
		$tmp2 = ($val[3] - ($tmp1 << 7)) >> 2;
		$OTP .= $lookupChar[$tmp2 & 31];
		$tmp2 = $val[3] - (($val[3] - ($tmp1 << 7)) >> 2) << 2;
		$tmp1 = $val[4] >> 5;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 31];
		$tmp2 = $val[4] - ($tmp1 << 5);
		$OTP .= $lookupChar[$tmp2 & 31];
		$tmp1 = $val[5] >> 3;
		$OTP .= $lookupChar[$tmp1 & 31];
		$tmp2 = $val[5] - ($tmp1 << 3);
		$tmp1 = $val[6] >> 6;
		$OTP .= $lookupChar[($tmp1 + $tmp2) & 31];

		return $OTP;
       }

	if($oldid == 4)
	{
		$oldid = 0;
		$id = 4;

		$_SESSION['_config']['errmsg'] = "";

		$email = mysql_escape_string(stripslashes(strip_tags(trim($_REQUEST['email']))));
		$pword = mysql_escape_string(stripslashes(trim($_REQUEST['pword'])));
		$query = "select * from `users` where `email`='$email' and (`password`=old_password('$pword') or `password`=sha1('$pword') or
						`password`=password('$pword')) and `verified`=1 and `deleted`=0 and `locked`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$otpquery = "select * from `users` where `email`='$email' and `otphash`!='' and `verified`=1 and `deleted`=0 and `locked`=0";
			$otpres = mysql_query($otpquery);
			if(mysql_num_rows($otpres) > 0)
			{
				$otp = mysql_fetch_assoc($otpres);
				$otphash = $otp['otphash'];
				$otppin = $otp['otppin'];
				if(strlen($pword) == 6)
				{
					$matchperiod = 18;
					$time = round(gmdate("U") / 10);
				} else {
					$matchperiod = 3;
					$time = round(gmdate("U") / 60);
				}

				$query = "delete from `otphashes` where UNIX_TIMESTAMP(`when`) <= UNIX_TIMESTAMP(NOW()) - 600";
				mysql_query($query);

				$query = "select * from `otphashes` where `username`='$email' and `otp`='$pword'";
				if(mysql_num_rows(mysql_query($query)) <= 0)
				{
					$query = "insert into `otphashes` set `when`=NOW(), `username`='$email', `otp`='$pword'";
					mysql_query($query);
					for($i = $time - $matchperiod; $i <= $time + $matchperiod * 2; $i++)
					{
						if($otppin > 0)
							$tmpmd5 = md5("$i$otphash$otppin");
						else
							$tmpmd5 = md5("$i$otphash");

						if(strlen($pword) == 6)
							$md5 = substr(md5("$i$otphash"), 0, 6);
						else if(strlen($pword) == 8)
							$md5 = getOTP64(md5("$i$otphash"));
						else
							$md5 = getOTP32(md5("$i$otphash"));

						if($pword == $md5)
							$res = mysql_query($otpquery);
					}
				}
			}
		}
		if(mysql_num_rows($res) > 0)
		{
			$_SESSION['profile'] = "";
			unset($_SESSION['profile']);
			$_SESSION['profile'] = mysql_fetch_assoc($res);
			$query = "update `users` set `modified`=NOW(), `password`=sha1('$pword') where `id`='".$_SESSION['profile']['id']."'";
			mysql_query($query);

			if($_SESSION['profile']['language'] == "")
			{
				$query = "update `users` set `language`='".L10n::get_translation()."'
						where `id`='".$_SESSION['profile']['id']."'";
				mysql_query($query);
			} else {
				L10n::set_translation($_SESSION['profile']['language']);
				L10n::init_gettext();
			}
			$query = "select sum(`points`) as `total` from `notary` where `to`='".$_SESSION['profile']['id']."' group by `to`";
			$res = mysql_query($query);
			$row = mysql_fetch_assoc($res);
			$_SESSION['profile']['points'] = $row['total'];
			$_SESSION['profile']['loggedin'] = 1;
			if($_SESSION['profile']['Q1'] == "" || $_SESSION['profile']['Q2'] == "" ||
				$_SESSION['profile']['Q3'] == "" || $_SESSION['profile']['Q4'] == "" ||
				$_SESSION['profile']['Q5'] == "")
			{
				$_SESSION['_config']['errmsg'] .= _("For your own security you must enter 5 lost password questions and answers.")."<br>";
				$_SESSION['_config']['oldlocation'] = "account.php?id=13";
			}
			if (checkpwlight($pword) < 3)
				$_SESSION['_config']['oldlocation'] = "account.php?id=14&force=1";
			if($_SESSION['_config']['oldlocation'] != "")
				header("location: https://".$_SERVER['HTTP_HOST']."/".$_SESSION['_config']['oldlocation']);
			else
				header("location: https://".$_SERVER['HTTP_HOST']."/account.php");
			exit;
		}

		$query = "select * from `users` where `email`='$email' and (`password`=old_password('$pword') or `password`=sha1('$pword') or
						`password`=password('$pword')) and `verified`=0 and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$_SESSION['_config']['errmsg'] = _("Incorrect email address and/or Pass Phrase.");
		} else {
			$_SESSION['_config']['errmsg'] = _("Your account has not been verified yet, please check your email account for the signup messages.");
		}
	}

	if($process && $oldid == 1)
	{
		$id = 2;
		$oldid = 0;

		$_SESSION['_config']['errmsg'] = "";

		$_SESSION['signup']['email'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['email']))));
		$_SESSION['signup']['fname'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['fname']))));
		$_SESSION['signup']['mname'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['mname']))));
		$_SESSION['signup']['lname'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['lname']))));
		$_SESSION['signup']['suffix'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['suffix']))));
		$_SESSION['signup']['day'] = intval($_REQUEST['day']);
		$_SESSION['signup']['month'] = intval($_REQUEST['month']);
		$_SESSION['signup']['year'] = intval($_REQUEST['year']);
		$_SESSION['signup']['pword1'] = trim(mysql_escape_string(stripslashes($_REQUEST['pword1'])));
		$_SESSION['signup']['pword2'] = trim(mysql_escape_string(stripslashes($_REQUEST['pword2'])));
		$_SESSION['signup']['Q1'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['Q1']))));
		$_SESSION['signup']['Q2'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['Q2']))));
		$_SESSION['signup']['Q3'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['Q3']))));
		$_SESSION['signup']['Q4'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['Q4']))));
		$_SESSION['signup']['Q5'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['Q5']))));
		$_SESSION['signup']['A1'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A1']))));
		$_SESSION['signup']['A2'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A2']))));
		$_SESSION['signup']['A3'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A3']))));
		$_SESSION['signup']['A4'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A4']))));
		$_SESSION['signup']['A5'] = trim(mysql_escape_string(stripslashes(strip_tags($_REQUEST['A5']))));
		$_SESSION['signup']['general'] = intval(array_key_exists('general',$_REQUEST)?$_REQUEST['general']:0);
		$_SESSION['signup']['country'] = intval(array_key_exists('country',$_REQUEST)?$_REQUEST['country']:0);
		$_SESSION['signup']['regional'] = intval(array_key_exists('regional',$_REQUEST)?$_REQUEST['regional']:0);
		$_SESSION['signup']['radius'] = intval(array_key_exists('radius',$_REQUEST)?$_REQUEST['radius']:0);
		$_SESSION['signup']['cca_agree'] = intval(array_key_exists('cca_agree',$_REQUEST)?$_REQUEST['cca_agree']:0);


		if($_SESSION['signup']['Q1'] == $_SESSION['signup']['Q2'] ||
			$_SESSION['signup']['Q1'] == $_SESSION['signup']['Q3'] ||
			$_SESSION['signup']['Q1'] == $_SESSION['signup']['Q4'] ||
			$_SESSION['signup']['Q1'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['Q2'] == $_SESSION['signup']['Q3'] ||
			$_SESSION['signup']['Q2'] == $_SESSION['signup']['Q4'] ||
			$_SESSION['signup']['Q2'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['Q3'] == $_SESSION['signup']['Q4'] ||
			$_SESSION['signup']['Q3'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['Q4'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['Q1'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['Q2'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['Q3'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['Q4'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['A2'] == $_SESSION['signup']['Q3'] ||
			$_SESSION['signup']['A2'] == $_SESSION['signup']['Q4'] ||
			$_SESSION['signup']['A2'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['A3'] == $_SESSION['signup']['Q4'] ||
			$_SESSION['signup']['A3'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['A4'] == $_SESSION['signup']['Q5'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['A2'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['A3'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['A4'] ||
			$_SESSION['signup']['A1'] == $_SESSION['signup']['A5'] ||
			$_SESSION['signup']['A2'] == $_SESSION['signup']['A3'] ||
			$_SESSION['signup']['A2'] == $_SESSION['signup']['A4'] ||
			$_SESSION['signup']['A2'] == $_SESSION['signup']['A5'] ||
			$_SESSION['signup']['A3'] == $_SESSION['signup']['A4'] ||
			$_SESSION['signup']['A3'] == $_SESSION['signup']['A5'] ||
			$_SESSION['signup']['A4'] == $_SESSION['signup']['A5'])
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("For your own security you must enter 5 different secret questions and answers. You aren't allowed to duplicate questions, set questions as answers or use the question as the answer. The questions and answers must be unambiguous and easy to remember forever, that means permanent issues that never change, not current issues, even more no random strings.")."<br>\n";
		}

		if($_SESSION['signup']['Q1'] == "" || $_SESSION['signup']['Q2'] == "" ||
			$_SESSION['signup']['Q3'] == "" || $_SESSION['signup']['Q4'] == "" ||
			$_SESSION['signup']['Q5'] == "")
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("For your own security you must enter 5 lost password questions and answers.")."<br>\n";
		}
		if($_SESSION['signup']['fname'] == "" || $_SESSION['signup']['lname'] == "")
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("First and/or last names were blank.")."<br>\n";
		}
		if($_SESSION['signup']['year'] < 1900 || $_SESSION['signup']['month'] < 1 || $_SESSION['signup']['month'] > 12 ||
			$_SESSION['signup']['day'] < 1 || $_SESSION['signup']['day'] > 31 ||
			!checkdate($_SESSION['signup']['month'],$_SESSION['signup']['day'],$_SESSION['signup']['year']) ||
			mktime(0,0,0,$_SESSION['signup']['month'],$_SESSION['signup']['day'],$_SESSION['signup']['year']) > time() )
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("Invalid date of birth")."<br>\n";
		}
		if($_SESSION['signup']['cca_agree'] == "0")
		{
		        $id = 1;
		        $_SESSION['_config']['errmsg'] .= _("You have to agree to the CAcert Community agreement.")."<br>\n";
		}
		if($_SESSION['signup']['email'] == "")
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("Email Address was blank")."<br>\n";
		}
		if($_SESSION['signup']['pword1'] == "")
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("Pass Phrases were blank")."<br>\n";
		}
		if($_SESSION['signup']['pword1'] != $_SESSION['signup']['pword2'])
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] .= _("Pass Phrases don't match")."<br>\n";
		}

		$score = checkpw($_SESSION['signup']['pword1'], $_SESSION['signup']['email'], $_SESSION['signup']['fname'], $_SESSION['signup']['mname'], $_SESSION['signup']['lname'], $_SESSION['signup']['suffix']);
		if($score < 3)
		{
			$id = 1;
			$_SESSION['_config']['errmsg'] = _("The Pass Phrase you submitted failed to contain enough differing characters and/or contained words from your name and/or email address. Only scored $score points out of 6.");
		}

		if($id == 2)
		{
			$query = "select * from `email` where `email`='".$_SESSION['signup']['email']."' and `deleted`=0";
			$res1 = mysql_query($query);

			$query = "select * from `users` where `email`='".$_SESSION['signup']['email']."' and `deleted`=0";
			$res2 = mysql_query($query);
			if(mysql_num_rows($res1) > 0 || mysql_num_rows($res2) > 0)
			{
				$id = 1;
				$_SESSION['_config']['errmsg'] .= _("This email address is currently valid in the system.")."<br>\n";
			}

			$query = "select `domain` from `baddomains` where `domain`=RIGHT('".$_SESSION['signup']['email']."', LENGTH(`domain`))";
			$res = mysql_query($query);
			if(mysql_num_rows($res) > 0)
			{
				$domain = mysql_fetch_assoc($res);
				$domain = $domain['domain'];
				$id = 1;
				$_SESSION['_config']['errmsg'] .= sprintf(_("We don't allow signups from people using email addresses from %s"), $domain)."<br>\n";
			}
		}

		if($id == 2)
		{
			$checkemail = checkEmail($_SESSION['signup']['email']);
			if($checkemail != "OK")
			{
				$id = 1;
				if (substr($checkemail, 0, 1) == "4") 
				{
					$_SESSION['_config']['errmsg'] .= _("The mail server responsible for your domain indicated a temporary failure. This may be due to anti-SPAM measures, such as greylisting. Please try again in a few minutes.");
				} else {
					$_SESSION['_config']['errmsg'] .= _("Email Address given was invalid, or a test connection couldn't be made to your server, or the server rejected the email address as invalid");
				}
				$_SESSION['_config']['errmsg'] .= "<br>\n$checkemail<br>\n";
			}
		}

		if($id == 2)
		{
			$hash = make_hash();

			$query = "insert into `users` set `email`='".$_SESSION['signup']['email']."',
							`password`=sha1('".$_SESSION['signup']['pword1']."'),
							`fname`='".$_SESSION['signup']['fname']."',
							`mname`='".$_SESSION['signup']['mname']."',
							`lname`='".$_SESSION['signup']['lname']."',
							`suffix`='".$_SESSION['signup']['suffix']."',
							`dob`='".$_SESSION['signup']['year']."-".$_SESSION['signup']['month']."-".$_SESSION['signup']['day']."',
							`Q1`='".$_SESSION['signup']['Q1']."',
							`Q2`='".$_SESSION['signup']['Q2']."',
							`Q3`='".$_SESSION['signup']['Q3']."',
							`Q4`='".$_SESSION['signup']['Q4']."',
							`Q5`='".$_SESSION['signup']['Q5']."',
							`A1`='".$_SESSION['signup']['A1']."',
							`A2`='".$_SESSION['signup']['A2']."',
							`A3`='".$_SESSION['signup']['A3']."',
							`A4`='".$_SESSION['signup']['A4']."',
							`A5`='".$_SESSION['signup']['A5']."',
							`created`=NOW(), `uniqueID`=SHA1(CONCAT(NOW(),'$hash'))";
			mysql_query($query);
			$memid = mysql_insert_id();
			$query = "insert into `email` set `email`='".$_SESSION['signup']['email']."',
							`hash`='$hash',
							`created`=NOW(),
							`memid`='$memid'";
			mysql_query($query);
			$emailid = mysql_insert_id();
			$query = "insert into `alerts` set `memid`='$memid',
						`general`='".$_SESSION['signup']['general']."',
						`country`='".$_SESSION['signup']['country']."',
						`regional`='".$_SESSION['signup']['regional']."',
						`radius`='".$_SESSION['signup']['radius']."'";
			mysql_query($query);

			$body = _("Thanks for signing up with CAcert.org, below is the link you need to open to verify your account. Once your account is verified you will be able to start issuing certificates till your hearts' content!")."\n\n";
			$body .= "http://".$_SESSION['_config']['normalhostname']."/verify.php?type=email&emailid=$emailid&hash=$hash\n\n";
			$body .= _("Best regards")."\n"._("CAcert.org Support!");

			sendmail($_SESSION['signup']['email'], "[CAcert.org] "._("Mail Probe"), $body, "support@cacert.org", "", "", "CAcert Support");
			foreach($_SESSION['signup'] as $key => $val)
				$_SESSION['signup'][$key] = "";
			unset($_SESSION['signup']);
		}
	}

	if($oldid == 11 && $process != "")
	{
		$who = stripslashes($_REQUEST['who']);
		$email = stripslashes($_REQUEST['email']);
		$subject = stripslashes($_REQUEST['subject']);
		$message = stripslashes($_REQUEST['message']);
		$secrethash = $_REQUEST['secrethash2'];

		if($_SESSION['_config']['secrethash'] != $secrethash || $secrethash == "" || $_SESSION['_config']['secrethash'] == "")
		{
			$id = $oldid;
			$process = "";
			$_SESSION['_config']['errmsg'] = _("This seems like you have cookies or Javascript disabled, cannot continue.");
			$oldid = 0;

			$message = "From: $who\nEmail: $email\nSubject: $subject\n\nMessage:\n".$message;
			sendmail("support@cacert.org", "[CAcert.org] Possible SPAM", $message, $email, "", "", "CAcert Support");
			//echo "Alert! Alert! Alert! SPAM SPAM SPAM!!!<br><br><br>";
			//if($_SESSION['_config']['secrethash'] != $secrethash) echo "Hash does not match: $secrethash vs. ".$_SESSION['_config']['secrethash']."\n";
			echo _("This seems like you have cookies or Javascript disabled, cannot continue.");
			die;
		}
		if(strstr($subject, "botmetka") || strstr($subject, "servermetka") || strstr($who,"\n") || strstr($email,"\n") || strstr($subject,"\n") )
		{
			$id = $oldid;
			$process = "";
			$_SESSION['_config']['errmsg'] = _("This seems like potential spam, cannot continue.");
			$oldid = 0;

			$message = "From: $who\nEmail: $email\nSubject: $subject\n\nMessage:\n".$message;
			sendmail("support@cacert.org", "[CAcert.org] Possible SPAM", $message, $email, "", "", "CAcert Support");
			//echo "Alert! Alert! Alert! SPAM SPAM SPAM!!!<br><br><br>";
			//if($_SESSION['_config']['secrethash'] != $secrethash) echo "Hash does not match: $secrethash vs. ".$_SESSION['_config']['secrethash']."\n";
			echo _("This seems like potential spam, cannot continue.");
			die;
		}


		if(trim($who) == "" || trim($email) == "" || trim($subject) == "" || trim($message) == "")
		{
			$id = $oldid;
			$process = "";
			$_SESSION['_config']['errmsg'] = _("All fields are mandatory.")."<br>\n";
			$oldid = 0;
		}
	}

	if($oldid == 11 && $process != "" && $_REQUEST['support'] != "yes")
	{
		$message = "From: $who\nEmail: $email\nSubject: $subject\n\nMessage:\n".$message;

		sendmail("support@cacert.org", "[CAcert.org] ".$subject, $message, $email, "", "", "CAcert Support");
		showheader(_("Welcome to CAcert.org"));
		echo _("Your message has been sent.");
		showfooter();
		exit;
	}

	if($oldid == 11 && $process != "" && $_REQUEST['support'] == "yes")
	{
		$message = "From: $who\nEmail: $email\nSubject: $subject\n\nMessage:\n".$message;

		sendmail("cacert-support@lists.cacert.org", "[website form email]: ".$subject, $message, "website-form@cacert.org", "cacert-support@lists.cacert.org, $email", "", "CAcert-Website");
		showheader(_("Welcome to CAcert.org"));
		echo _("Your message has been sent to the general support list.");
		showfooter();
		exit;
	}

	if(!array_key_exists('signup',$_SESSION) || $_SESSION['signup']['year'] < 1900)
		$_SESSION['signup']['year'] = "19XX";

	if ($id == 12)
	{
		$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
		$newUrl = $protocol . '://wiki.cacert.org/FAQ/AboutUs';
		header('Location: '.$newUrl, true, 301); // 301 = Permanently Moved
	}
	
	if ($id == 19)
	{
		$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
		$newUrl = $protocol . '://wiki.cacert.org/FAQ/Privileges';
		header('Location: '.$newUrl, true, 301); // 301 = Permanently Moved
	}

	if ($id == 8)
	{
		$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
		$newUrl = $protocol . '://wiki.cacert.org/Board';
		header('Location: '.$newUrl, true, 301); // 301 = Permanently Moved
	}
	
	showheader(_("Welcome to CAcert.org"));
	includeit($id);
	showfooter();
?>
