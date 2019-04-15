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

	require_once(dirname(__FILE__)."/lib/general.php");

	session_name("cacert");
	session_start();

//	session_register("_config");
//	session_register("profile");
//	session_register("signup");
//	session_register("lostpw");
//	if($_SESSION['profile']['id'] > 0)
//		session_regenerate_id();

	$pageLoadTime_Start = microtime(true);

	$junk = array(_("Face to Face Meeting"), _("Trusted Third Parties"), _("Thawte Points Transfer"), _("Administrative Increase"),
			_("CT Magazine - Germany"), _("Temporary Increase"), _("Unknown"));

	$_SESSION['_config']['errmsg']="";

	$id = 0; if(array_key_exists("id",$_REQUEST)) $id=intval($_REQUEST['id']);
	$oldid = 0; if(array_key_exists("oldid",$_REQUEST)) $oldid=intval($_REQUEST['oldid']);

	$_SESSION['_config']['filepath'] = "/www";

	require_once($_SESSION['_config']['filepath']."/includes/mysql.php");
	require_once($_SESSION['_config']['filepath'].'/includes/lib/account.php');
	require_once($_SESSION['_config']['filepath'].'/includes/lib/l10n.php');

	if(array_key_exists('HTTP_HOST',$_SERVER) &&
			$_SERVER['HTTP_HOST'] != $_SESSION['_config']['normalhostname'] &&
			$_SERVER['HTTP_HOST'] != $_SESSION['_config']['securehostname'] &&
			$_SERVER['HTTP_HOST'] != $_SESSION['_config']['tverify'] &&
			$_SERVER['HTTP_HOST'] != "stamp.cacert.org")
	{
		if(array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] == "on")
			header("location: https://".$_SESSION['_config']['normalhostname']);
		else
			header("location: http://".$_SESSION['_config']['normalhostname']);
		exit;
	}

	if(array_key_exists('HTTP_HOST',$_SERVER) &&
			($_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'] ||
			$_SERVER['HTTP_HOST'] == $_SESSION['_config']['tverify']))
	{
		if(array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] == "on")
		{
		}
		else
		{
			if($_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'])
			header("location: https://". $_SESSION['_config']['securehostname']);
			if($_SERVER['HTTP_HOST'] == $_SESSION['_config']['tverify'])
			header("location: https://".$_SESSION['_config']['tverify']);
			exit;
		}
	}

	L10n::detect_language();
	L10n::init_gettext();

	if(array_key_exists('profile',$_SESSION) && is_array($_SESSION['profile']) && array_key_exists('id',$_SESSION['profile']) && $_SESSION['profile']['id'] > 0)
	{
		$locked = mysql_fetch_assoc(mysql_query("select `locked` from `users` where `id`='".intval($_SESSION['profile']['id'])."'"));
		if($locked['locked'] == 0)
		{
			$query = "select sum(`points`) as `total` from `notary` where `to`='".intval($_SESSION['profile']['id'])."' and `deleted` = 0 group by `to`";
			$res = mysql_query($query);
			$row = mysql_fetch_assoc($res);
			$_SESSION['profile']['points'] = $row['total'];
		} else {
			$_SESSION['profile'] = "";
			unset($_SESSION['profile']);
		}
	}

	function loadem($section = "index")
	{
		if($section != "index" && $section != "account" && $section != "tverify")
		{
			$section = "index";
		}

		if($section == "account")
			include_once($_SESSION['_config']['filepath']."/includes/account_stuff.php");

		if($section == "index")
			include_once($_SESSION['_config']['filepath']."/includes/general_stuff.php");

		if($section == "tverify")
			include_once($_SESSION['_config']['filepath']."/includes/tverify_stuff.php");
	}

	function includeit($id = "0", $section = "index")
	{
		$id = intval($id);
		if($section != "index" && $section != "account" && $section != "wot" && $section != "help" && $section != "gpg" && $section != "disputes" && $section != "tverify" && $section != "advertising")
		{
			$section = "index";
		}

		if($section == "tverify" && file_exists($_SESSION['_config']['filepath']."/tverify/index/$id.php"))
			include_once($_SESSION['_config']['filepath']."/tverify/index/$id.php");
		else if(file_exists($_SESSION['_config']['filepath']."/pages/$section/$id.php"))
			include_once($_SESSION['_config']['filepath']."/pages/$section/$id.php");
		else {
			$id = "0";

			if(file_exists($_SESSION['_config']['filepath']."/pages/$section/$id.php"))
				include_once($_SESSION['_config']['filepath']."/pages/$section/$id.php");
			else {

				$section = "index";
				$id = "0";

				if(file_exists($_SESSION['_config']['filepath']."/pages/$section/$id.php"))
					include_once($_SESSION['_config']['filepath']."/pages/$section/$id.php");
				else
					include_once($_SESSION['_config']['filepath']."/www/error404.php");
			}
		}
	}

	function checkpwlight($pwd) {
		$points = 0;

		if(strlen($pwd) > 15)
			$points++;
		if(strlen($pwd) > 20)
			$points++;
		if(strlen($pwd) > 25)
			$points++;
		if(strlen($pwd) > 30)
			$points++;

		//echo "Points due to length: $points<br/>";

		if(preg_match("/\d/", $pwd))
			$points++;

		if(preg_match("/[a-z]/", $pwd))
			$points++;

		if(preg_match("/[A-Z]/", $pwd))
			$points++;

		if(preg_match("/\W/", $pwd))
			$points++;

		if(preg_match("/\s/", $pwd))
			$points++;

		//echo "Points due to length and charset: $points<br/>";

		// check for historical password proposal
		if ($pwd === "Fr3d Sm|7h") {
			return 0;
		}

		return $points;
	}

	function checkpw($pwd, $email, $fname, $mname, $lname, $suffix)
	{
		$points = checkpwlight($pwd);

		if(@strstr(strtolower($pwd), strtolower($email)))
			$points--;

		if(@strstr(strtolower($email), strtolower($pwd)))
			$points--;

		if(@strstr(strtolower($pwd), strtolower($fname)))
			$points--;

		if(@strstr(strtolower($fname), strtolower($pwd)))
			$points--;

		if($mname)
		if(@strstr(strtolower($pwd), strtolower($mname)))
			$points--;

		if($mname)
		if(@strstr(strtolower($mname), strtolower($pwd)))
			$points--;

		if(@strstr(strtolower($pwd), strtolower($lname)))
			$points--;

		if(@strstr(strtolower($lname), strtolower($pwd)))
			$points--;

		if($suffix)
		if(@strstr(strtolower($pwd), strtolower($suffix)))
			$points--;

		if($suffix)
		if(@strstr(strtolower($suffix), strtolower($pwd)))
			$points--;

		//echo "Points due to name matches: $points<br/>";

		$shellpwd = escapeshellarg($pwd);
		$do = shell_exec("grep -F -- $shellpwd /usr/share/dict/american-english");
		if($do)
			$points--;

		//echo "Points due to wordlist: $points<br/>";

		return($points);
	}

	function extractit()
	{
		$bits = explode(": ", $_SESSION['_config']['subject'], 2);
		$bits = str_replace(", ", "|", str_replace("/", "|", array_key_exists('1',$bits)?$bits['1']:""));
		$bits = explode("|", $bits);

		$_SESSION['_config']['cnc'] = $_SESSION['_config']['subaltc'] = 0;
		$_SESSION['_config']['OU'] = "";

		if(is_array($bits))
		foreach($bits as $val)
		{
			if(!strstr($val, "="))
				continue;

			$split = explode("=", $val);

			$k = $split[0];
			$split['1'] = trim($split['1']);
			if($k == "CN" && $split['1'])
			{
				$k = $_SESSION['_config']['cnc'].".".$k;
				$_SESSION['_config']['cnc']++;
				$_SESSION['_config'][$k] = $split['1'];
			}
			if($k == "OU" && $split['1'] && $_SESSION['_config']['OU'] == "")
			{
				$_SESSION['_config']['OU'] = $split['1'];
			}
			if($k == "subjectAltName" && $split['1'])
			{
				$k = $_SESSION['_config']['subaltc'].".".$k;
				$_SESSION['_config']['subaltc']++;
				$_SESSION['_config'][$k] = $split['1'];
			}
		}
	}

	function getcn()
	{
		unset($_SESSION['_config']['rows']);
		unset($_SESSION['_config']['rowid']);
		unset($_SESSION['_config']['rejected']);
		$rows=array();
		$rowid=array();
		for($cnc = 0; $cnc < $_SESSION['_config']['cnc']; $cnc++)
		{
			$CN = $_SESSION['_config']["$cnc.CN"];
			$bits = explode(".", $CN);
			$dom = "";
			$cnok = 0;
			for($i = count($bits) - 1; $i >= 0; $i--)
			{
				if($dom)
					$dom = $bits[$i].".".$dom;
				else
					$dom = $bits[$i];
				$_SESSION['_config']['row'] = "";
				$dom = mysql_real_escape_string($dom);
				$query = "select * from domains where `memid`='".intval($_SESSION['profile']['id'])."' and `domain` like '$dom' and `deleted`=0 and `hash`=''";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					$cnok = 1;
					$_SESSION['_config']['row'] = mysql_fetch_assoc($res);
					$rowid[] = $_SESSION['_config']['row']['id'];
					break;
				}
			}

			if(!preg_match("/(?=^.{4,253}$)(^(?:\\*\\.)?((?!-)[a-zA-Z0-9_-]{1,63}(?<!-)\\.)+[a-zA-Z]{2,63}$)/i", $CN)) {
				$cnok = 0;
			}

			if($cnok == 0) {
				$_SESSION['_config']['rejected'][] = $CN;
				continue;
			}

			if($_SESSION['_config']['row'] != "")
				$rows[] = $CN;
		}
