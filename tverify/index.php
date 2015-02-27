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
//	phpinfo(); exit;
	include_once("../includes/general.php");
	loadem("tverify");

	$id = intval($_GET['id']);
	if(intval($_REQUEST['id']) > 0)
		$id = intval($_REQUEST['id']);

	if($id == 1)
	{
		$nofile = 1;
		$filename = "";
		$photoid = $_FILES['photoid'];
		if($photoid['error'] == 0 && $_REQUEST["notaryURL"] != "")
		{
			$filename = $photoid['tmp_name'];
			$do = trim(`file -b -i $filename`);
			$type = strtolower($do);
			switch($type)
			{
				case 'image/gif': $ext = "gif"; $nofile = 0; break;
				case 'image/jpeg': $ext = "jpg"; $nofile = 0; break;
				case 'image/jpg': $ext = "jpg"; $nofile = 0; break;
				case 'image/png': $ext = "png"; $nofile = 0; break;
				default:
					$id = 0;
					$_SESSION['_config']['errmsg'] = _("Only jpg, gif and png file types are acceptable, your browser sent a file of type: ").$type;
			}
		}
	}

	if($id == 1)
	{
		$email = mysql_real_escape_string(trim($_REQUEST["email"]));
		$password = mysql_real_escape_string(stripslashes(trim($_REQUEST["pword"])));
		$URL = mysql_real_escape_string(trim($_REQUEST["notaryURL"]));
		$CN = mysql_real_escape_string($_SESSION['_config']['CN']);
		$memid = intval($_SESSION['_config']['uid']);
		$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='$memid'"));
		$tmp = mysql_fetch_assoc(mysql_query("select sum(`points`) as `points` from `notary` where `to`='$memid'"));

		if($URL != "" && $nofile == 0)
			$max = 150;
		else if($URL != "")
			$max = 90;
		else
			$max = 50;

		if($URL != "")
		if(!preg_match("/^https:\/\/www\.thawte\.com\/cgi\/personal\/wot\/directory\.exe\?(.*?&)?node=\d+(&.*)?$/",$URL))
		{
			showheader(_("Thawte Points Transfer"));
			echo _("You failed to enter a valid Thawte Notary URL.");
			showfooter();
			exit;
		}

		if($tmp['points'] >= $max)
		{
			showheader(_("Thawte Points Transfer"));
			echo _("Your request would not gain you any more points and will not be taken any further.").
				sprintf(_("You have %s points already and you would have been issued up to %s points."), $tmp['points'], $max);
			showfooter();
			exit;
		}

	}

	if($id == 1)
	{
		$query = "select * from `users`,`email` where `email`.`memid`='$memid' and `email`.`email`='$email' and `users`.`id`=`email`.`memid` and
				(`password`=old_password('$password') or `password`=sha1('$password') or `password`=password('$password'))";
		if(mysql_num_rows(mysql_query($query)) <= 0)
		{
			$_SESSION['_config']['errmsg'] = _("I'm sorry, I couldn't match your login details (password) to your certificate to an account on this system.");
			$id = 0;
		} else {
			$query = "insert into `tverify` set `memid`='$memid', `URL`='$URL', `CN`='$CN', `created`=NOW()";
			mysql_query($query);
			$tverify = mysql_insert_id();
			if($nofile == 0)
			{
				$filename = $photoid['tmp_name'];
				$newfile = mysql_real_escape_string('/www/photoid/'.$tverify.".".$ext);
				move_uploaded_file($filename, $newfile);
				$query = "update `tverify` set `photoid`='$newfile' where `id`='$tverify'";
				mysql_query($query);
			}
		}
	}

	if($id == 1)
	{
		$points = 0;
		if($URL != "" && $newfile != "")
			$points = 150 - intval($tmp['points']);
		else if($URL != "")
			$points = 90 - intval($tmp['points']);
		else
			$points = 50 - intval($tmp['points']);

		if($points < 0)
			$points = 0;
	}

	if($id == 1 && $max == 50)
	{
		if($points > 0)
		{
			mysql_query("insert into `notary` set `from`='0', `to`='$memid', `points`='$points',
					`method`='Thawte Points Transfer', `when`=NOW()");
			fix_assurer_flag($memid);
		}
		$totalpoints = intval($tmp['points']) + $points;
		mysql_query("update `tverify` set `modified`=NOW() where `id`='$tverify'");

		$body  = _("Your request to have points transfered was sucessful. You were issued $points points as a result, and you now have $totalpoints in total")."\n\n";

		$body .= _("Best regards")."\n";
		$body .= _("CAcert Support Team");
		sendmail($user['email'], "[CAcert.org] Thawte Notary Points Transfer", $body, "website-form@cacert.org", "returns@cacert.org", "", "CAcert Tverify");
	} else if($id == 1) {
		$body  = "There is a new valid request for thawte points tranfer, details as follows:\n\n";
		$body .= "To vote on this application, go to: https://www.cacert.org/account.php?id=52&uid=$tverify\n\n";
		$body .= "Or use the certificate login: https://secure.cacert.org/account.php?id=52&uid=$tverify\n\n";

		$body .= "We know that by signing into https://tverify.cacert.org that\n";
		$body .= "1. they have possession of a cert issued from Thawte\n";
		$body .= "2. the person named in the cert has been verified by Thawte's Web of Trust\n";
		$body .= "3. at least 1 of the emails listed as valid in that cert belongs to a\n";
		$body .= "CAcert.org user\n\n";
		$body .= "It's up to us as voting members to verify the details that can't be\n";
		$body .= "programatically handled, that means checking the ID, and signing into\n";
		$body .= "the Thawte site and validating their name is listed as a notary.\n\n";

		$body .= "Best regards"."\n";
		$body .= "CAcert Support Team";

		sendmail("cacert-tverify@lists.cacert.org", "[CAcert.org] Thawte Notary Points Transfer", $body, "website-form@cacert.org", "returns@cacert.org", "", "CAcert Tverify");
	}

	showheader(_("Thawte Points Transfer"));
	includeit($id, "tverify");
	showfooter();
?>