//		if(count($rows) <= 0)
//		{
//			echo _("There were no valid CommonName fields on the CSR, or I was unable to match any of these against your account. Please review your CSR, or add and verify domains contained in it to your account before trying again.");
//			exit;
//		}

		$_SESSION['_config']['rows'] = $rows;
		$_SESSION['_config']['rowid'] = $rowid;
	}

	function getalt()
	{
		unset($_SESSION['_config']['altrows']);
		unset($_SESSION['_config']['altid']);
		$altrows=array();
		$altid=array();
		for($altc = 0; $altc < $_SESSION['_config']['subaltc']; $altc++)
		{
			$subalt = $_SESSION['_config']["$altc.subjectAltName"];
			if(substr($subalt, 0, 4) == "DNS:")
				$alt = substr($subalt, 4);
			else
				continue;

			$bits = explode(".", $alt);
			$dom = "";
			$altok = 0;
			for($i = count($bits) - 1; $i >= 0; $i--)
			{
				if($dom)
					$dom = $bits[$i].".".$dom;
				else
					$dom = $bits[$i];
				$_SESSION['_config']['altrow'] = "";
				$dom = mysql_real_escape_string($dom);
				$query = "select * from domains where `memid`='".intval($_SESSION['profile']['id'])."' and `domain` like '$dom' and `deleted`=0 and `hash`=''";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					$altok = 1;
					$_SESSION['_config']['altrow'] = mysql_fetch_assoc($res);
					$altid[] = $_SESSION['_config']['altrow']['id'];
					break;
				}
			}

			if(!preg_match("/(?=^.{4,253}$)(^(?:\\*\\.)?((?!-)[a-zA-Z0-9_-]{1,63}(?<!-)\\.)+[a-zA-Z]{2,63}$)/i", $alt)) {
				$altok = 0;
			}

			if($altok == 0) {
				$_SESSION['_config']['rejected'][] = $alt;
				continue;
			}

			if($_SESSION['_config']['altrow'] != "")
				$altrows[] = $subalt;
		}
		$_SESSION['_config']['altrows'] = $altrows;
		$_SESSION['_config']['altid'] = $altid;
	}

	function getcn2()
	{
		$rows=array();
		$rowid=array();
		for($cnc = 0; $cnc < $_SESSION['_config']['cnc']; $cnc++)
		{
			$CN = $_SESSION['_config']["$cnc.CN"];
			$bits = explode(".", $CN);
			$dom = "";
			for($i = count($bits) - 1; $i >= 0; $i--)
			{
				if($dom)
					$dom = $bits[$i].".".$dom;
				else
					$dom = $bits[$i];
				$_SESSION['_config']['row'] = "";
				$dom = mysql_real_escape_string($dom);
				$query = "select *, `orginfo`.`id` as `id` from `orginfo`,`orgdomains`,`org` where
						`org`.`memid`='".intval($_SESSION['profile']['id'])."' and
						`org`.`orgid`=`orginfo`.`id` and
						`orgdomains`.`orgid`=`orginfo`.`id` and
						`orgdomains`.`domain`='$dom'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					$_SESSION['_config']['row'] = mysql_fetch_assoc($res);
					$rowid[] = $_SESSION['_config']['row']['id'];
					break;
				}
			}

			if(!preg_match("/(?=^.{4,253}$)(^(?:\\*\\.)?((?!-)[a-zA-Z0-9_-]{1,63}(?<!-)\\.)+[a-zA-Z]{2,63}$)/i", $CN)) {
				continue;
			}

			if($_SESSION['_config']['row'] != "")
				$rows[] = $CN;
		}
//		if(count($rows) <= 0)
//		{
//			echo _("There were no valid CommonName fields on the CSR, or I was unable to match any of these against your account. Please review your CSR, or add and verify domains contained in it to your account before trying again.");
//			exit;
//		}
		$_SESSION['_config']['rows'] = $rows;
		$_SESSION['_config']['rowid'] = $rowid;
	}

	function getalt2()
	{
		$altrows=array();
		$altid=array();
		for($altc = 0; $altc < $_SESSION['_config']['subaltc']; $altc++)
		{
			$subalt = $_SESSION['_config']["$altc.subjectAltName"];
			if(substr($subalt, 0, 4) == "DNS:")
				$alt = substr($subalt, 4);
			else
				continue;

			$bits = explode(".", $alt);
			$dom = "";
			for($i = count($bits) - 1; $i >= 0; $i--)
			{
				if($dom)
					$dom = $bits[$i].".".$dom;
				else
					$dom = $bits[$i];
				$_SESSION['_config']['altrow'] = "";
				$dom = mysql_real_escape_string($dom);
				$query = "select * from `orginfo`,`orgdomains`,`org` where
						`org`.`memid`='".intval($_SESSION['profile']['id'])."' and
						`org`.`orgid`=`orginfo`.`id` and
						`orgdomains`.`orgid`=`orginfo`.`id` and
						`orgdomains`.`domain`='$dom'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					$_SESSION['_config']['altrow'] = mysql_fetch_assoc($res);
					$altid[] = $_SESSION['_config']['altrow']['id'];
					break;
				}
			}

			if(!preg_match("/(?=^.{4,253}$)(^(?:\\*\\.)?((?!-)[a-zA-Z0-9_-]{1,63}(?<!-)\\.)+[a-zA-Z]{2,63}$)/i", $alt)) {
				continue;
			}

			if($_SESSION['_config']['altrow'] != "")
				$altrows[] = $subalt;
		}
		$_SESSION['_config']['altrows'] = $altrows;
		$_SESSION['_config']['altid'] = $altid;
	}

	function checkownership($hostname)
	{
		$bits = explode(".", $hostname);
		$dom = "";
		for($i = count($bits) - 1; $i >= 0; $i--)
		{
			if($dom)
				$dom = $bits[$i].".".$dom;
			else
				$dom = $bits[$i];
			$dom = mysql_real_escape_string($dom);
			$query = "select * from `org`,`orgdomains`,`orginfo`
					where `org`.`memid`='".intval($_SESSION['profile']['id'])."'
					and `orgdomains`.`orgid`=`org`.`orgid`
					and `orginfo`.`id`=`org`.`orgid`
					and `orgdomains`.`domain`='$dom'";
			$res = mysql_query($query);
			if(mysql_num_rows($res) > 0)
			{
				$_SESSION['_config']['row'] = mysql_fetch_assoc($res);
				return(true);
			}
		}
		return(false);
	}

	function maxpoints($id = 0)
	{
		if($id <= 0)
			$id = $_SESSION['profile']['id'];

		$query = "select sum(`points`) as `points` from `notary` where `to`='$id' and `deleted` = 0 group by `to`";
		$row = mysql_fetch_assoc(mysql_query($query));
		$points = $row['points'];

		$dob = date("Y-m-d", mktime(0,0,0,date("m"),date("d"),date("Y")-18));
		$query = "select * from `users` where `id`='".intval($_SESSION['profile']['id'])."' and `dob` < '$dob'";
		if(mysql_num_rows(mysql_query($query)) < 1)
		{
			if($points >= 100)
				return(10);
			else
				return(0);
		}

		if($points >= 150)
			return(35);
		if($points >= 140)
			return(30);
		if($points >= 130)
			return(25);
		if($points >= 120)
			return(20);
		if($points >= 110)
			return(15);
		if($points >= 100)
			return(10);
		return(0);
	}

	function gpg_hex2bin($data)
	{
		while(strstr($data, "\\x"))
		{
			$pos = strlen($data) - strlen(strstr($data, "\\x"));
			$before = substr($data, 0, $pos);
			$char = chr(hexdec(substr($data, $pos + 2, 2)));
			$after = substr($data, $pos + 4);
			$data = $before.$char.$after;
		}
		return(utf8_decode($data));
	}

	function signmail($to, $subject, $message, $from, $replyto = "")
	{
		if($replyto == "")
			$replyto = $from;
		$tmpfname = tempnam("/tmp", "CSR");
		$fp = fopen($tmpfname, "w");
		fputs($fp, $message);
		fclose($fp);
		$to_esc = escapeshellarg($to);
		$do = shell_exec("/usr/bin/gpg --homedir /home/gpg --clearsign \"$tmpfname\"|/usr/sbin/sendmail ".$to_esc);
		@unlink($tmpfname);
	}

	function checkEmail($email)
	{
		$myemail = mysql_real_escape_string($email);
		if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\+\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $email))
		{
			list($username,$domain)=explode('@',$email,2);
			$mxhostrr = array();
			$mxweight = array();
			if( !getmxrr($domain, $mxhostrr, $mxweight) ) {
				$mxhostrr = array($domain);
				$mxweight = array(0);
			} else if ( empty($mxhostrr) ) {
				$mxhostrr = array($domain);
				$mxweight = array(0);
			}

			$mxhostprio = array();
			for($i = 0; $i < count($mxhostrr); $i++) {
				$mx_host = trim($mxhostrr[$i], '.');
				$mx_prio = $mxweight[$i];
				if(empty($mxhostprio[$mx_prio])) {
					$mxhostprio[$mx_prio] = array();
				}
				$mxhostprio[$mx_prio][] = $mx_host;
			}

			array_walk($mxhostprio, function(&$mx) { shuffle($mx); } );
			ksort($mxhostprio);

			$mxhosts = array();
			foreach($mxhostprio as $mx_prio => $mxhostnames) {
				foreach($mxhostnames as $mx_host) {
					$mxhosts[] = $mx_host;
				}
			}

			foreach($mxhosts as $key => $domain)
			{
				$fp_opt = array(
					'ssl' => array(
						'verify_peer'   => false,	// Opportunistic Encryption
						'verify_peer_name'   => false,	// Opportunistic Encryption
						)
					);
				$fp_ctx = stream_context_create($fp_opt);
				$fp = @stream_socket_client("tcp://$domain:25",$errno,$errstr,5,STREAM_CLIENT_CONNECT,$fp_ctx);
				if($fp)
				{
					stream_set_blocking($fp, true);

					$has_starttls = false;

					do {
						$line = fgets($fp, 4096);
					} while(substr($line, 0, 4) == "220-");
					if(substr($line, 0, 3) != "220") {
						fclose($fp);
						continue;
					}

					fputs($fp, "EHLO www.cacert.org\r\n");
					do {
						$line = fgets($fp, 4096);
						$has_starttls |= substr(trim($line),4) == "STARTTLS";
					} while(substr($line, 0, 4) == "250-");
					if(substr($line, 0, 3) != "250") {
						fclose($fp);
						continue;
					}

					if($has_starttls) {
						fputs($fp, "STARTTLS\r\n");
						do {
							$line = fgets($fp, 4096);
						} while(substr($line, 0, 4) == "220-");
						if(substr($line, 0, 3) != "220") {
							fclose($fp);
							continue;
						}

						stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT|STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT|STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);

						fputs($fp, "EHLO www.cacert.org\r\n");
						do {
							$line = fgets($fp, 4096);
						} while(substr($line, 0, 4) == "250-");
						if(substr($line, 0, 3) != "250") {
							fclose($fp);
							continue;
						}
					}

					fputs($fp, "MAIL FROM:<returns@cacert.org>\r\n");
					do {
						$line = fgets($fp, 4096);
					} while(substr($line, 0, 4) == "250-");
					if(substr($line, 0, 3) != "250") {
						fclose($fp);
						continue;
					}

					fputs($fp, "RCPT TO:<$email>\r\n");
					do {
						$line = fgets($fp, 4096);
					} while(substr($line, 0, 4) == "250-");
					if(substr($line, 0, 3) != "250") {
						fclose($fp);
						continue;
					}

					fputs($fp, "QUIT\r\n");
					fclose($fp);

					$line = mysql_real_escape_string(trim(strip_tags($line)));
					$query = "insert into `pinglog` set `when`=NOW(), `email`='$myemail', `result`='$line'";
					if(is_array($_SESSION['profile'])) $query.=", `uid`='".intval($_SESSION['profile']['id'])."'";
					mysql_query($query);

					if(substr($line, 0, 3) != "250")
						return $line;
					else
						return "OK";
				}
			}
		}
		$query = "insert into `pinglog` set `when`=NOW(), `uid`='".intval($_SESSION['profile']['id'])."',
				`email`='$myemail', `result`='Failed to make a connection to the mail server'";
		mysql_query($query);
		return _("Failed to make a connection to the mail server");
	}

	function waitForResult($table, $certid, $id = 0, $show = 1)
	{
		$found = $trycount = 0;
		if($certid<=0)
		{
			if($show) showheader(_("My CAcert.org Account!"));
			echo _("ERROR: The new Certificate ID is wrong. Please contact support.\n");
			if($show) showfooter();
			if($show) exit;
			return;
		}
		while($trycount++ <= 40)
		{
			if($table == "gpg")
				$query = "select * from `$table` where `id`='".intval($certid)."' and `crt` != ''";
			else
				$query = "select * from `$table` where `id`='".intval($certid)."' and `crt_name` != ''";
			$res = mysql_query($query);
			if(mysql_num_rows($res) > 0)
			{
				$found = 1;
				break;
			}
			sleep(3);
		}

		if(!$found)
		{
			if($show) showheader(_("My CAcert.org Account!"));
			$query = "select * from `$table` where `id`='".intval($certid)."' ";
			$res = mysql_query($query);
			$body="";
			$subject="";
			if(mysql_num_rows($res) > 0)
			{
				printf(_("Your certificate request is still queued and hasn't been processed yet. Please wait, and go to Certificates -> View to see it's status."));
				$subject="[CAcert.org] Certificate TIMEOUT";
				$body = "A certificate has timed out!\n\n";
			}
			else
			{
				printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions.")." certid:$table:".intval($certid), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
				$subject="[CAcert.org] Certificate FAILURE";
				$body = "A certificate has failed: $table $certid $id $show\n\n";
			}

			$body .= _("Best regards")."\n"._("CAcert.org Support!");

			sendmail("philipp@cacert.org", $subject, $body, "returns@cacert.org", "", "", "CAcert Support");

			if($show) showfooter();
			if($show) exit;
		}
	}



	function generateTicket()
	{
		$query = "insert into tickets (timestamp) values (now()) ";
		mysql_query($query);
		$ticket = mysql_insert_id();
		return $ticket;
	}

	function sanitizeHTML($input)
	{
		return htmlentities(strip_tags($input), ENT_QUOTES, 'ISO-8859-1');
		//In case of problems, please use the following line again:
		//return htmlentities(strip_tags(utf8_decode($input)), ENT_QUOTES);
		//return htmlspecialchars(strip_tags($input));
	}

	function make_hash()
	{
		if(function_exists("dio_open"))
		{
			$rnd = dio_open("/dev/urandom",O_RDONLY);
			$hash = md5(dio_read($rnd,64));
			dio_close($rnd);
		} else {
			$rnd = fopen("/dev/urandom", "r");
			$hash = md5(fgets($rnd, 64));
			fclose($rnd);
		}
		return($hash);
	}

	function csrf_check($nam, $show=1)
        {
		if(!array_key_exists('csrf',$_REQUEST) || !array_key_exists('csrf_'.$nam,$_SESSION))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CSRF Hash is missing. Please try again.")."\n";
			showfooter();
			exit();
		}
		if(strlen($_REQUEST['csrf'])!=32)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CSRF Hash is wrong. Please try again.")."\n";
			showfooter();
			exit();
		}
		if(!array_key_exists($_REQUEST['csrf'],$_SESSION['csrf_'.$nam]))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CSRF Hash is wrong. Please try again.")."\n";
			showfooter();
			exit();
		}
        }
        function make_csrf($nam)
        {
                $hash=make_hash();
                $_SESSION['csrf_'.$nam][$hash]=1;
                return($hash);
        }

	function clean_csr($CSR)
	{
		$newcsr = str_replace("\r\n","\n",trim($CSR));
		$newcsr = str_replace("\n\n","\n",$newcsr);
		return(preg_replace("/[^A-Za-z0-9\n\r\-\:\=\+\/ ]/","",$newcsr));
	}
	function clean_gpgcsr($CSR)
	{
		return(preg_replace("/[^A-Za-z0-9\n\r\-\:\=\+\/ ]/","",trim($CSR)));
	}

	function sanitizeFilename($text)
	{
		$text=preg_replace("/[^\w-.@]/","",$text);
		return($text);
	}


	// returns text message to be shown to the user given the result of is_no_assurer
	function no_assurer_text($Status)
	{
		if ($Status == 0) {
			$Result = _("You have passed the Assurer Challenge and collected at least 100 Assurance Points, you are an Assurer.");
		} elseif ($Status == 3) {
			$Result = _("You have passed the Assurer Challenge, but to become an Assurer you still have to reach 100 Assurance Points!");
		} elseif ($Status == 5) {
			$Result = _("You have at least 100 Assurance Points, if you want to become an assurer try the").' <a href="https://cats.cacert.org/">'._("Assurer Challenge").'</a>!';
		} elseif ($Status == 7) {
			$Result = _("To become an Assurer you have to collect 100 Assurance Points and pass the").' <a href="https://cats.cacert.org/">'._("Assurer Challenge").'</a>!';
		} elseif ($Status & 8 > 0) {
			$Result = _("Sorry, you are not allowed to be an Assurer. Please contact").' <a href="mailto:cacert-support@lists.cacert.org">cacert-support@lists.cacert.org</a>'._(" if you feel that this is not corect.");
		} else {
			$Result = _("You are not an Assurer, but the reason is not stored in the database. Please contact").' <a href="mailto:cacert-support@lists.cacert.org">cacert-support@lists.cacert.org</a>.';
		}
		return $Result;
	}

	function is_assurer($userID)
	{
               if (get_assurer_status($userID))
                       return 0;
               else
                       return 1;
	}

	function get_assurer_reason($userID)
	{
               return no_assurer_text(get_assurer_status($userID));
	}

	function generatecertpath($type,$kind,$id)
	{
		$name="../$type/$kind-".intval($id).".$type";
		$newlayout=1;
		if($newlayout)
		{
			$name="../$type/$kind/".intval($id/1000)."/$kind-".intval($id).".$type";
			if (!is_dir("../csr")) { mkdir("../csr",0777); }
			if (!is_dir("../crt")) { mkdir("../crt",0777); }

			if (!is_dir("../csr/$kind")) { mkdir("../csr/$kind",0777); }
			if (!is_dir("../crt/$kind")) { mkdir("../crt/$kind",0777); }
			if (!is_dir("../csr/$kind/".intval($id/1000))) { mkdir("../csr/$kind/".intval($id/1000)); }
			if (!is_dir("../crt/$kind/".intval($id/1000))) { mkdir("../crt/$kind/".intval($id/1000)); }
		}
		return $name;
	}

	/**
	  * Run the sql query given in $sql.
	  * The resource returned by mysql_query is
	  * returned by this function.
	  *
	  * It should be safe to replace every mysql_query
	  * call by a mysql_extended_query call.
	  */
	function mysql_timed_query($sql)
	{
		global $sql_data_log;
		$query_start = microtime(true);
		$res = mysql_query($sql);
		$query_end = microtime(true);
		$sql_data_log[] = array("sql" => $sql, "duration" => $query_end - $query_start);
		return $res;
	}


?>
