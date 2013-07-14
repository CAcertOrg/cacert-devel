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
	require_once("../includes/loggedin.php");
	require_once("../includes/lib/l10n.php");
	require_once('lib/check_weak_key.php');
	require_once('notary.inc.php');

	loadem("account");

	$id = 0; if(array_key_exists("id",$_REQUEST)) $id=intval($_REQUEST['id']);
	$oldid = 0; if(array_key_exists("oldid",$_REQUEST)) $oldid=intval($_REQUEST['oldid']);
	$process = ""; if(array_key_exists("process",$_REQUEST)) $process=$_REQUEST['process'];

	$cert=0; if(array_key_exists('cert',$_REQUEST)) $cert=intval($_REQUEST['cert']);
	$orgid=0; if(array_key_exists('orgid',$_REQUEST)) $orgid=intval($_REQUEST['orgid']);
	$memid=0; if(array_key_exists('memid',$_REQUEST)) $memid=intval($_REQUEST['memid']);
	$domid=0; if(array_key_exists('domid',$_REQUEST)) $domid=intval($_REQUEST['domid']);


	if(!$_SESSION['mconn'])
	{
		echo _("Several CAcert Services are currently unavailable. Please try again later.");
		exit;
	}

	if ($process == _("Cancel"))
	{
		// General reset CANCEL process requests
		$process = "";
	}


	if($id == 45 || $id == 46 || $oldid == 45 || $oldid == 46)
	{
		$id = 1;
		$oldid=0;
	}

	if($process != "" && $oldid == 1)
	{
		$id = 1;
		csrf_check('addemail');
		if(strstr($_REQUEST['newemail'], "xn--") && $_SESSION['profile']['codesign'] <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("Due to the possibility for punycode domain exploits we currently do not allow any certificates to sign punycode domains or email addresses.");
			showfooter();
			exit;
		}
		if(trim(mysql_real_escape_string(stripslashes($_REQUEST['newemail']))) == "")
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("Not a valid email address. Can't continue."));
			showfooter();
			exit;
		}
		$oldid=0;
		$_REQUEST['email'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['newemail'])));
		$query = "select * from `email` where `email`='".$_REQUEST['email']."' and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("The email address '%s' is already in a different account. Can't continue."), sanitizeHTML($_REQUEST['email']));
			showfooter();
			exit;
		}
		$checkemail = checkEmail($_REQUEST['newemail']);
		if($checkemail != "OK")
		{
			showheader(_("My CAcert.org Account!"));
			if (substr($checkemail, 0, 1) == "4")
			{
				echo "<p>"._("The mail server responsible for your domain indicated a temporary failure. This may be due to anti-SPAM measures, such as greylisting. Please try again in a few minutes.")."</p>\n";
			} else {
				echo "<p>"._("Email Address given was invalid, or a test connection couldn't be made to your server, or the server rejected the email address as invalid")."</p>\n";
			}
			echo "<p>$checkemail</p>\n";
			showfooter();
			exit;
		}
		$hash = make_hash();
		$query = "insert into `email` set `email`='".$_REQUEST['email']."',`memid`='".$_SESSION['profile']['id']."',`created`=NOW(),`hash`='$hash'";
		mysql_query($query);
		$emailid = mysql_insert_id();

		$body = _("Below is the link you need to open to verify your email address. Once your address is verified you will be able to start issuing certificates to your heart's content!")."\n\n";
		$body .= "http://".$_SESSION['_config']['normalhostname']."/verify.php?type=email&emailid=$emailid&hash=$hash\n\n";
		$body .= _("Best regards")."\n"._("CAcert.org Support!");

		sendmail($_REQUEST['email'], "[CAcert.org] "._("Email Probe"), $body, "support@cacert.org", "", "", "CAcert Support");

		showheader(_("My CAcert.org Account!"));
		printf(_("The email address '%s' has been added to the system, however before any certificates for this can be issued you need to open the link in a browser that has been sent to your email address."), sanitizeHTML($_REQUEST['email']));
		showfooter();
		exit;
	}

	if(array_key_exists("makedefault",$_REQUEST) && $_REQUEST['makedefault'] != "" && $oldid == 2)
	{
		$id = 2;
		$emailid = intval($_REQUEST['emailid']);
		$query = "select * from `email` where `id`='$emailid' and `memid`='".$_SESSION['profile']['id']."' and `hash` = '' and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("Error!"));
			echo _("You currently don't have access to the email address you selected, or you haven't verified it yet.");
			showfooter();
			exit;
		}
		$row = mysql_fetch_assoc($res);
		$body  = sprintf(_("Hi %s,"),$_SESSION['profile']['fname'])."\n\n";
		$body .= _("You are receiving this email because you or someone else ".
				"has changed the default email on your account.")."\n\n";

		$body .= _("Best regards")."\n"._("CAcert.org Support!");

		sendmail($_SESSION['profile']['email'], "[CAcert.org] "._("Default Account Changed"), $body,
				"support@cacert.org", "", "", "CAcert Support");

		$_SESSION['profile']['email'] = $row['email'];
		$query = "update `users` set `email`='".$row['email']."' where `id`='".$_SESSION['profile']['id']."'";
		mysql_query($query);
		showheader(_("My CAcert.org Account!"));
		printf(_("Your default email address has been updated to '%s'."), sanitizeHTML($row['email']));
		showfooter();
		exit;
	}

	if($process != "" && $oldid == 2)
	{
		$id = 2;
		csrf_check("chgdef");
		showheader(_("My CAcert.org Account!"));
		$delcount = 0;
		if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
		{
			foreach($_REQUEST['delid'] as $id)
			{
				if (0==$delcount) {
					echo _('The following email addresses have been removed:')."<br>\n";
				}
				$id = intval($id);
				$query = "select * from `email` where `id`='$id' and `memid`='".intval($_SESSION['profile']['id'])."' and
						`email`!='".$_SESSION['profile']['email']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					$row = mysql_fetch_assoc($res);
					echo $row['email']."<br>\n";
					$query = "select `emailcerts`.`id`
							from `emaillink`,`emailcerts` where
							`emailid`='$id' and `emaillink`.`emailcertsid`=`emailcerts`.`id` and
							`revoked`=0 and UNIX_TIMESTAMP(`expire`)-UNIX_TIMESTAMP() > 0
							group by `emailcerts`.`id`";
					$dres = mysql_query($query);
					while($drow = mysql_fetch_assoc($dres))
						mysql_query("update `emailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='".$drow['id']."'");

					$query = "update `email` set `deleted`=NOW() where `id`='$id'";
					mysql_query($query);
					$delcount++;
				}
			}
		}
		else
		{
			echo _("You did not select any email accounts for removal.");
		}
		if(0 == $delcount)
		{
			echo _("You failed to select any accounts to be removed, or you attempted to remove the default account. No action was taken.");
		}

		showfooter();
		exit;
	}

	if($process != "" && $oldid == 3)
	{
		if(!array_key_exists('CCA',$_REQUEST))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("You did not accept the CAcert Community Agreement (CCA), hit the back button and try again.");
			showfooter();
			exit;
		}

		if(!(array_key_exists('addid',$_REQUEST) && is_array($_REQUEST['addid'])) && $_REQUEST['SSO'] != '1')
		{
			showheader(_("My CAcert.org Account!"));
			echo _("I didn't receive a valid Certificate Request, hit the back button and try again.");
			showfooter();
			exit;
		}

		$_SESSION['_config']['SSO'] = intval($_REQUEST['SSO']);

		$_SESSION['_config']['addid'] = $_REQUEST['addid'];
		if($_SESSION['profile']['points'] >= 50)
			$_SESSION['_config']['incname'] = intval($_REQUEST['incname']);
		if(array_key_exists('codesign',$_REQUEST) && $_REQUEST['codesign'] != 0 && ($_SESSION['profile']['codesign'] == 0 || $_SESSION['profile']['points'] < 100))
		{
			$_REQUEST['codesign'] = 0;
		}
		if($_SESSION['profile']['points'] >= 100 && $_SESSION['profile']['codesign'] > 0 && array_key_exists('codesign',$_REQUEST) && $_REQUEST['codesign'] == 1)
		{
			if($_SESSION['_config']['incname'] < 1 || $_SESSION['_config']['incname'] > 4)
				$_SESSION['_config']['incname'] = 1;
		}
		if(array_key_exists('codesign',$_REQUEST) && $_REQUEST['codesign'] == 1 && $_SESSION['profile']['points'] >= 100)
			$_SESSION['_config']['codesign'] = 1;
		else
			$_SESSION['_config']['codesign'] = 0;

		if(array_key_exists('login',$_REQUEST) && $_REQUEST['login'] == 1)
			$_SESSION['_config']['disablelogin'] = 0;
		else
			$_SESSION['_config']['disablelogin'] = 1;

		$_SESSION['_config']['rootcert'] = 1;
		if($_SESSION['profile']['points'] >= 50)
		{
			$_SESSION['_config']['rootcert'] = intval($_REQUEST['rootcert']);
			if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
				$_SESSION['_config']['rootcert'] = 1;
		}
		$csr = "";
		if(trim($_REQUEST['optionalCSR']) == "")
		{
			$id = 4;
		} else {
			$oldid = 4;
			$_REQUEST['keytype'] = "MS";
			$csr = clean_csr($_REQUEST['optionalCSR']);
		}
	}

	if($oldid == 4)
	{
		if($_REQUEST['keytype'] == "NS")
		{
			$spkac=""; if(array_key_exists('SPKAC',$_REQUEST) && preg_match("/^[a-zA-Z0-9+=\/]+$/", trim(str_replace("\n", "", str_replace("\r", "",$_REQUEST['SPKAC']))))) $spkac=trim(str_replace("\n", "", str_replace("\r", "",$_REQUEST['SPKAC'])));

			if($spkac=="" || $spkac == "deadbeef")
			{
				$id = 4;
				showheader(_("My CAcert.org Account!"));
				echo _("I didn't receive a valid Certificate Request, please try a different browser.");
				showfooter();
				exit;
			}
			$count = 0;
			$emails = "";
			$addys = array();
			$defaultemail="";
			if(is_array($_SESSION['_config']['addid']))
			foreach($_SESSION['_config']['addid'] as $id)
			{
				$res = mysql_query("select * from `email` where `memid`='".$_SESSION['profile']['id']."' and `id`='".intval($id)."'");
				if(mysql_num_rows($res) > 0)
				{
					$row = mysql_fetch_assoc($res);
					if(!$emails)
						$defaultemail = $row['email'];
					$emails .= "$count.emailAddress = ".$row['email']."\n";
					$count++;
					$addys[] = intval($row['id']);
				}
			}
			if($count <= 0 && $_SESSION['_config']['SSO'] != 1)
			{
				$id = 4;
				showheader(_("My CAcert.org Account!"));
				echo _("You submitted invalid email addresses, or email address you no longer have control of. Can't continue with certificate request.");
				showfooter();
				exit;
			}
			$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".$_SESSION['profile']['id']."'"));
			if($_SESSION['_config']['SSO'] == 1)
				$emails .= "$count.emailAddress = ".$user['uniqueID']."\n";

			if(strlen($user['mname']) == 1)
				$user['mname'] .= '.';
			if(!array_key_exists('incname',$_SESSION['_config']) || $_SESSION['_config']['incname'] <= 0 || $_SESSION['_config']['incname'] > 4)
			{
				$emails .= "commonName = CAcert WoT User\n";
			}
			else
			{
				if($_SESSION['_config']['incname'] == 1)
					$emails .= "commonName = ".$user['fname']." ".$user['lname']."\n";
				if($_SESSION['_config']['incname'] == 2)
					$emails .= "commonName = ".$user['fname']." ".$user['mname']." ".$user['lname']."\n";
				if($_SESSION['_config']['incname'] == 3)
					$emails .= "commonName = ".$user['fname']." ".$user['lname']." ".$user['suffix']."\n";
				if($_SESSION['_config']['incname'] == 4)
					$emails .= "commonName = ".$user['fname']." ".$user['mname']." ".$user['lname']." ".$user['suffix']."\n";
			}
			if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
				$_SESSION['_config']['rootcert'] = 1;

			$emails .= "SPKAC = $spkac";
			if (($weakKey = checkWeakKeySPKAC($emails)) !== "")
			{
				$id = 4;
				showheader(_("My CAcert.org Account!"));
				echo $weakKey;
				showfooter();
				exit;
			}

			write_user_agreement(intval($_SESSION['profile']['id']), "CCA", "certificate creation", "", 1);

			$query = "insert into emailcerts set
						`CN`='$defaultemail',
						`keytype`='NS',
						`memid`='".intval($_SESSION['profile']['id'])."',
						`created`=FROM_UNIXTIME(UNIX_TIMESTAMP()),
						`codesign`='".intval($_SESSION['_config']['codesign'])."',
						`disablelogin`='".($_SESSION['_config']['disablelogin']?1:0)."',
						`rootcert`='".intval($_SESSION['_config']['rootcert'])."'";
			mysql_query($query);
			$emailid = mysql_insert_id();
			if(is_array($addys))
			foreach($addys as $addy)
				mysql_query("insert into `emaillink` set `emailcertsid`='$emailid', `emailid`='$addy'");
			$CSRname=generatecertpath("csr","client",$emailid);
			$fp = fopen($CSRname, "w");
			fputs($fp, $emails);
			fclose($fp);
			$challenge=$_SESSION['spkac_hash'];
                        $res=`openssl spkac -verify -in $CSRname`;
                        if(!strstr($res,"Challenge String: ".$challenge))
                        {
                                $id = $oldid;
                                showheader(_("My CAcert.org Account!"));
                                echo _("The challenge-response code of your certificate request did not match. Can't continue with certificaterequest.");
                                showfooter();
                                exit;
                        }
			mysql_query("update `emailcerts` set `csr_name`='$CSRname' where `id`='".intval($emailid)."'");
		} else if($_REQUEST['keytype'] == "MS" || $_REQUEST['keytype'] == "VI") {
			if($csr == "")
				$csr = "-----BEGIN CERTIFICATE REQUEST-----\n".clean_csr($_REQUEST['CSR'])."\n-----END CERTIFICATE REQUEST-----\n";

			if (($weakKey = checkWeakKeyCSR($csr)) !== "")
			{
				$id = 4;
				showheader(_("My CAcert.org Account!"));
				echo $weakKey;
				showfooter();
				exit;
			}

			$tmpfname = tempnam("/tmp", "id4CSR");
			$fp = fopen($tmpfname, "w");
			fputs($fp, $csr);
			fclose($fp);

			$addys = array();
			$defaultemail = "";
			$csrsubject="";

			$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($_SESSION['profile']['id'])."'"));
                        if(strlen($user['mname']) == 1)
                                $user['mname'] .= '.';
			if($_SESSION['_config']['incname'] <= 0 || $_SESSION['_config']['incname'] > 4)
				$csrsubject = "/CN=CAcert WoT User";
			if($_SESSION['_config']['incname'] == 1)
				$csrsubject = "/CN=".$user['fname']." ".$user['lname'];
			if($_SESSION['_config']['incname'] == 2)
				$csrsubject = "/CN=".$user['fname']." ".$user['mname']." ".$user['lname'];
			if($_SESSION['_config']['incname'] == 3)
				$csrsubject = "/CN=".$user['fname']." ".$user['lname']." ".$user['suffix'];
			if($_SESSION['_config']['incname'] == 4)
				$csrsubject = "/CN=".$user['fname']." ".$user['mname']." ".$user['lname']." ".$user['suffix'];
			if(is_array($_SESSION['_config']['addid']))
			foreach($_SESSION['_config']['addid'] as $id)
			{
				$res = mysql_query("select * from `email` where `memid`='".intval($_SESSION['profile']['id'])."' and `id`='".intval($id)."'");
				if(mysql_num_rows($res) > 0)
				{
					$row = mysql_fetch_assoc($res);
					if($defaultemail == "")
						$defaultemail = $row['email'];
					$csrsubject .= "/emailAddress=".$row['email'];
					$addys[] = $row['id'];
				}
			}
			if($_SESSION['_config']['SSO'] == 1)
				$csrsubject .= "/emailAddress = ".$user['uniqueID'];

			$tmpname = tempnam("/tmp", "id4csr");
			$do = `/usr/bin/openssl req -in $tmpfname -out $tmpname`; // -subj "$csr"`;
			@unlink($tmpfname);
			$csr = "";
			$fp = fopen($tmpname, "r");
			while($data = fgets($fp, 4096))
				$csr .= $data;
			fclose($fp);
			@unlink($tmpname);
			if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
				$_SESSION['_config']['rootcert'] = 1;

			if($csr == "")
			{
				$id = 4;
				showheader(_("My CAcert.org Account!"));
				echo _("I didn't receive a valid Certificate Request, hit the back button and try again.");
				showfooter();
				exit;
			}
			$query = "insert into emailcerts set
						`CN`='$defaultemail',
						`keytype`='".sanitizeHTML($_REQUEST['keytype'])."',
						`memid`='".$_SESSION['profile']['id']."',
						`created`=FROM_UNIXTIME(UNIX_TIMESTAMP()),
						`subject`='".mysql_real_escape_string($csrsubject)."',
						`codesign`='".$_SESSION['_config']['codesign']."',
						`disablelogin`='".($_SESSION['_config']['disablelogin']?1:0)."',
						`rootcert`='".$_SESSION['_config']['rootcert']."'";
			mysql_query($query);
			$emailid = mysql_insert_id();
			if(is_array($addys))
			foreach($addys as $addy)
				mysql_query("insert into `emaillink` set `emailcertsid`='$emailid', `emailid`='".mysql_real_escape_string($addy)."'");
			$CSRname=generatecertpath("csr","client",$emailid);
			$fp = fopen($CSRname, "w");
			fputs($fp, $csr);
			fclose($fp);
			mysql_query("update `emailcerts` set `csr_name`='$CSRname' where `id`='$emailid'");
		}
		waitForResult("emailcerts", $emailid, 4);
		$query = "select * from `emailcerts` where `id`='$emailid' and `crt_name` != ''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$id = 4;
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		} else {
			$id = 6;
			$cert = $emailid;
			$_REQUEST['cert']=$emailid;
		}
	}

	if($oldid == 7)
	{
		csrf_check("adddomain");
		if(strstr($_REQUEST['newdomain'],"\x00"))
		{
                        showheader(_("My CAcert.org Account!"));
                        echo _("Due to the possibility for nullbyte domain exploits we currently do not allow any domain names with nullbytes.");
                        showfooter();
                        exit;
		}

		list($newdomain) = explode(" ", $_REQUEST['newdomain'], 2); // Ignore the rest
		while($newdomain['0'] == '-')
			$newdomain = substr($newdomain, 1);
		if(strstr($newdomain, "xn--") && $_SESSION['profile']['codesign'] <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("Due to the possibility for punycode domain exploits we currently do not allow any certificates to sign punycode domains or email addresses.");
			showfooter();
			exit;
		}

		$newdom = trim(escapeshellarg($newdomain));
		$newdomain = mysql_real_escape_string(trim($newdomain));

		$res1 = mysql_query("select * from `orgdomains` where `domain`='$newdomain'");
		$query = "select * from `domains` where `domain`='$newdomain' and `deleted`=0";
		$res2 = mysql_query($query);
		if(mysql_num_rows($res1) > 0 || mysql_num_rows($res2))
		{
			$oldid=0;
			$id = 7;
			showheader(_("My CAcert.org Account!"));
			printf(_("The domain '%s' is already in a different account and is listed as valid. Can't continue."), sanitizeHTML($newdomain));
			showfooter();
			exit;
		}
	}

	if($oldid == 7)
	{
		$oldid=0;
		$id = 8;
		$addy = array();
		$adds = array();
		if(strtolower(substr($newdom, -4, 3)) != ".jp")
			$adds = explode("\n", trim(`/usr/bin/whois $newdom|grep "@"`));
		if(substr($newdomain, -4) == ".org" || substr($newdomain, -5) == ".info")
		{
			if(is_array($adds))
			foreach($adds as $line)
			{
				$bits = explode(":", $line, 2);
				$line = trim($bits[1]);
				if(!in_array($line, $addy) && $line != "")
					$addy[] = trim(mysql_real_escape_string(stripslashes($line)));
			}
		} else {
			if(is_array($adds))
			foreach($adds as $line)
			{
				$line = trim(str_replace("\t", " ", $line));
				$line = trim(str_replace("(", "", $line));
				$line = trim(str_replace(")", " ", $line));
				$line = trim(str_replace(":", " ", $line));

				$bits = explode(" ", $line);
				foreach($bits as $bit)
				{
					if(strstr($bit, "@"))
						$line = $bit;
				}
				if(!in_array($line, $addy) && $line != "")
					$addy[] = trim(mysql_real_escape_string(stripslashes($line)));
			}
		}

		$rfc = array("root@$newdomain", "hostmaster@$newdomain", "postmaster@$newdomain", "admin@$newdomain", "webmaster@$newdomain");
		foreach($rfc as $sub)
			if(!in_array($sub, $addy))
				$addy[] = $sub;
		$_SESSION['_config']['addy'] = $addy;
		$_SESSION['_config']['domain'] = mysql_real_escape_string($newdomain);
	}

	if($process != "" && $oldid == 8)
	{
		csrf_check('ctcinfo');
		$oldid=0;
		$id = 8;

		$authaddy = trim(mysql_real_escape_string(stripslashes($_REQUEST['authaddy'])));

		if($authaddy == "" || !is_array($_SESSION['_config']['addy']))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("The address you submitted isn't a valid authority address for the domain.");
			showfooter();
			exit;
		}

		if(!in_array($authaddy, $_SESSION['_config']['addy']))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("The address you submitted isn't a valid authority address for the domain.");
			showfooter();
			exit;
		}

		$query = "select * from `domains` where `domain`='".mysql_real_escape_string($_SESSION['_config']['domain'])."' and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("The domain '%s' is already in a different account and is listed as valid. Can't continue."), sanitizeHTML($_SESSION['_config']['domain']));
			showfooter();
			exit;
		}
		$checkemail = checkEmail($authaddy);
		if($checkemail != "OK")
		{
			showheader(_("My CAcert.org Account!"));
			//echo "<p>"._("Email Address given was invalid, or a test connection couldn't be made to your server, or the server rejected the email address as invalid")."</p>\n";
			if (substr($checkemail, 0, 1) == "4")
			{
				echo "<p>"._("The mail server responsible for your domain indicated a temporary failure. This may be due to anti-SPAM measures, such as greylisting. Please try again in a few minutes.")."</p>\n";
			} else {
				echo "<p>"._("Email Address given was invalid, or a test connection couldn't be made to your server, or the server rejected the email address as invalid")."</p>\n";
			}
			echo "<p>$checkemail</p>\n";
			showfooter();
			exit;
		}

		$hash = make_hash();
		$query = "insert into `domains` set `domain`='".mysql_real_escape_string($_SESSION['_config']['domain'])."',
					`memid`='".$_SESSION['profile']['id']."',`created`=NOW(),`hash`='$hash'";
		mysql_query($query);
		$domainid = mysql_insert_id();

		$body = sprintf(_("Below is the link you need to open to verify your domain '%s'. Once your address is verified you will be able to start issuing certificates to your heart's content!"),$_SESSION['_config']['domain'])."\n\n";
		$body .= "http://".$_SESSION['_config']['normalhostname']."/verify.php?type=domain&domainid=$domainid&hash=$hash\n\n";
		$body .= _("Best regards")."\n"._("CAcert.org Support!");

		sendmail($authaddy, "[CAcert.org] "._("Email Probe"), $body, "support@cacert.org", "", "", "CAcert Support");

		showheader(_("My CAcert.org Account!"));
		printf(_("The domain '%s' has been added to the system, however before any certificates for this can be issued you need to open the link in a browser that has been sent to your email address."), $_SESSION['_config']['domain']);
		showfooter();
		exit;
	}

	if($process != "" && $oldid == 9)
	{
		$id = 9;
		showheader(_("My CAcert.org Account!"));
		if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
		{
			echo _("The following domains have been removed:")."<br>
				("._("Any valid certificates will be revoked as well").")<br>\n";

			foreach($_REQUEST['delid'] as $id)
			{
				$id = intval($id);
				$query = "select * from `domains` where `id`='$id' and `memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					$row = mysql_fetch_assoc($res);
					echo $row['domain']."<br>\n";

					$dres = mysql_query(
						"select distinct `domaincerts`.`id`
							from `domaincerts`, `domlink`
							where `domaincerts`.`domid` = '$id'
							or (
								`domaincerts`.`id` = `domlink`.`certid`
								and `domlink`.`domid` = '$id'
								)");
					while($drow = mysql_fetch_assoc($dres))
					{
						mysql_query(
							"update `domaincerts`
								set `revoked`='1970-01-01 10:00:01'
								where `id` = '".$drow['id']."'
								and `revoked` = 0
								and UNIX_TIMESTAMP(`expire`) -
										UNIX_TIMESTAMP() > 0");
					}

					mysql_query(
						"update `domains`
							set `deleted`=NOW()
							where `id` = '$id'");
				}
			}
		}
		else
		{
			echo _("You did not select any domains for removal.");
		}

		showfooter();
		exit;
	}

	if($process != "" && $oldid == 10)
	{
		if(!array_key_exists('CCA',$_REQUEST))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("You did not accept the CAcert Community Agreement (CCA), hit the back button and try again.");
			showfooter();
			exit;
		}

		$CSR = clean_csr($_REQUEST['CSR']);
		if(strpos($CSR,"---BEGIN")===FALSE)
		{
		  // In case the CSR is missing the ---BEGIN lines, add them automatically:
		  $CSR = "-----BEGIN CERTIFICATE REQUEST-----\n".$CSR."\n-----END CERTIFICATE REQUEST-----\n";
		}

		if (($weakKey = checkWeakKeyCSR($CSR)) !== "")
		{
			showheader(_("My CAcert.org Account!"));
			echo $weakKey;
			showfooter();
			exit;
		}

		$_SESSION['_config']['tmpfname'] = tempnam("/tmp", "id10CSR");
		$fp = fopen($_SESSION['_config']['tmpfname'], "w");
		fputs($fp, $CSR);
		fclose($fp);
		$CSR = $_SESSION['_config']['tmpfname'];
		$_SESSION['_config']['subject'] = trim(`/usr/bin/openssl req -text -noout -in "$CSR"|tr -d "\\0"|grep "Subject:"`);
		$bits = explode(",", trim(`/usr/bin/openssl req -text -noout -in "$CSR"|tr -d "\\0"|grep -A1 'X509v3 Subject Alternative Name:'|grep DNS:`));
		foreach($bits as $val)
		{
			$_SESSION['_config']['subject'] .= "/subjectAltName=".trim($val);
		}
		$id = 11;

		$_SESSION['_config']['0.CN'] = $_SESSION['_config']['0.subjectAltName'] = "";
		extractit();
		getcn();
		getalt();

		if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
			showfooter();
			exit;
		}

		$_SESSION['_config']['rootcert'] = 1;
		if($_SESSION['profile']['points'] >= 50)
		{
			$_SESSION['_config']['rootcert'] = intval($_REQUEST['rootcert']);
			if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
				$_SESSION['_config']['rootcert'] = 1;
		}
	}

	if($process != "" && $oldid == 11)
	{
		if(!file_exists($_SESSION['_config']['tmpfname']))
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		}

		if (($weakKey = checkWeakKeyCSR(file_get_contents(
				$_SESSION['_config']['tmpfname']))) !== "")
		{
			showheader(_("My CAcert.org Account!"));
			echo $weakKey;
			showfooter();
			exit;
		}

		$id = 11;
		if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
			showfooter();
			exit;
		}

		$subject = "";
		$count = 0;
		$supressSAN=0;
                if($_SESSION["profile"]["id"] == 104074) $supressSAN=1;

		if(is_array($_SESSION['_config']['rows']))
			foreach($_SESSION['_config']['rows'] as $row)
			{
				$count++;
				if($count <= 1)
				{
					$subject .= "/CN=$row";
					if(!$supressSAN) $subject .= "/subjectAltName=DNS:$row";
					if(!$supressSAN) $subject .= "/subjectAltName=otherName:1.3.6.1.5.5.7.8.5;UTF8:$row";
				} else {
					if(!$supressSAN) $subject .= "/subjectAltName=DNS:$row";
					if(!$supressSAN) $subject .= "/subjectAltName=otherName:1.3.6.1.5.5.7.8.5;UTF8:$row";
				}
			}
		if(is_array($_SESSION['_config']['altrows']))
			foreach($_SESSION['_config']['altrows'] as $row)
			{
				if(substr($row, 0, 4) == "DNS:")
				{
					$row = substr($row, 4);
					if(!$supressSAN) $subject .= "/subjectAltName=DNS:$row";
					if(!$supressSAN) $subject .= "/subjectAltName=otherName:1.3.6.1.5.5.7.8.5;UTF8:$row";
				}
			}
		if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
			$_SESSION['_config']['rootcert'] = 1;

		write_user_agreement(intval($_SESSION['profile']['id']), "CCA", "certificate creation", "", 1);

		if(array_key_exists('0',$_SESSION['_config']['rowid']) && $_SESSION['_config']['rowid']['0'] > 0)
		{
			$query = "insert into `domaincerts` set
						`CN`='".mysql_real_escape_string($_SESSION['_config']['rows']['0'])."',
						`domid`='".mysql_real_escape_string($_SESSION['_config']['rowid']['0'])."',
						`created`=NOW(),`subject`='".mysql_real_escape_string($subject)."',
						`rootcert`='".mysql_real_escape_string($_SESSION['_config']['rootcert'])."'";
		} elseif(array_key_exists('0',$_SESSION['_config']['altid']) && $_SESSION['_config']['altid']['0'] > 0) {
			$query = "insert into `domaincerts` set
						`CN`='".mysql_real_escape_string($_SESSION['_config']['altrows']['0'])."',
						`domid`='".mysql_real_escape_string($_SESSION['_config']['altid']['0'])."',
						`created`=NOW(),`subject`='".mysql_real_escape_string($subject)."',
						`rootcert`='".mysql_real_escape_string($_SESSION['_config']['rootcert'])."'";
		} else {
			showheader(_("My CAcert.org Account!"));
			echo _("Domain not verified.");
			showfooter();
			exit;

		}

		mysql_query($query);
		$CSRid = mysql_insert_id();

		if(is_array($_SESSION['_config']['rowid']))
			foreach($_SESSION['_config']['rowid'] as $dom)
				mysql_query("insert into `domlink` set `certid`='$CSRid', `domid`='$dom'");
		if(is_array($_SESSION['_config']['altid']))
		foreach($_SESSION['_config']['altid'] as $dom)
			mysql_query("insert into `domlink` set `certid`='$CSRid', `domid`='$dom'");

		$CSRname=generatecertpath("csr","server",$CSRid);
		rename($_SESSION['_config']['tmpfname'], $CSRname);
		chmod($CSRname,0644);
		mysql_query("update `domaincerts` set `CSR_name`='$CSRname' where `id`='$CSRid'");
		waitForResult("domaincerts", $CSRid, 11);
		$query = "select * from `domaincerts` where `id`='$CSRid' and `crt_name` != ''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$id = 11;
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		} else {
			$id = 15;
			$cert = $CSRid;
			$_REQUEST['cert']=$CSRid;
		}
	}

	if($oldid == 12 && array_key_exists('renew',$_REQUEST) && $_REQUEST['renew'] != "")
	{
		csrf_check('srvcerchange');
		$id = 12;
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			echo _("Now renewing the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				echo _("Processing request")." $id:<br/>";
				$query = "select *,UNIX_TIMESTAMP(`domaincerts`.`revoked`) as `revoke` from `domaincerts`,`domains`
						where `domaincerts`.`id`='$id' and
						`domaincerts`.`domid`=`domains`.`id` and
						`domains`.`memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br/>\n", $id);
					continue;
				}

				$row = mysql_fetch_assoc($res);

				if (($weakKey = checkWeakKeyX509(file_get_contents(
						$row['crt_name']))) !== "")
				{
					echo $weakKey, "<br/>\n";
					continue;
				}

				mysql_query("update `domaincerts` set `renewed`='1' where `id`='$id'");
				$query = "insert into `domaincerts` set
						`domid`='".$row['domid']."',
						`CN`='".mysql_real_escape_string($row['CN'])."',
						`subject`='".mysql_real_escape_string($row['subject'])."',".
						//`csr_name`='".$row['csr_name']."', // RACE CONDITION
						"`created`='".$row['created']."',
						`modified`=NOW(),
						`rootcert`='".$row['rootcert']."',
						`type`='".$row['type']."',
						`pkhash`='".$row['pkhash']."'";
				mysql_query($query);
				$newid = mysql_insert_id();
				$newfile=generatecertpath("csr","server",$newid);
				copy($row['csr_name'], $newfile);
				$_SESSION['_config']['subject'] = trim(`/usr/bin/openssl req -text -noout -in "$newfile"|tr -d "\\0"|grep "Subject:"`);
				$bits = explode(",", trim(`/usr/bin/openssl req -text -noout -in "$newfile"|tr -d "\\0"|grep -A1 'X509v3 Subject Alternative Name:'|grep DNS:`));
				foreach($bits as $val)
				{
					$_SESSION['_config']['subject'] .= "/subjectAltName=".trim($val);
				}
				$_SESSION['_config']['0.CN'] = $_SESSION['_config']['0.subjectAltName'] = "";
				extractit();
				getcn();
				getalt();

				if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
				{
					echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
					continue;
				}

				$subject = "";
				$count = 0;
				if(is_array($_SESSION['_config']['rows']))
					foreach($_SESSION['_config']['rows'] as $row)
					{
						$count++;
						if($count <= 1)
						{
							$subject .= "/CN=$row";
							if(!strstr($subject, "=$row/") &&
								substr($subject, -strlen("=$row")) != "=$row")
								$subject .= "/subjectAltName=$row";
						} else {
							if(!strstr($subject, "=$row/") &&
								substr($subject, -strlen("=$row")) != "=$row")
								$subject .= "/subjectAltName=$row";
						}
					}
				if(is_array($_SESSION['_config']['altrows']))
					foreach($_SESSION['_config']['altrows'] as $row)
						if(!strstr($subject, "=$row/") &&
							substr($subject, -strlen("=$row")) != "=$row")
							$subject .= "/subjectAltName=$row";
				$subject = mysql_real_escape_string($subject);
				mysql_query("update `domaincerts` set `subject`='$subject',`csr_name`='$newfile' where `id`='$newid'");

				echo _("Renewing").": ".sanitizeHTML($_SESSION['_config']['0.CN'])."<br>\n";
				waitForResult("domaincerts", $newid,$oldid,0);
				$query = "select * from `domaincerts` where `id`='$newid' and `crt_name` != ''";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
				} else {
					$drow = mysql_fetch_assoc($res);
					$cert = `/usr/bin/openssl x509 -in $drow[crt_name]`;
					echo "<pre>\n$cert\n</pre>\n";
				}
			}
		}
		else
		{
			echo _("You did not select any certificates for renewal.");
		}
		showfooter();
		exit;
	}

	if($oldid == 12 && array_key_exists('revoke',$_REQUEST) && $_REQUEST['revoke'] != "")
	{
		csrf_check('srvcerchange');
		$id = 12;
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			echo _("Now revoking the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`domaincerts`.`revoked`) as `revoke` from `domaincerts`,`domains`
						where `domaincerts`.`id`='$id' and
						`domaincerts`.`domid`=`domains`.`id` and
						`domains`.`memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['revoke'] > 0)
				{
					printf(_("It would seem '%s' has already been revoked. I'll skip this for now.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("update `domaincerts` set `revoked`='1970-01-01 10:00:01' where `id`='$id'");
				printf(_("Certificate for '%s' has been revoked.")."<br>\n", $row['CN']);
			}
		}
		else
		{
			echo _("You did not select any certificates for revocation.");
		}

		if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
		{
			echo _("Now deleting the following pending requests:")."<br>\n";
			foreach($_REQUEST['delid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`domaincerts`.`expire`) as `expired` from `domaincerts`,`domains`
						where `domaincerts`.`id`='$id' and
						`domaincerts`.`domid`=`domains`.`id` and
						`domains`.`memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['expired'] > 0)
				{
					printf(_("Couldn't remove the request for `%s`, request had already been processed.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("delete from `domaincerts` where `id`='$id'");
				@unlink($row['csr_name']);
				@unlink($row['crt_name']);
				printf(_("Removed a pending request for '%s'")."<br>\n", $row['CN']);
			}
		}
		showfooter();
		exit;
	}

	if($oldid == 5 && array_key_exists('renew',$_REQUEST) && $_REQUEST['renew'] != "")
	{
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			echo _("Now renewing the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`revoked`) as `revoke` from `emailcerts`
						where `id`='$id' and `memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}

				$row = mysql_fetch_assoc($res);

				if (($weakKey = checkWeakKeyX509(file_get_contents(
						$row['crt_name']))) !== "")
				{
					echo $weakKey, "<br/>\n";
					continue;
				}

				mysql_query("update `emailcerts` set `renewed`='1' where `id`='$id'");
				$query = "insert into emailcerts set
						`memid`='".$row['memid']."',
						`CN`='".mysql_real_escape_string($row['CN'])."',
						`subject`='".mysql_real_escape_string($row['subject'])."',
						`keytype`='".$row['keytype']."',
						`csr_name`='".$row['csr_name']."',
						`created`='".$row['created']."',
						`modified`=NOW(),
						`disablelogin`='".$row['disablelogin']."',
						`codesign`='".$row['codesign']."',
						`rootcert`='".$row['rootcert']."'";
				mysql_query($query);
				$newid = mysql_insert_id();
				$newfile=generatecertpath("csr","client",$newid);
				copy($row['csr_name'], $newfile);
				mysql_query("update `emailcerts` set `csr_name`='$newfile' where `id`='$newid'");
				$res = mysql_query("select * from `emaillink` where `emailcertsid`='".$row['id']."'");
				while($r2 = mysql_fetch_assoc($res))
				{
					mysql_query("insert into `emaillink` set `emailid`='".$r2['emailid']."',
							`emailcertsid`='$newid'");
				}
				waitForResult("emailcerts", $newid,$oldid,0);
				$query = "select * from `emailcerts` where `id`='$newid' and `crt_name` != ''";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
				} else {
					printf(_("Certificate for '%s' has been renewed."), $row['CN']);
					echo "<br/>\n<a href='account.php?id=6&cert=$newid' target='_new'>".
						_("Click here")."</a> "._("to install your certificate.")."<br/><br/>\n";
				}
			}
		}
		else
		{
			echo _("You did not select any certificates for renewal.")."<br/>";
		}

		showfooter();
		exit;
	}

	if($oldid == 5 && array_key_exists('revoke',$_REQUEST) && $_REQUEST['revoke'] != "")
	{
		$id = 5;
		showheader(_("My CAcert.org Account!"));
		if(array_key_exists('revokeid',$_REQUEST) && is_array($_REQUEST['revokeid']))
		{
			echo _("Now revoking the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`revoked`) as `revoke` from `emailcerts`
						where `id`='$id' and `memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['revoke'] > 0)
				{
					printf(_("It would seem '%s' has already been revoked. I'll skip this for now.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("update `emailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='$id'");
				printf(_("Certificate for '%s' has been revoked.")."<br>\n", $row['CN']);
			}
		}
		else
		{
			echo _("You did not select any certificates for revocation.");
		}

		if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
		{
			echo _("Now deleting the following pending requests:")."<br>\n";
			foreach($_REQUEST['delid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`expire`) as `expired` from `emailcerts`
						where `id`='$id' and `memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['expired'] > 0)
				{
					printf(_("Couldn't remove the request for `%s`, request had already been processed.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("delete from `emailcerts` where `id`='$id'");
				@unlink($row['csr_name']);
				@unlink($row['crt_name']);
				printf(_("Removed a pending request for '%s'")."<br>\n", $row['CN']);
			}
		}
		showfooter();
		exit;
	}

	if($oldid == 5 && array_key_exists('change',$_REQUEST) && $_REQUEST['change'] != "")
	{
	  showheader(_("My CAcert.org Account!"));
	  //echo _("Now changing the settings for the following certificates:")."<br>\n";
	  foreach($_REQUEST as $id => $val)
	  {
	    //echo $id."<br/>";
	    if(substr($id,0,5)=="cert_")
	    {
	      $id = intval(substr($id,5));
	      $dis=(array_key_exists('disablelogin_'.$id,$_REQUEST) && $_REQUEST['disablelogin_'.$id]=="1")?"0":"1";
	      //echo "$id -> ".$_REQUEST['disablelogin_'.$id]."<br/>\n";
	      mysql_query("update `emailcerts` set `disablelogin`='$dis' where `id`='$id' and `memid`='".$_SESSION['profile']['id']."'");
	      //$row = mysql_fetch_assoc($res);
	    }
	  }
	  echo(_("Certificate settings have been changed.")."<br/>\n");
	  showfooter();
	  exit;
	}


	if($oldid == 13 && $process != "")
	{
		csrf_check("perschange");
		$_SESSION['_config']['user'] = $_SESSION['profile'];

		$_SESSION['_config']['user']['Q1'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['Q1']))));
		$_SESSION['_config']['user']['Q2'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['Q2']))));
		$_SESSION['_config']['user']['Q3'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['Q3']))));
		$_SESSION['_config']['user']['Q4'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['Q4']))));
		$_SESSION['_config']['user']['Q5'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['Q5']))));
		$_SESSION['_config']['user']['A1'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['A1']))));
		$_SESSION['_config']['user']['A2'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['A2']))));
		$_SESSION['_config']['user']['A3'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['A3']))));
		$_SESSION['_config']['user']['A4'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['A4']))));
		$_SESSION['_config']['user']['A5'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['A5']))));

                if($_SESSION['_config']['user']['Q1'] == $_SESSION['_config']['user']['Q2'] ||
                        $_SESSION['_config']['user']['Q1'] == $_SESSION['_config']['user']['Q3'] ||
                        $_SESSION['_config']['user']['Q1'] == $_SESSION['_config']['user']['Q4'] ||
                        $_SESSION['_config']['user']['Q1'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['Q2'] == $_SESSION['_config']['user']['Q3'] ||
                        $_SESSION['_config']['user']['Q2'] == $_SESSION['_config']['user']['Q4'] ||
                        $_SESSION['_config']['user']['Q2'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['Q3'] == $_SESSION['_config']['user']['Q4'] ||
                        $_SESSION['_config']['user']['Q3'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['Q4'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['Q1'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['Q2'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['Q3'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['Q4'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['A2'] == $_SESSION['_config']['user']['Q3'] ||
                        $_SESSION['_config']['user']['A2'] == $_SESSION['_config']['user']['Q4'] ||
                        $_SESSION['_config']['user']['A2'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['A3'] == $_SESSION['_config']['user']['Q4'] ||
                        $_SESSION['_config']['user']['A3'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['A4'] == $_SESSION['_config']['user']['Q5'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['A2'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['A3'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['A4'] ||
                        $_SESSION['_config']['user']['A1'] == $_SESSION['_config']['user']['A5'] ||
                        $_SESSION['_config']['user']['A2'] == $_SESSION['_config']['user']['A3'] ||
                        $_SESSION['_config']['user']['A2'] == $_SESSION['_config']['user']['A4'] ||
                        $_SESSION['_config']['user']['A2'] == $_SESSION['_config']['user']['A5'] ||
                        $_SESSION['_config']['user']['A3'] == $_SESSION['_config']['user']['A4'] ||
                        $_SESSION['_config']['user']['A3'] == $_SESSION['_config']['user']['A5'] ||
                        $_SESSION['_config']['user']['A4'] == $_SESSION['_config']['user']['A5'])
                {
                        $_SESSION['_config']['errmsg'] .= _("For your own security you must enter 5 different password questions and answers. You aren't allowed to duplicate questions, set questions as answers or use the question as the answer.")."<br>\n";
                        $id = $oldid;
			$oldid=0;
                }

		if($_SESSION['_config']['user']['Q1'] == "" || $_SESSION['_config']['user']['Q2'] == "" ||
			$_SESSION['_config']['user']['Q3'] == "" || $_SESSION['_config']['user']['Q4'] == "" ||
			$_SESSION['_config']['user']['Q5'] == "")
		{
			$_SESSION['_config']['errmsg'] .= _("For your own security you must enter 5 lost password questions and answers.")."<br>";
			$id = $oldid;
			$oldid=0;
		}
	}

	if($oldid == 13 && $process != "")
	{
		$ddquery = "select sum(`points`) as `total` from `notary` where `to`='".$_SESSION['profile']['id']."' group by `to`";
		$ddres = mysql_query($ddquery);
		$ddrow = mysql_fetch_assoc($ddres);
		$_SESSION['profile']['points'] = $ddrow['total'];

		if($_SESSION['profile']['points'] == 0)
		{
			$_SESSION['_config']['user']['fname'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['fname']))));
			$_SESSION['_config']['user']['mname'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['mname']))));
			$_SESSION['_config']['user']['lname'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['lname']))));
			$_SESSION['_config']['user']['suffix'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['suffix']))));
			$_SESSION['_config']['user']['day'] = intval($_REQUEST['day']);
			$_SESSION['_config']['user']['month'] = intval($_REQUEST['month']);
			$_SESSION['_config']['user']['year'] = intval($_REQUEST['year']);

			if($_SESSION['_config']['user']['fname'] == "" || $_SESSION['_config']['user']['lname'] == "")
			{
				$_SESSION['_config']['errmsg'] .= _("First and Last name fields can not be blank.")."<br>";
				$id = $oldid;
				$oldid=0;
			}
			if($_SESSION['_config']['user']['year'] < 1900 || $_SESSION['_config']['user']['month'] < 1 || $_SESSION['_config']['user']['month'] > 12 ||
				$_SESSION['_config']['user']['day'] < 1 || $_SESSION['_config']['user']['day'] > 31)
			{
				$_SESSION['_config']['errmsg'] .= _("Invalid date of birth")."<br>\n";
				$id = $oldid;
				$oldid=0;
			}
		}
	}

	if($oldid == 13 && $process != "")
	{
		if($_SESSION['profile']['points'] == 0)
		{
			$query = "update `users` set `fname`='".$_SESSION['_config']['user']['fname']."',
						`mname`='".$_SESSION['_config']['user']['mname']."',
						`lname`='".$_SESSION['_config']['user']['lname']."',
						`suffix`='".$_SESSION['_config']['user']['suffix']."',
						`dob`='".$_SESSION['_config']['user']['year']."-".$_SESSION['_config']['user']['month']."-".$_SESSION['_config']['user']['day']."'
						where `id`='".$_SESSION['profile']['id']."'";
			mysql_query($query);
		}
		$query = "update `users` set `Q1`='".$_SESSION['_config']['user']['Q1']."',
						`Q2`='".$_SESSION['_config']['user']['Q2']."',
						`Q3`='".$_SESSION['_config']['user']['Q3']."',
						`Q4`='".$_SESSION['_config']['user']['Q4']."',
						`Q5`='".$_SESSION['_config']['user']['Q5']."',
						`A1`='".$_SESSION['_config']['user']['A1']."',
						`A2`='".$_SESSION['_config']['user']['A2']."',
						`A3`='".$_SESSION['_config']['user']['A3']."',
						`A4`='".$_SESSION['_config']['user']['A4']."',
						`A5`='".$_SESSION['_config']['user']['A5']."'
						where `id`='".$_SESSION['profile']['id']."'";
		mysql_query($query);

		//!!!Should be rewritten
		$_SESSION['_config']['user']['otphash'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['otphash']))));
		$_SESSION['_config']['user']['otppin'] = trim(mysql_real_escape_string(stripslashes(strip_tags($_REQUEST['otppin']))));
		if($_SESSION['_config']['user']['otphash'] != "" && $_SESSION['_config']['user']['otppin'] != "")
		{
			$query = "update `users` set `otphash`='".$_SESSION['_config']['user']['otphash']."',
						`otppin`='".$_SESSION['_config']['user']['otppin']."' where `id`='".$_SESSION['profile']['id']."'";
			mysql_query($query);
		}

		$_SESSION['_config']['user']['set'] = 0;
		$_SESSION['profile'] = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".$_SESSION['profile']['id']."'"));
		$_SESSION['profile']['loggedin'] = 1;

		$ddquery = "select sum(`points`) as `total` from `notary` where `to`='".$_SESSION['profile']['id']."' group by `to`";
		$ddres = mysql_query($ddquery);
		$ddrow = mysql_fetch_assoc($ddres);
		$_SESSION['profile']['points'] = $ddrow['total'];


		$id = 13;
		showheader(_("My CAcert.org Account!"));
		echo _("Your details have been updated with the database.");
		showfooter();
		exit;
	}

	if($oldid == 14 && $process != "")
	{
		$_SESSION['_config']['user']['oldpass'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['oldpassword'])));
		$_SESSION['_config']['user']['pword1'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['pword1'])));
		$_SESSION['_config']['user']['pword2'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['pword2'])));

		$id = 14;
		csrf_check("pwchange");

		showheader(_("My CAcert.org Account!"));
		if($_SESSION['_config']['user']['pword1'] == "" || $_SESSION['_config']['user']['pword1'] != $_SESSION['_config']['user']['pword2'])
		{
			echo '<h3 style="color:red">', _("Failure: Pass Phrase not Changed"),
				'</h3>', "\n";
			echo _("New Pass Phrases specified don't match or were blank.");
		} else {
			$score = checkpw($_SESSION['_config']['user']['pword1'], $_SESSION['profile']['email'], $_SESSION['profile']['fname'],
						$_SESSION['profile']['mname'], $_SESSION['profile']['lname'], $_SESSION['profile']['suffix']);

			if($_SESSION['_config']['hostname'] != $_SESSION['_config']['securehostname'])
			{
				$match = mysql_query("select * from `users` where `id`='".$_SESSION['profile']['id']."' and
						(`password`=old_password('".$_SESSION['_config']['user']['oldpass']."') or
						`password`=sha1('".$_SESSION['_config']['user']['oldpass']."'))");
				$rc = mysql_num_rows($match);
			} else {
				$rc = 1;
			}

			if(strlen($_SESSION['_config']['user']['pword1']) < 6) {
				echo '<h3 style="color:red">',
					_("Failure: Pass Phrase not Changed"), '</h3>', "\n";
				echo _("The Pass Phrase you submitted was too short.");
			} else if($score < 3) {
				echo '<h3 style="color:red">',
					_("Failure: Pass Phrase not Changed"), '</h3>', "\n";
				printf(_("The Pass Phrase you submitted failed to contain enough differing characters and/or contained words from your name and/or email address. Only scored %s points out of 6."), $score);
			} else if($rc <= 0) {
				echo '<h3 style="color:red">',
					_("Failure: Pass Phrase not Changed"), '</h3>', "\n";
				echo _("You failed to correctly enter your current Pass Phrase.");
			} else {
				mysql_query("update `users` set `password`=sha1('".$_SESSION['_config']['user']['pword1']."')
						where `id`='".$_SESSION['profile']['id']."'");
				echo '<h3>', _("Pass Phrase Changed Successfully"), '</h3>', "\n";
				echo _("Your Pass Phrase has been updated and your primary email account has been notified of the change.");
				$body  = sprintf(_("Hi %s,"),$_SESSION['profile']['fname'])."\n\n";
				$body .= _("You are receiving this email because you or someone else ".
						"has changed the password on your account.")."\n\n";

				$body .= _("Best regards")."\n"._("CAcert.org Support!");

				sendmail($_SESSION['profile']['email'], "[CAcert.org] "._("Password Update Notification"), $body,
						"support@cacert.org", "", "", "CAcert Support");
			}
		}
		showfooter();
		exit;
	}

	if($oldid == 16)
	{
		$id = 16;
		$_SESSION['_config']['emails'] = array();

		foreach($_REQUEST['emails'] as $val)
		{
			$val = mysql_real_escape_string(stripslashes(trim($val)));
			$bits = explode("@", $val);
			$count = count($bits);
			if($count != 2)
				continue;

			if(checkownership($bits[1]) == false)
				continue;

			if(!is_array($_SESSION['_config']['row']))
				continue;
			else if($_SESSION['_config']['row']['id'] > 0)
				$_SESSION['_config']['domids'][] = $_SESSION['_config']['row']['id'];

			if($val != "")
				$_SESSION['_config']['emails'][] = $val;
		}
		$_SESSION['_config']['name'] = mysql_real_escape_string(stripslashes(trim($_REQUEST['name'])));
		$_SESSION['_config']['OU'] = mysql_real_escape_string(stripslashes(trim($_REQUEST['OU'])));
	}

	if($oldid == 16 && (intval(count($_SESSION['_config']['emails'])) + 0) <= 0)
	{
		$id = 16;
		showheader(_("My CAcert.org Account!"));
		echo _("I couldn't match any emails against your organisational account.");
		showfooter();
		exit;
	}

	if($oldid == 16 && $process != "")
	{

		if(array_key_exists('codesign',$_REQUEST) && $_REQUEST['codesign'] && $_SESSION['profile']['codesign'] && ($_SESSION['profile']['points'] >= 100))
		{
			$_REQUEST['codesign'] = 1;
			$_SESSION['_config']['codesign'] = 1;
		}
		else
		{
			$_REQUEST['codesign'] = 0;
			$_SESSION['_config']['codesign'] = 0;
		}

		$_SESSION['_config']['rootcert'] = intval($_REQUEST['rootcert']);
		if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
			$_SESSION['_config']['rootcert'] = 1;

		if(@count($_SESSION['_config']['emails']) > 0)
			$id = 17;
	}

	if($oldid == 17)
	{
		$org = $_SESSION['_config']['row'];
		if($_REQUEST['keytype'] == "NS")
		{
			$spkac=""; if(preg_match("/^[a-zA-Z0-9+=\/]+$/", trim(str_replace("\n", "", str_replace("\r", "",$_REQUEST['SPKAC']))))) $spkac=trim(str_replace("\n", "", str_replace("\r", "",$_REQUEST['SPKAC'])));

			if($spkac == "" || strlen($spkac) < 128)
			{
				$id = 17;
				showheader(_("My CAcert.org Account!"));
				echo _("I didn't receive a valid Certificate Request, hit the back button and try again.");
				showfooter();
				exit;
			}

			$count = 0;
			$emails = "";
			$addys = array();
			if(is_array($_SESSION['_config']['emails']))
			foreach($_SESSION['_config']['emails'] as $_REQUEST['email'])
			{
				if(!$emails)
					$defaultemail = $_REQUEST['email'];
				$emails .= "$count.emailAddress = $_REQUEST[email]\n";
				$count++;
			}
			if($_SESSION['_config']['name'] != "")
				$emails .= "commonName = ".$_SESSION['_config']['name']."\n";
			if($_SESSION['_config']['OU'])
				$emails .= "organizationalUnitName = ".$_SESSION['_config']['OU']."\n";
			if($org['O'])
				$emails .= "organizationName = ".$org['O']."\n";
			if($org['L'])
				$emails .= "localityName = ".$org['L']."\n";
			if($org['ST'])
				$emails .= "stateOrProvinceName = ".$org['ST']."\n";
			if($org['C'])
				$emails .= "countryName = ".$org['C']."\n";
			if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
				$_SESSION['_config']['rootcert'] = 1;

			$emails .= "SPKAC = $spkac";
			if (($weakKey = checkWeakKeySPKAC($emails)) !== "")
			{
				$id = 17;
				showheader(_("My CAcert.org Account!"));
				echo $weakKey;
				showfooter();
				exit;
			}

			$query = "insert into `orgemailcerts` set
						`CN`='$defaultemail',
						`keytype`='NS',
						`orgid`='".$org['orgid']."',
						`created`=FROM_UNIXTIME(UNIX_TIMESTAMP()),
						`codesign`='".$_SESSION['_config']['codesign']."',
						`rootcert`='".$_SESSION['_config']['rootcert']."'";
			mysql_query($query);
			$emailid = mysql_insert_id();

			foreach($_SESSION['_config']['domids'] as $addy)
				mysql_query("insert into `domemaillink` set `emailcertsid`='$emailid', `emailid`='$addy'");

			$CSRname=generatecertpath("csr","orgclient",$emailid);
			$fp = fopen($CSRname, "w");
			fputs($fp, $emails);
			fclose($fp);
			$challenge=$_SESSION['spkac_hash'];
                        $res=`openssl spkac -verify -in $CSRname`;
                        if(!strstr($res,"Challenge String: ".$challenge))
                        {
                                $id = $oldid;
                                showheader(_("My CAcert.org Account!"));
                                echo _("The challenge-response code of your certificate request did not match. Can't continue with certificaterequest.");
                                showfooter();
                                exit;
                        }
			mysql_query("update `orgemailcerts` set `csr_name`='$CSRname' where `id`='$emailid'");
		} else if($_REQUEST['keytype'] == "MS" || $_REQUEST['keytype']=="VI") {
			$csr = "-----BEGIN CERTIFICATE REQUEST-----\n".clean_csr($_REQUEST['CSR'])."-----END CERTIFICATE REQUEST-----\n";

			if (($weakKey = checkWeakKeyCSR($csr)) !== "")
			{
				$id = 17;
				showheader(_("My CAcert.org Account!"));
				echo $weakKey;
				showfooter();
				exit;
			}

			$tmpfname = tempnam("/tmp", "id17CSR");
			$fp = fopen($tmpfname, "w");
			fputs($fp, $csr);
			fclose($fp);

			$addys = array();
			$defaultemail = "";
			$csrsubject="";

			if($_SESSION['_config']['name'] != "")
				$csrsubject = "/CN=".$_SESSION['_config']['name'];
			if(is_array($_SESSION['_config']['emails']))
			foreach($_SESSION['_config']['emails'] as $_REQUEST['email'])
			{
				if($defaultemail == "")
					$defaultemail = $_REQUEST['email'];
				$csrsubject .= "/emailAddress=$_REQUEST[email]";
			}
			if($_SESSION['_config']['OU'])
				$csrsubject .= "/organizationalUnitName=".$_SESSION['_config']['OU'];
			if($org['O'])
				$csrsubject .= "/organizationName=".$org['O'];
			if($org['L'])
				$csrsubject .= "/localityName=".$org['L'];
			if($org['ST'])
				$csrsubject .= "/stateOrProvinceName=".$org['ST'];
			if($org['C'])
				$csrsubject .= "/countryName=".$org['C'];

			$tmpname = tempnam("/tmp", "id17csr");
			$do = `/usr/bin/openssl req -in $tmpfname -out $tmpname`;
			@unlink($tmpfname);
			$csr = "";
			$fp = fopen($tmpname, "r");
			while($data = fgets($fp, 4096))
				$csr .= $data;
			fclose($fp);
			@unlink($tmpname);

			if($csr == "")
			{
				showheader(_("My CAcert.org Account!"));
				echo _("I didn't receive a valid Certificate Request, hit the back button and try again.");
				showfooter();
				exit;
			}
			if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
				$_SESSION['_config']['rootcert'] = 1;

			$query = "insert into `orgemailcerts` set
						`CN`='$defaultemail',
						`keytype`='" . sanitizeHTML($_REQUEST['keytype']) . "',
						`orgid`='".$org['orgid']."',
						`created`=FROM_UNIXTIME(UNIX_TIMESTAMP()),
						`subject`='$csrsubject',
						`codesign`='".$_SESSION['_config']['codesign']."',
						`rootcert`='".$_SESSION['_config']['rootcert']."'";
			mysql_query($query);
			$emailid = mysql_insert_id();

			foreach($_SESSION['_config']['domids'] as $addy)
				mysql_query("insert into `domemaillink` set `emailcertsid`='$emailid', `emailid`='$addy'");

			$CSRname=generatecertpath("csr","orgclient",$emailid);
			$fp = fopen($CSRname, "w");
			fputs($fp, $csr);
			fclose($fp);
			mysql_query("update `orgemailcerts` set `csr_name`='$CSRname' where `id`='$emailid'");
		}
		waitForResult("orgemailcerts", $emailid,$oldid);
		$query = "select * from `orgemailcerts` where `id`='$emailid' and `crt_name` != ''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		} else {
			$id = 19;
			$cert = $emailid;
			$_REQUEST['cert']=$emailid;
		}
	}

	if($oldid == 18 && array_key_exists('renew',$_REQUEST) && $_REQUEST['renew'] != "")
	{
		csrf_check('clicerchange');
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			$id = 18;
			echo _("Now renewing the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				echo "Renewing certificate #$id ...\n<br/>";
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`revoked`) as `revoke` from `orgemailcerts`, `org`
						where `orgemailcerts`.`id`='$id' and `org`.`memid`='".$_SESSION['profile']['id']."' and
						`org`.`orgid`=`orgemailcerts`.`orgid`";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}

				$row = mysql_fetch_assoc($res);

				if (($weakKey = checkWeakKeyX509(file_get_contents(
						$row['crt_name']))) !== "")
				{
					echo $weakKey, "<br/>\n";
					continue;
				}

				mysql_query("update `orgemailcerts` set `renewed`='1' where `id`='$id'");
				if($row['revoke'] > 0)
				{
					printf(_("It would seem '%s' has already been revoked. I'll skip this for now.")."<br>\n", $row['CN']);
					continue;
				}
				$query = "insert into `orgemailcerts` set
						`orgid`='".$row['orgid']."',
						`CN`='".$row['CN']."',
						`subject`='".$row['subject']."',
						`keytype`='".$row['keytype']."',
						`csr_name`='".$row['csr_name']."',
						`created`='".$row['created']."',
						`modified`=NOW(),
						`codesign`='".$row['codesign']."',
						`rootcert`='".$row['rootcert']."'";
				mysql_query($query);
				$newid = mysql_insert_id();
				$newfile=generatecertpath("csr","orgclient",$newid);
				copy($row['csr_name'], $newfile);
				mysql_query("update `orgemailcerts` set `csr_name`='$newfile' where `id`='$newid'");
				waitForResult("orgemailcerts", $newid,$oldid,0);
				$query = "select * from `orgemailcerts` where `id`='$newid' and `crt_name` != ''";
				$res = mysql_query($query);
				if(mysql_num_rows($res) > 0)
				{
					printf(_("Certificate for '%s' has been renewed."), $row['CN']);
					echo "<a href='account.php?id=19&cert=$newid' target='_new'>".
						_("Click here")."</a> "._("to install your certificate.");
				}
				echo("<br/>");
			}
		}
		else
		{
			echo _("You did not select any certificates for renewal.");
		}
		showfooter();
		exit;
	}

	if($oldid == 18 && array_key_exists('revoke',$_REQUEST) && $_REQUEST['revoke'] != "")
	{
		csrf_check('clicerchange');
		$id = 18;
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			echo _("Now revoking the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`revoked`) as `revoke` from `orgemailcerts`, `org`
						where `orgemailcerts`.`id`='$id' and `org`.`memid`='".$_SESSION['profile']['id']."' and
						`org`.`orgid`=`orgemailcerts`.`orgid`";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['revoke'] > 0)
				{
					printf(_("It would seem '%s' has already been revoked. I'll skip this for now.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("update `orgemailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='$id'");
				printf(_("Certificate for '%s' has been revoked.")."<br>\n", $row['CN']);
			}
		}
		else
		{
			echo _("You did not select any certificates for revocation.");
		}

		if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
		{
			echo _("Now deleting the following pending requests:")."<br>\n";
			foreach($_REQUEST['delid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`expire`) as `expired` from `orgemailcerts`, `org`
						where `orgemailcerts`.`id`='$id' and `org`.`memid`='".$_SESSION['profile']['id']."' and
						`org`.`orgid`=`orgemailcerts`.`orgid`";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['expired'] > 0)
				{
					printf(_("Couldn't remove the request for `%s`, request had already been processed.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("delete from `orgemailcerts` where `id`='$id'");
				@unlink($row['csr_name']);
				@unlink($row['crt_name']);
				printf(_("Removed a pending request for '%s'")."<br>\n", $row['CN']);
			}
		}
		showfooter();
		exit;
	}

	if($process != "" && $oldid == 20)
	{
		$CSR = clean_csr($_REQUEST['CSR']);

		if (($weakKey = checkWeakKeyCSR($CSR)) !== "")
		{
			$id = 20;
			showheader(_("My CAcert.org Account!"));
			echo $weakKey;
			showfooter();
			exit;
		}

		$_SESSION['_config']['tmpfname'] = tempnam("/tmp", "id20CSR");
		$fp = fopen($_SESSION['_config']['tmpfname'], "w");
		fputs($fp, $CSR);
		fclose($fp);
		$CSR = $_SESSION['_config']['tmpfname'];
		$_SESSION['_config']['subject'] = trim(`/usr/bin/openssl req -text -noout -in "$CSR"|tr -d "\\0"|grep "Subject:"`);
		$bits = explode(",", trim(`/usr/bin/openssl req -text -noout -in "$CSR"|tr -d "\\0"|grep -A1 'X509v3 Subject Alternative Name:'|grep DNS:`));
		foreach($bits as $val)
		{
			$_SESSION['_config']['subject'] .= "/subjectAltName=".trim($val);
		}
		$id = 21;

		$_SESSION['_config']['0.CN'] = $_SESSION['_config']['0.subjectAltName'] = "";
		extractit();
		getcn2();
		getalt2();

		$query = "select * from `orginfo`,`org`,`orgdomains` where
				`org`.`memid`='".$_SESSION['profile']['id']."' and
				`org`.`orgid`=`orginfo`.`id` and
				`org`.`orgid`=`orgdomains`.`orgid` and
				`orgdomains`.`domain`='".$_SESSION['_config']['0.CN']."'";
		$_SESSION['_config']['CNorg'] = mysql_fetch_assoc(mysql_query($query));
		$query = "select * from `orginfo`,`org`,`orgdomains` where
				`org`.`memid`='".$_SESSION['profile']['id']."' and
				`org`.`orgid`=`orginfo`.`id` and
				`org`.`orgid`=`orgdomains`.`orgid` and
				`orgdomains`.`domain`='".$_SESSION['_config']['0.subjectAltName']."'";
		$_SESSION['_config']['SANorg'] = mysql_fetch_assoc(mysql_query($query));
//echo "<pre>"; print_r($_SESSION['_config']); die;

		if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
		{
			$id = 20;
			showheader(_("My CAcert.org Account!"));
			echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
			showfooter();
			exit;
		}

		$_SESSION['_config']['rootcert'] = intval($_REQUEST['rootcert']);
		if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
			$_SESSION['_config']['rootcert'] = 1;
	}

	if($process != "" && $oldid == 21)
	{
		$id = 21;

		if(!file_exists($_SESSION['_config']['tmpfname']))
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		}

		if (($weakKey = checkWeakKeyCSR(file_get_contents(
				$_SESSION['_config']['tmpfname']))) !== "")
		{
			showheader(_("My CAcert.org Account!"));
			echo $weakKey;
			showfooter();
			exit;
		}

		if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
			showfooter();
			exit;
		}

                if($_SESSION['_config']['rowid']['0'] > 0)
                {
			$query = "select * from `org`,`orginfo` where
					`orginfo`.`id`='".$_SESSION['_config']['rowid']['0']."' and
					`orginfo`.`id`=`org`.`orgid` and
					`org`.`memid`='".$_SESSION['profile']['id']."'";
		} else {
			$query = "select * from `org`,`orginfo` where
					`orginfo`.`id`='".$_SESSION['_config']['altid']['0']."' and
					`orginfo`.`id`=`org`.`orgid` and
					`org`.`memid`='".$_SESSION['profile']['id']."'";
		}
		$org = mysql_fetch_assoc(mysql_query($query));
		$csrsubject = "";

		if($_SESSION['_config']['OU'])
			$csrsubject .= "/organizationalUnitName=".$_SESSION['_config']['OU'];
		if($org['O'])
			$csrsubject .= "/organizationName=".$org['O'];
		if($org['L'])
			$csrsubject .= "/localityName=".$org['L'];
		if($org['ST'])
			$csrsubject .= "/stateOrProvinceName=".$org['ST'];
		if($org['C'])
			$csrsubject .= "/countryName=".$org['C'];
		//if($org['contact'])
		//	$csrsubject .= "/emailAddress=".trim($org['contact']);

		if(is_array($_SESSION['_config']['rows']))
			foreach($_SESSION['_config']['rows'] as $row)
				$csrsubject .= "/commonName=$row";
		$SAN="";
		if(is_array($_SESSION['_config']['altrows']))
			foreach($_SESSION['_config']['altrows'] as $subalt)
			{
				if($SAN != "")
					$SAN .= ",";
				$SAN .= "$subalt";
			}

		if($SAN != "")
			$csrsubject .= "/subjectAltName=".$SAN;

		$type="";
		if($_REQUEST["ocspcert"]!="" && $_SESSION['profile']['admin'] == 1) $type="8";
		if($_SESSION['_config']['rootcert'] < 1 || $_SESSION['_config']['rootcert'] > 2)
			$_SESSION['_config']['rootcert'] = 1;

                if($_SESSION['_config']['rowid']['0'] > 0)
                {
                        $query = "insert into `orgdomaincerts` set
						`CN`='".$_SESSION['_config']['rows']['0']."',
						`orgid`='".$org['id']."',
                                                `created`=NOW(),
						`subject`='$csrsubject',
						`rootcert`='".$_SESSION['_config']['rootcert']."',
						`type`='$type'";
                } else {
                        $query = "insert into `orgdomaincerts` set
						`CN`='".$_SESSION['_config']['altrows']['0']."',
						`orgid`='".$org['id']."',
                                                `created`=NOW(),
						`subject`='$csrsubject',
						`rootcert`='".$_SESSION['_config']['rootcert']."',
						`type`='$type'";
                }
                mysql_query($query);
		$CSRid = mysql_insert_id();

		$CSRname=generatecertpath("csr","orgserver",$CSRid);
		rename($_SESSION['_config']['tmpfname'], $CSRname);
		chmod($CSRname,0644);
		mysql_query("update `orgdomaincerts` set `CSR_name`='$CSRname' where `id`='$CSRid'");
		if(is_array($_SESSION['_config']['rowid']))
			foreach($_SESSION['_config']['rowid'] as $id)
				mysql_query("insert into `orgdomlink` set `orgdomid`='$id', `orgcertid`='$CSRid'");
		if(is_array($_SESSION['_config']['altid']))
			foreach($_SESSION['_config']['altid'] as $id)
				mysql_query("insert into `orgdomlink` set `orgdomid`='$id', `orgcertid`='$CSRid'");
		waitForResult("orgdomaincerts", $CSRid,$oldid);
		$query = "select * from `orgdomaincerts` where `id`='$CSRid' and `crt_name` != ''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions.")." CSRid: $CSRid", "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		} else {
			$id = 23;
			$cert = $CSRid;
			$_REQUEST['cert']=$CSRid;
		}
	}

	if($oldid == 22 && array_key_exists('renew',$_REQUEST) && $_REQUEST['renew'] != "")
	{
		csrf_check('orgsrvcerchange');
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			echo _("Now renewing the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`orgdomaincerts`.`revoked`) as `revoke` from
						`orgdomaincerts`,`org`
						where `orgdomaincerts`.`id`='$id' and
						`orgdomaincerts`.`orgid`=`org`.`orgid` and
						`org`.`memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}

				$row = mysql_fetch_assoc($res);

				if (($weakKey = checkWeakKeyX509(file_get_contents(
						$row['crt_name']))) !== "")
				{
					echo $weakKey, "<br/>\n";
					continue;
				}

				mysql_query("update `orgdomaincerts` set `renewed`='1' where `id`='$id'");
				if($row['revoke'] > 0)
				{
					printf(_("It would seem '%s' has already been revoked. I'll skip this for now.")."<br>\n", $row['CN']);
					continue;
				}
				$query = "insert into `orgdomaincerts` set
						`orgid`='".$row['orgid']."',
						`CN`='".$row['CN']."',
						`csr_name`='".$row['csr_name']."',
						`created`='".$row['created']."',
						`modified`=NOW(),
						`subject`='".$row['subject']."',
						`type`='".$row['type']."',
						`rootcert`='".$row['rootcert']."'";
				mysql_query($query);
				$newid = mysql_insert_id();
				//echo "NewID: $newid<br/>\n";
				$newfile=generatecertpath("csr","orgserver",$newid);
				copy($row['csr_name'], $newfile);
				mysql_query("update `orgdomaincerts` set `csr_name`='$newfile' where `id`='$newid'");
				echo _("Renewing").": ".$row['CN']."<br>\n";
				$res = mysql_query("select * from `orgdomlink` where `orgcertid`='".$row['id']."'");
				while($r2 = mysql_fetch_assoc($res))
					mysql_query("insert into `orgdomlink` set `orgdomid`='".$r2['id']."', `orgcertid`='$newid'");
				waitForResult("orgdomaincerts", $newid,$oldid,0);
				$query = "select * from `orgdomaincerts` where `id`='$newid' and `crt_name` != ''";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions.")." newid: $newid", "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
				} else {
					$drow = mysql_fetch_assoc($res);
					$cert = `/usr/bin/openssl x509 -in $drow[crt_name]`;
					echo "<pre>\n$cert\n</pre>\n";
				}
			}
		}
		else
		{
			echo _("You did not select any certificates for renewal.");
		}
		showfooter();
		exit;
	}

	if($oldid == 22 && array_key_exists('revoke',$_REQUEST) && $_REQUEST['revoke'] != "")
	{
		csrf_check('orgsrvcerchange');
		showheader(_("My CAcert.org Account!"));
		if(is_array($_REQUEST['revokeid']))
		{
			echo _("Now revoking the following certificates:")."<br>\n";
			foreach($_REQUEST['revokeid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`orgdomaincerts`.`revoked`) as `revoke` from
						`orgdomaincerts`,`org`
						where `orgdomaincerts`.`id`='$id' and
						`orgdomaincerts`.`orgid`=`org`.`orgid` and
						`org`.`memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['revoke'] > 0)
				{
					printf(_("It would seem '%s' has already been revoked. I'll skip this for now.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("update `orgdomaincerts` set `revoked`='1970-01-01 10:00:01' where `id`='$id'");
				printf(_("Certificate for '%s' has been revoked.")."<br>\n", $row['CN']);
			}
		}
		else
		{
			echo _("You did not select any certificates for revocation.");
		}

		if(array_key_exists('delid',$_REQUEST) && is_array($_REQUEST['delid']))
		{
			echo _("Now deleting the following pending requests:")."<br>\n";
			foreach($_REQUEST['delid'] as $id)
			{
				$id = intval($id);
				$query = "select *,UNIX_TIMESTAMP(`orgdomaincerts`.`expire`) as `expired` from
						`orgdomaincerts`,`org`
						where `orgdomaincerts`.`id`='$id' and
						`orgdomaincerts`.`orgid`=`org`.`orgid` and
						`org`.`memid`='".$_SESSION['profile']['id']."'";
				$res = mysql_query($query);
				if(mysql_num_rows($res) <= 0)
				{
					printf(_("Invalid ID '%s' presented, can't do anything with it.")."<br>\n", $id);
					continue;
				}
				$row = mysql_fetch_assoc($res);
				if($row['expired'] > 0)
				{
					printf(_("Couldn't remove the request for `%s`, request had already been processed.")."<br>\n", $row['CN']);
					continue;
				}
				mysql_query("delete from `orgdomaincerts` where `id`='$id'");
				@unlink($row['csr_name']);
				@unlink($row['crt_name']);
				printf(_("Removed a pending request for '%s'")."<br>\n", $row['CN']);
			}
		}
		showfooter();
		exit;
	}

	if(($id == 24 || $oldid == 24 || $id == 25 || $oldid == 25 || $id == 26 || $oldid == 26 ||
		$id == 27 || $oldid == 27 || $id == 28 || $oldid == 28 || $id == 29 || $oldid == 29 ||
		$id == 30 || $oldid == 30 || $id == 31 || $oldid == 31) &&
		$_SESSION['profile']['orgadmin'] != 1)
	{
		showheader(_("My CAcert.org Account!"));
		echo _("You don't have access to this area.");
		showfooter();
		exit;
	}

	if($oldid == 24 && $process != "")
	{
		$id = intval($oldid);
		$_SESSION['_config']['O'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['O'])));
		$_SESSION['_config']['contact'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['contact'])));
		$_SESSION['_config']['L'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['L'])));
		$_SESSION['_config']['ST'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['ST'])));
		$_SESSION['_config']['C'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['C'])));
		$_SESSION['_config']['comments'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['comments'])));

		if($_SESSION['_config']['O'] == "" || $_SESSION['_config']['contact'] == "")
		{
			$_SESSION['_config']['errmsg'] = _("Organisation Name and Contact Email are required fields.");
		} else {
			mysql_query("insert into `orginfo` set `O`='".$_SESSION['_config']['O']."',
						`contact`='".$_SESSION['_config']['contact']."',
						`L`='".$_SESSION['_config']['L']."',
						`ST`='".$_SESSION['_config']['ST']."',
						`C`='".$_SESSION['_config']['C']."',
						`comments`='".$_SESSION['_config']['comments']."'");
			showheader(_("My CAcert.org Account!"));
			printf(_("'%s' has just been successfully added as an organisation to the database."), sanitizeHTML($_SESSION['_config']['O']));
			showfooter();
			exit;
		}
	}

	if($oldid == 27 && $process != "")
	{
		csrf_check('orgdetchange');
		$id = intval($oldid);
		$_SESSION['_config']['O'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['O'])));
		$_SESSION['_config']['contact'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['contact'])));
		$_SESSION['_config']['L'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['L'])));
		$_SESSION['_config']['ST'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['ST'])));
		$_SESSION['_config']['C'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['C'])));
		$_SESSION['_config']['comments'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['comments'])));

		if($_SESSION['_config']['O'] == "" || $_SESSION['_config']['contact'] == "")
		{
			$_SESSION['_config']['errmsg'] = _("Organisation Name and Contact Email are required fields.");
		} else {
			mysql_query("update `orginfo` set `O`='".$_SESSION['_config']['O']."',
						`contact`='".$_SESSION['_config']['contact']."',
						`L`='".$_SESSION['_config']['L']."',
						`ST`='".$_SESSION['_config']['ST']."',
						`C`='".$_SESSION['_config']['C']."',
						`comments`='".$_SESSION['_config']['comments']."'
					where `id`='".$_SESSION['_config']['orgid']."'");
			showheader(_("My CAcert.org Account!"));
			printf(_("'%s' has just been successfully updated in the database."), sanitizeHTML($_SESSION['_config']['O']));
			showfooter();
			exit;
		}
	}

	if($oldid == 28 && $process != "" && array_key_exists("domainname",$_REQUEST))
	{
		$domain = $_SESSION['_config']['domain'] = trim(mysql_real_escape_string(stripslashes($_REQUEST['domainname'])));
		$res1 = mysql_query("select * from `orgdomains` where `domain`='$domain'");
		if(mysql_num_rows($res1) > 0)
		{
			$_SESSION['_config']['errmsg'] = sprintf(_("The domain '%s' is already in a different account and is listed as valid. Can't continue."), sanitizeHTML($domain));
			$id = $oldid;
			$oldid=0;
		}
	}

	if($oldid == 28 && $_SESSION['_config']['orgid'] <= 0)
	{
		$oldid=0;
		$id = 25;
	}

	if($oldid == 28 && $process != "" && array_key_exists("orgid",$_SESSION["_config"]))
	{
		mysql_query("insert into `orgdomains` set `orgid`='".intval($_SESSION['_config']['orgid'])."', `domain`='$domain'");
		showheader(_("My CAcert.org Account!"));
		printf(_("'%s' has just been successfully added to the database."), sanitizeHTML($domain));
		echo "<br><br><a href='account.php?id=26&orgid=".intval($_SESSION['_config']['orgid'])."'>"._("Click here")."</a> "._("to continue.");
		showfooter();
		exit;
	}

	if($oldid == 29 && $process != "")
	{
		$domain = mysql_real_escape_string(stripslashes(trim($_REQUEST['domainname'])));

		$res1 = mysql_query("select * from `orgdomains` where `domain` like '$domain' and `id`!='".intval($domid)."'");
		$res2 = mysql_query("select * from `domains` where `domain` like '$domain' and `deleted`=0");
		if(mysql_num_rows($res1) > 0 || mysql_num_rows($res2) > 0)
		{
			$_SESSION['_config']['errmsg'] = sprintf(_("The domain '%s' is already in a different account and is listed as valid. Can't continue."), sanitizeHTML($domain));
			$id = $oldid;
			$oldid=0;
		}
	}

	if(($oldid == 29 || $oldid == 30) && $process != "")      // _("Cancel") is handled in front of account.php
	{
		$query = "select `orgdomaincerts`.`id` as `id` from `orgdomlink`, `orgdomaincerts`, `orgdomains` where
				`orgdomlink`.`orgdomid`=`orgdomains`.`id` and
				`orgdomaincerts`.`id`=`orgdomlink`.`orgcertid` and
				`orgdomains`.`id`='".intval($domid)."'";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
			mysql_query("update `orgdomaincerts` set `revoked`='1970-01-01 10:00:01' where `id`='".$row['id']."'");

		$query = "select `orgemailcerts`.`id` as `id` from `orgemailcerts`, `orgemaillink`, `orgdomains` where
				`orgemaillink`.`domid`=`orgdomains`.`id` and
				`orgemailcerts`.`id`=`orgemaillink`.`emailcertsid` and
				`orgdomains`.`id`='".intval($domid)."'";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
			mysql_query("update `orgemailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='".intval($row['id'])."'");
	}

	if($oldid == 29 && $process != "")
	{
		$row = mysql_fetch_assoc(mysql_query("select * from `orgdomains` where `id`='".intval($domid)."'"));
		mysql_query("update `orgdomains` set `domain`='$domain' where `id`='".intval($domid)."'");
		showheader(_("My CAcert.org Account!"));
		printf(_("'%s' has just been successfully updated in the database."), sanitizeHTML($domain));
		echo "<br><br><a href='account.php?id=26&orgid=".intval($orgid)."'>"._("Click here")."</a> "._("to continue.");
		showfooter();
		exit;
	}

	if($oldid == 30 && $process != "")
	{
		$row = mysql_fetch_assoc(mysql_query("select * from `orgdomains` where `id`='".intval($domid)."'"));
		$domain = $row['domain'];
		mysql_query("delete from `orgdomains` where `id`='".intval($domid)."'");
		showheader(_("My CAcert.org Account!"));
		printf(_("'%s' has just been successfully deleted from the database."), sanitizeHTML($domain));
		echo "<br><br><a href='account.php?id=26&orgid=".intval($orgid)."'>"._("Click here")."</a> "._("to continue.");
		showfooter();
		exit;
	}

	if($oldid == 30)
	{
		$id = 26;
		$orgid = 0;
	}

	if($oldid == 31 && $process != "")
	{
		$query = "select * from `orgdomains` where `orgid`='".intval($_SESSION['_config']['orgid'])."'";
		$dres = mysql_query($query);
		while($drow = mysql_fetch_assoc($dres))
		{
			$query = "select `orgdomaincerts`.`id` as `id` from `orgdomlink`, `orgdomaincerts`, `orgdomains` where
					`orgdomlink`.`orgdomid`=`orgdomains`.`id` and
					`orgdomaincerts`.`id`=`orgdomlink`.`orgcertid` and
					`orgdomains`.`id`='".intval($drow['id'])."'";
			$res = mysql_query($query);
			while($row = mysql_fetch_assoc($res))
			{
				mysql_query("update `orgdomaincerts` set `revoked`='1970-01-01 10:00:01' where `id`='".intval($row['id'])."'");
				mysql_query("delete from `orgdomaincerts` where `orgid`='".intval($row['id'])."'");
				mysql_query("delete from `orgdomlink` where `domid`='".intval($row['id'])."'");
			}

			$query = "select `orgemailcerts`.`id` as `id` from `orgemailcerts`, `orgemaillink`, `orgdomains` where
					`orgemaillink`.`domid`=`orgdomains`.`id` and
					`orgemailcerts`.`id`=`orgemaillink`.`emailcertsid` and
					`orgdomains`.`id`='".intval($drow['id'])."'";
			$res = mysql_query($query);
			while($row = mysql_fetch_assoc($res))
			{
				mysql_query("update `orgemailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='".intval($row['id'])."'");
				mysql_query("delete from `orgemailcerts` where `id`='".intval($row['id'])."'");
				mysql_query("delete from `orgemaillink` where `domid`='".intval($row['id'])."'");
			}
		}
		mysql_query("delete from `org` where `orgid`='".intval($_SESSION['_config']['orgid'])."'");
		mysql_query("delete from `orgdomains` where `orgid`='".intval($_SESSION['_config']['orgid'])."'");
		mysql_query("delete from `orginfo` where `id`='".intval($_SESSION['_config']['orgid'])."'");
	}

	if($oldid == 31)
	{
		$id = 25;
		$orgid = 0;
	}

	if($id == 32 || $oldid == 32 || $id == 33 || $oldid == 33 || $id == 34 || $oldid == 34)
	{
		$query = "select * from `org` where `memid`='".intval($_SESSION['profile']['id'])."' and `masteracc`='1'";
		$_macc = mysql_num_rows(mysql_query($query));
		if($_SESSION['profile']['orgadmin'] != 1 && $_macc <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("You don't have access to this area.");
			showfooter();
			exit;
		}
	}

	if($id == 35 || $oldid == 35)
	{
		$query = "select 1 from `org` where `memid`='".intval($_SESSION['profile']['id'])."'";
		$is_orguser = mysql_num_rows(mysql_query($query));
		if($_SESSION['profile']['orgadmin'] != 1 && $is_orguser <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("You don't have access to this area.");
			showfooter();
			exit;
		}
	}

	if($id == 33 && $_SESSION['profile']['orgadmin'] != 1)
	{
		$orgid = intval($_SESSION['_config']['orgid']);
		$query = "select * from `org` where `orgid`='$orgid' and `memid`='".intval($_SESSION['profile']['id'])."' and `masteracc`='1'";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$id = 35;
		}
	}

	if($oldid == 33 && $process != "")
	{
		csrf_check('orgadmadd');
		if($_SESSION['profile']['orgadmin'] == 1)
			$masteracc = $_SESSION['_config'][masteracc] = intval($_REQUEST['masteracc']);
		else
			$masteracc = $_SESSION['_config'][masteracc] = 0;
		$_REQUEST['email'] = $_SESSION['_config']['email'] = mysql_real_escape_string(stripslashes(trim($_REQUEST['email'])));
		$OU = $_SESSION['_config']['OU'] = mysql_real_escape_string(stripslashes(trim($_REQUEST['OU'])));
		$comments = $_SESSION['_config']['comments'] = mysql_real_escape_string(stripslashes(trim($_REQUEST['comments'])));
		$res = mysql_query("select * from `users` where `email`='".$_REQUEST['email']."' and `deleted`=0");
		if(mysql_num_rows($res) <= 0)
		{
			$id = $oldid;
			$oldid=0;
			$_SESSION['_config']['errmsg'] = sprintf(_("Wasn't able to match '%s' against any user in the system"), sanitizeHTML($_REQUEST['email']));
		} else {
			$row = mysql_fetch_assoc($res);
			if ( !is_assurer(intval($row['id'])) )
			{
				$id = $oldid;
				$oldid=0;
				$_SESSION['_config']['errmsg'] =
						_("The user is not an Assurer yet");
			} else {
				mysql_query(
					"insert into `org`
						set `memid`='".intval($row['id'])."',
							`orgid`='".intval($_SESSION['_config']['orgid'])."',
							`masteracc`='$masteracc',
							`OU`='$OU',
							`comments`='$comments'");
			}
		}
	}

	if(($oldid == 34 || $id == 34) && $_SESSION['profile']['orgadmin'] != 1)
	{
		$orgid = intval($_SESSION['_config']['orgid']);
		$res = mysql_query("select * from `org` where `orgid`='$orgid' and `memid`='".$_SESSION['profile']['id']."' and `masteracc`='1'");
		if(mysql_num_rows($res) <= 0)
			$id = 32;
	}

	if($oldid == 34 && $process != "")
	{
		$orgid = intval($_SESSION['_config']['orgid']);
		$memid = intval($_REQUEST['memid']);
		$query = "delete from `org` where `orgid`='$orgid' and `memid`='$memid'";
		mysql_query($query);
	}

	if($oldid == 34 || $oldid == 33)
	{
		$oldid=0;
		$id = 32;
		$orgid = 0;
	}

	if($id == 36)
	{
		$row = mysql_fetch_assoc(mysql_query("select * from `alerts` where `memid`='".intval($_SESSION['profile']['id'])."'"));
		$_REQUEST['general'] = $row['general'];
		$_REQUEST['country'] = $row['country'];
		$_REQUEST['regional'] = $row['regional'];
		$_REQUEST['radius'] = $row['radius'];
	}

	if($oldid == 36)
	{
		$rc = mysql_num_rows(mysql_query("select * from `alerts` where `memid`='".intval($_SESSION['profile']['id'])."'"));
		if($rc > 0)
		{
			$query = "update `alerts` set `general`='".intval(array_key_exists('general',$_REQUEST)?$_REQUEST['general']:0)."',
							`country`='".intval(array_key_exists('country',$_REQUEST)?$_REQUEST['country']:0)."',
							`regional`='".intval(array_key_exists('regional',$_REQUEST)?$_REQUEST['regional']:0)."',
							`radius`='".intval(array_key_exists('radius',$_REQUEST)?$_REQUEST['radius']:0)."'
					where `memid`='".intval($_SESSION['profile']['id'])."'";
		} else {
			$query = "insert into `alerts` set `general`='".intval(array_key_exists('general',$_REQUEST)?$_REQUEST['general']:0)."',
							`country`='".intval(array_key_exists('country',$_REQUEST)?$_REQUEST['country']:0)."',
							`regional`='".intval(array_key_exists('regional',$_REQUEST)?$_REQUEST['regional']:0)."',
							`radius`='".intval(array_key_exists('radius',$_REQUEST)?$_REQUEST['radius']:0)."',
							`memid`='".intval($_SESSION['profile']['id'])."'";
		}
		mysql_query($query);
		$id = $oldid;
		$oldid=0;
	}

	if($oldid == 41 && $_REQUEST['action'] == 'default')
	{
		csrf_check("mainlang");
		$lang = mysql_real_escape_string($_REQUEST['lang']);
		foreach(L10n::$translations as $key => $val)
		{
			if($key == $lang)
			{
				mysql_query("update `users` set `language`='$lang' where `id`='".$_SESSION['profile']['id']."'");
				$_SESSION['profile']['language'] = $lang;
				showheader(_("My CAcert.org Account!"));
				echo _("Your language setting has been updated.");
				showfooter();
				exit;
			}
		}

		showheader(_("My CAcert.org Account!"));
		echo _("You tried to use an invalid language.");
		showfooter();
		exit;
	}

	if($oldid == 41 && $_REQUEST['action'] == 'addsec')
	{
		csrf_check("seclang");
		$addlang = mysql_real_escape_string($_REQUEST['addlang']);
		// Does the language exist?
		mysql_query("insert into `addlang` set `userid`='".intval($_SESSION['profile']['id'])."', `lang`='$addlang'");
		showheader(_("My CAcert.org Account!"));
		echo _("Your language setting has been updated.");
		showfooter();
		exit;
	}

	if($oldid == 41 && $_REQUEST['action'] == 'dellang')
	{
		csrf_check("seclang");
		$remove = mysql_real_escape_string($_REQUEST['remove']);
		mysql_query("delete from `addlang` where `userid`='".intval($_SESSION['profile']['id'])."' and `lang`='$remove'");
		showheader(_("My CAcert.org Account!"));
		echo _("Your language setting has been updated.");
		showfooter();
		exit;
	}

	if(($id == 42 || $id == 43 || $id == 44 || $id == 48 || $id == 49 || $id == 50 ||
		$oldid == 42 || $oldid == 43 || $oldid == 44 || $oldid == 48 || $oldid == 49 || $oldid == 50) &&
		$_SESSION['profile']['admin'] != 1)
	{
		showheader(_("My CAcert.org Account!"));
		echo _("You don't have access to this area.");
		showfooter();
		exit;
	}

	if(($id == 53 || $id == 54 || $oldid == 53 || $oldid == 54) &&
		$_SESSION['profile']['locadmin'] != 1)
	{
		showheader(_("My CAcert.org Account!"));
		echo _("You don't have access to this area.");
		showfooter();
		exit;
	}

	if($oldid == 54 || ($id == 53 && array_key_exists('action',$_REQUEST) && $_REQUEST['action'] != "") ||
	             ($id == 54 && array_key_exists('action',$_REQUEST) && $_REQUEST['action'] != "" &&
			$_REQUEST['action'] != "aliases" && $_REQUEST['action'] != "edit" && $_REQUEST['action'] != "add"))
	{
		$id = 53;
		$ccid = intval(array_key_exists('ccid',$_REQUEST)?$_REQUEST['ccid']:0);
		$regid = intval(array_key_exists('regid',$_REQUEST)?$_REQUEST['regid']:0);
		$newreg = intval(array_key_exists('newreg',$_REQUEST)?$_REQUEST['newreg']:0);
		$locid = intval(array_key_exists('locid',$_REQUEST)?$_REQUEST['locid']:0);
		$name = array_key_exists('name',$_REQUEST)?mysql_real_escape_string(strip_tags($_REQUEST['name'])):"";
		$long = array_key_exists('longitude',$_REQUEST)?ereg_replace("[^-0-9\.]","",$_REQUEST['longitude']):"";
		$lat =  array_key_exists('latitude', $_REQUEST)?ereg_replace("[^-0-9\.]","",$_REQUEST['latitude']):"";
		$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:"";

		if($locid > 0 && $action == "edit")
		{
			$query = "update `locations` set `name`='$name', `lat`='$lat', `long`='$long' where `id`='$locid'";
			mysql_query($query);
			$row = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='$locid'"));
			$_REQUEST['regid'] = $row['regid'];
			unset($_REQUEST['ccid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($regid > 0 && $action == "edit") {
			$query = "update `regions` set `name`='$name' where `id`='$regid'";
			mysql_query($query);
			$row = mysql_fetch_assoc(mysql_query("select * from `regions` where `id`='$regid'"));
			$_REQUEST['ccid'] = $row['ccid'];
			unset($_REQUEST['regid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($regid > 0 && $action == "add") {
			$row = mysql_fetch_assoc(mysql_query("select `ccid` from `regions` where `id`='$regid'"));
			$ccid = $row['ccid'];
			$query = "insert into `locations` set `ccid`='$ccid', `regid`='$regid', `name`='$name', `lat`='$lat', `long`='$long'";
			mysql_query($query);
			unset($_REQUEST['ccid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($ccid > 0 && $action == "add" && $name != "") {
			$query = "insert into `regions` set `ccid`='$ccid', `name`='$name'";
			mysql_query($query);
			$row = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='$locid'"));
			unset($_REQUEST['regid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($locid > 0 && $action == "delete") {
			$row = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='$locid'"));
			$_REQUEST['regid'] = $row['regid'];
			mysql_query("delete from `localias` where `locid`='$locid'");
			mysql_query("delete from `locations` where `id`='$locid'");
			unset($_REQUEST['ccid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($locid > 0 && $action == "move") {
			$row = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='$locid'"));
			$oldregid = $row['regid'];
			mysql_query("update `locations` set `regid`='$newreg' where `id`='$locid'");
			mysql_query("update `users` set `regid`='$newreg' where `regid`='$oldregid'");
			$row = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='$locid'"));
			$_REQUEST['regid'] = $row['regid'];
			unset($_REQUEST['ccid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($regid > 0 && $action == "delete") {
			$row = mysql_fetch_assoc(mysql_query("select * from `regions` where `id`='$regid'"));
			$_REQUEST['ccid'] = $row['ccid'];
			mysql_query("delete from `locations` where `regid`='$regid'");
			mysql_query("delete from `regions` where `id`='$regid'");
			unset($_REQUEST['regid']);
			unset($_REQUEST['locid']);
			unset($_REQUEST['action']);
		} else if($locid > 0 && $action == "alias") {
			$id = 54;
			$_REQUEST['action'] = "aliases";
			$_REQUEST['locid'] = $locid;
			$name = htmlentities($name);
			$row = mysql_query("insert into `localias` set `locid`='$locid',`name`='$name'");
		} else if($locid > 0 && $action == "delalias") {
			$id = 54;
			$_REQUEST['action'] = "aliases";
			$_REQUEST['locid'] = $locid;
			$row = mysql_query("delete from `localias` where `locid`='$locid' and `name`='$name'");
		}
	}

	if($oldid == 42 && $_REQUEST['email'] == "")
	{
		$id = $oldid;
		$oldid=0;
	}

	if($oldid == 42)
	{
		$id = 43;
		$oldid=0;
	}

	if($oldid == 43 && $_REQUEST['action'] == "updatedob")
	{
		$id = 43;
		$oldid=0;
		$fname = mysql_real_escape_string($_REQUEST['fname']);
		$mname = mysql_real_escape_string($_REQUEST['mname']);
		$lname = mysql_real_escape_string($_REQUEST['lname']);
		$suffix = mysql_real_escape_string($_REQUEST['suffix']);
		$day = intval($_REQUEST['day']);
		$month = intval($_REQUEST['month']);
		$year = intval($_REQUEST['year']);
		$userid = intval($_REQUEST['userid']);
		$query = "select `fname`,`mname`,`lname`,`suffix`,`dob` from `users` where `id`='$userid'";
		$details = mysql_fetch_assoc(mysql_query($query));
		$query = "insert into `adminlog` set `when`=NOW(),`old-lname`='${details['lname']}',`old-dob`='${details['dob']}',
				`new-lname`='$lname',`new-dob`='$year-$month-$day',`uid`='$userid',`adminid`='".$_SESSION['profile']['id']."'";
		mysql_query($query);
		$query = "update `users` set `fname`='$fname',`mname`='$mname',`lname`='$lname',`suffix`='$suffix',`dob`='$year-$month-$day' where `id`='$userid'";
		mysql_query($query);
	}

	if($oldid == 48 && $_REQUEST['domain'] == "")
	{
		$id = $oldid;
		$oldid=0;
	}

	if($oldid == 48)
	{
		$id = 49;
		$oldid=0;
	}

	if($id == 44)
	{
		if($_REQUEST['userid'] != "")
			$_REQUEST['userid'] = intval($_REQUEST['userid']);
		$row = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($_REQUEST['userid'])."'"));
		if($row['email'] == "")
			$id = 42;
		else
			$_REQUEST['email'] = $row['email'];
	}

	if($oldid == 44)
	{
		showheader(_("My CAcert.org Account!"));
		if(intval($_REQUEST['userid']) <= 0)
		{
			echo _("No such user found.");
		} else {
			mysql_query("update `users` set `password`=sha1('".mysql_real_escape_string(stripslashes($_REQUEST['newpass']))."') where `id`='".intval($_REQUEST['userid'])."'");
			$row = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($_REQUEST['userid'])."'"));
			printf(_("The password for %s has been updated successfully in the system."), sanitizeHTML($row['email']));


			$body  = sprintf(_("Hi %s,"),$row['fname'])."\n\n";
			$body .= _("You are receiving this email because a CAcert administrator ".
					"has changed the password on your account.")."\n\n";

			$body .= _("Best regards")."\n"._("CAcert.org Support!");

			sendmail($row['email'], "[CAcert.org] "._("Password Update Notification"), $body,
						"support@cacert.org", "", "", "CAcert Support");

		}
		showfooter();
		exit;
	}

	if($process != "" && $oldid == 45)
	{
		$CSR = clean_csr($CSR);
		$_SESSION['_config']['CSR'] = $CSR;
		$_SESSION['_config']['subject'] = trim(`echo "$CSR"|/usr/bin/openssl req -text -noout|tr -d "\\0"|grep "Subject:"`);
		$bits = explode(",", trim(`echo "$CSR"|/usr/bin/openssl req -text -noout|tr -d "\\0"|grep -A1 'X509v3 Subject Alternative Name:'|grep DNS:`));
		foreach($bits as $val)
		{
			$_SESSION['_config']['subject'] .= "/subjectAltName=".trim($val);
		}
		$id = 46;

		$_SESSION['_config']['0.CN'] = $_SESSION['_config']['0.subjectAltName'] = "";
		extractit();
		getcn();
		getalt();

		if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
			showfooter();
			exit;
		}
	}

	if($process != "" && $oldid == 46)
	{
		$CSR = clean_csr($_SESSION['_config']['CSR']);
		$_SESSION['_config']['subject'] = trim(`echo "$CSR"|/usr/bin/openssl req -text -noout|tr -d "\\0"|grep "Subject:"`);
		$bits = explode(",", trim(`echo "$CSR"|/usr/bin/openssl req -text -noout|tr -d "\\0"|grep -A1 'X509v3 Subject Alternative Name:'|grep DNS:`));
		foreach($bits as $val)
		{
			$_SESSION['_config']['subject'] .= "/subjectAltName=".trim($val);
		}
		$id = 11;

		$_SESSION['_config']['0.CN'] = $_SESSION['_config']['0.subjectAltName'] = "";
		extractit();
		getcn();
		getalt();

		if($_SESSION['_config']['0.CN'] == "" && $_SESSION['_config']['0.subjectAltName'] == "")
		{
			showheader(_("My CAcert.org Account!"));
			echo _("CommonName field was blank. This is usually caused by entering your own name when openssl prompt's you for 'YOUR NAME', or if you try to issue certificates for domains you haven't already verified, as such this process can't continue.");
			showfooter();
			exit;
		}

		if (($weakKey = checkWeakKeyCSR($CSR)) !== "")
		{
			showheader(_("My CAcert.org Account!"));
			echo $weakKey;
			showfooter();
			exit;
		}

		$query = "insert into `domaincerts` set
						`CN`='".$_SESSION['_config']['0.CN']."',
						`domid`='".$_SESSION['_config']['row']['id']."',
						`created`=NOW()";
		mysql_query($query);
		$CSRid = mysql_insert_id();

		foreach($_SESSION['_config']['rowid'] as $dom)
			mysql_query("insert into `domlink` set `certid`='$CSRid', `domid`='$dom'");
		if(is_array($_SESSION['_config']['altid']))
		foreach($_SESSION['_config']['altid'] as $dom)
			mysql_query("insert into `domlink` set `certid`='$CSRid', `domid`='$dom'");

		$CSRname=generatecertpath("csr","server",$CSRid);
		$fp = fopen($CSRname, "w");
		fputs($fp, $_SESSION['_config']['CSR']);
		fclose($fp);
		mysql_query("update `domaincerts` set `CSR_name`='$CSRname' where `id`='$CSRid'");
		waitForResult("domaincerts", $CSRid,$oldid);
		$query = "select * from `domaincerts` where `id`='$CSRid' and `crt_name` != ''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			printf(_("Your certificate request has failed to be processed correctly, see %sthe WIKI page%s for reasons and solutions."), "<a href='http://wiki.cacert.org/wiki/FAQ/CertificateRenewal'>", "</a>");
			showfooter();
			exit;
		} else {
			$id = 47;
			$cert = $CSRid;
			$_REQUEST['cert']=$CSRid;
		}
	}

	if($id == 43 && array_key_exists('tverify',$_REQUEST) && $_REQUEST['tverify'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['tverify']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['tverify'];
		mysql_query("update `users` set `tverify`='$ver' where `id`='$memid'");
	}

  if($id == 43 && array_key_exists('assurer',$_REQUEST) && $_REQUEST['assurer'] > 0)
  {
    csrf_check('admsetassuret');
    $memid = $_REQUEST['userid'] = intval($_REQUEST['assurer']);
    $query = "select * from `users` where `id`='$memid'";
    $row = mysql_fetch_assoc(mysql_query($query));
    $ver = !$row['assurer'];
    mysql_query("update `users` set `assurer`='$ver' where `id`='$memid'");
  }

  if($id == 43 && array_key_exists('assurer_blocked',$_REQUEST) && $_REQUEST['assurer_blocked'] > 0)
  {
    $memid = $_REQUEST['userid'] = intval($_REQUEST['assurer_blocked']);
    $query = "select * from `users` where `id`='$memid'";
    $row = mysql_fetch_assoc(mysql_query($query));
    $ver = !$row['assurer_blocked'];
    mysql_query("update `users` set `assurer_blocked`='$ver' where `id`='$memid'");
  }

	if($id == 43 && array_key_exists('locked',$_REQUEST) && $_REQUEST['locked'] > 0)
	{
		csrf_check('admactlock');
		$memid = $_REQUEST['userid'] = intval($_REQUEST['locked']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['locked'];
		mysql_query("update `users` set `locked`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('codesign',$_REQUEST) && $_REQUEST['codesign'] > 0)
	{
		csrf_check('admcodesign');
		$memid = $_REQUEST['userid'] = intval($_REQUEST['codesign']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['codesign'];
		mysql_query("update `users` set `codesign`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('orgadmin',$_REQUEST) && $_REQUEST['orgadmin'] > 0)
	{
		csrf_check('admorgadmin');
		$memid = $_REQUEST['userid'] = intval($_REQUEST['orgadmin']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['orgadmin'];
		mysql_query("update `users` set `orgadmin`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('ttpadmin',$_REQUEST) && $_REQUEST['ttpadmin'] > 0)
	{
		csrf_check('admttpadmin');
		$memid = $_REQUEST['userid'] = intval($_REQUEST['ttpadmin']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['ttpadmin'];
		mysql_query("update `users` set `ttpadmin`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('adadmin',$_REQUEST) && $_REQUEST['adadmin'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['adadmin']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = $row['adadmin'] + 1;
		if($ver > 2)
			$ver = 0;
		mysql_query("update `users` set `adadmin`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('locadmin',$_REQUEST) && $_REQUEST['locadmin'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['locadmin']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['locadmin'];
		mysql_query("update `users` set `locadmin`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('admin',$_REQUEST) && $_REQUEST['admin'] > 0)
	{
		csrf_check('admsetadmin');
		$memid = $_REQUEST['userid'] = intval($_REQUEST['admin']);
		$query = "select * from `users` where `id`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['admin'];
		mysql_query("update `users` set `admin`='$ver' where `id`='$memid'");
	}

	if($id == 43 && array_key_exists('general',$_REQUEST) && $_REQUEST['general'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['general']);
		$query = "select * from `alerts` where `memid`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['general'];
		mysql_query("update `alerts` set `general`='$ver' where `memid`='$memid'");
	}

	if($id == 43 && array_key_exists('country',$_REQUEST) && $_REQUEST['country'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['country']);
		$query = "select * from `alerts` where `memid`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['country'];
		mysql_query("update `alerts` set `country`='$ver' where `memid`='$memid'");
	}

	if($id == 43 && array_key_exists('regional',$_REQUEST) && $_REQUEST['regional'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['regional']);
		$query = "select * from `alerts` where `memid`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['regional'];
		mysql_query("update `alerts` set `regional`='$ver' where `memid`='$memid'");
	}

	if($id == 43 && array_key_exists('radius',$_REQUEST) && $_REQUEST['radius'] > 0)
	{
		$memid = $_REQUEST['userid'] = intval($_REQUEST['radius']);
		$query = "select * from `alerts` where `memid`='$memid'";
		$row = mysql_fetch_assoc(mysql_query($query));
		$ver = !$row['radius'];
		mysql_query("update `alerts` set `radius`='$ver' where `memid`='$memid'");
	}

	if($id == 50)
	{
		if(array_key_exists('userid',$_REQUEST) && $_REQUEST['userid'] != "")
			$_REQUEST['userid'] = intval($_REQUEST['userid']);

		$row = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($_REQUEST['userid'])."'"));
		if($row['email'] == "")
			$id = 42;
		else
			$_REQUEST['email'] = $row['email'];
	}

	if($oldid == 50)
	{
		$id = 43;
		$_REQUEST['userid'] = intval($_REQUEST['userid']);
	}

	if($oldid == 50 && $process != "")
	{
		$_REQUEST['userid'] = intval($_REQUEST['userid']);
		$res = mysql_query("select * from `users` where `id`='".intval($_REQUEST['userid'])."'");
		if(mysql_num_rows($res) > 0)
		{
			$query = "update `domaincerts`,`domains` SET `domaincerts`.`revoked`='1970-01-01 10:00:01'
					WHERE `domaincerts`.`domid` = `domains`.`id` AND `domains`.`memid`='".intval($_REQUEST['userid'])."'";
			mysql_query($query);
			$query = "update `domains` SET `deleted`=NOW() WHERE `domains`.`memid`='".intval($_REQUEST['userid'])."'";
			mysql_query($query);
			$query = "update `emailcerts` SET `revoked`='1970-01-01 10:00:01' WHERE `memid`='".intval($_REQUEST['userid'])."'";
			mysql_query($query);
			$query = "update `email` SET `deleted`=NOW() WHERE `memid`='".intval($_REQUEST['userid'])."'";
			mysql_query($query);
			$query = "delete from `org` WHERE `memid`='".intval($_REQUEST['userid'])."'";
			mysql_query($query);
			$query = "update `users` SET `deleted`=NOW() WHERE `id`='".intval($_REQUEST['userid'])."'";
			mysql_query($query);
		}
	}

	if(($id == 51 || $id == 52 || $oldid == 52) && $_SESSION['profile']['tverify'] <= 0)
	{
		showheader(_("My CAcert.org Account!"));
		echo _("You don't have access to this area.");
		showfooter();
		exit;
	}

	if($oldid == 52)
	{
		$uid = intval($_REQUEST['uid']);
		$query = "select * from `tverify` where `id`='$uid' and `modified`=0";
		$rc = mysql_num_rows(mysql_query($query));
		if($rc <= 0)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("Unable to find a valid tverify request for this ID.");
			showfooter();
			exit;
		}
	}

	if($oldid == 52)
	{
		$query = "select * from `tverify-vote` where `tverify`='$uid' and `memid`='".$_SESSION['profile']['id']."'";
		$rc = mysql_num_rows(mysql_query($query));
		if($rc > 0)
		{
			showheader(_("My CAcert.org Account!"));
			echo _("You have already voted on this request.");
			showfooter();
			exit;
		}
	}

	if($oldid == 52 && ($_REQUEST['agree'] != "" || $_REQUEST['disagree'] != ""))
	{
		$vote = -1;
		if($_REQUEST['agree'] != "")
			$vote = 1;

		$query = "insert into `tverify-vote` set
				`tverify`='$uid',
				`memid`='".$_SESSION['profile']['id']."',
				`when`=NOW(), `vote`='$vote',
				`comment`='".mysql_real_escape_string($_REQUEST['comment'])."'";
		mysql_query($query);

		$rc = mysql_num_rows(mysql_query("select * from `tverify-vote` where `tverify`='$uid' and `vote`='1'"));
		if($rc >= 8)
		{
			mysql_query("update `tverify` set `modified`=NOW() where `id`='$uid'");
			$tverify = mysql_fetch_assoc(mysql_query("select * from `tverify` where `id`='$uid'"));
			$memid = $tverify['memid'];
			$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='$memid'"));
			$tmp = mysql_fetch_assoc(mysql_query("select sum(`points`) as `points` from `notary` where `to`='$memid'"));

			$points = 0;
			if($tverify['URL'] != "" && $tverify['photoid'] != "")
				$points = 150 - intval($tmp['points']);
			if($tverify['URL'] != "" && $tverify['photoid'] == "")
				$points = 90 - intval($tmp['points']);
			if($tverify['URL'] == "" && $tverify['photoid'] == "")
				$points = 50 - intval($tmp['points']);

			if($points < 0)
				$points = 0;

			if($points > 0)
			{
				mysql_query("insert into `notary` set `from`='0', `to`='$memid', `points`='$points',
						`method`='Thawte Points Transfer', `when`=NOW()");
				fix_assurer_flag($memid);
			}
			$totalpoints = intval($tmp['points']) + $points;

			$body  = _("Your request to have points transfered was successful. You were issued $points points as a result, and you now have $totalpoints in total")."\n\n"._("The following comments were made by reviewers")."\n\n";
			$res = mysql_query("select * from `tverify-vote` where `tverify`='$uid' and `vote`='1'");
			while($row = mysql_fetch_assoc($res))
				$body .= $row['comment']."\n";
			$body .= "\n";

			$body .= _("Best regards")."\n";
			$body .= _("CAcert Support Team");
			sendmail($user['email'], "[CAcert.org] Thawte Notary Points Transfer", $body, "website-form@cacert.org", "support@cacert.org", "", "CAcert Tverify");
		}

		$rc = mysql_num_rows(mysql_query("select * from `tverify-vote` where `tverify`='$uid' and `vote`='-1'"));
		if($rc >= 4)
		{
			mysql_query("update `tverify` set `modified`=NOW() where `id`='$uid'");
			$tverify = mysql_fetch_assoc(mysql_query("select * from `tverify` where `id`='$uid'"));
			$memid = $tverify['memid'];
			$user = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='$memid'"));

			$body  = _("Unfortunately your request for a points increase has been denied, below is the comments from people that reviewed your request as to why they rejected your application.")."\n\n";
			$res = mysql_query("select * from `tverify-vote` where `tverify`='$uid' and `vote`='-1'");
			while($row = mysql_fetch_assoc($res))
				$body .= $row['comment']."\n";
			$body .= "\n";

			$body .= _("You are welcome to try submitting another request at any time in the future, please make sure you take the reviewer comments into consideration or you risk having your application rejected again.")."\n\n";

			$body .= _("Best regards")."\n";
			$body .= _("CAcert Support Team");
			sendmail($user['email'], "[CAcert.org] Thawte Notary Points Transfer", $body, "website-form@cacert.org", "support@cacert.org", "", "CAcert Tverify");
		}

		showheader(_("My CAcert.org Account!"));
		echo _("Your vote has been accepted.");
		showfooter();
		exit;
	}

	if(intval($cert) > 0)
		$_SESSION['_config']['cert'] = intval($cert);
	if(intval($orgid) > 0)
		$_SESSION['_config']['orgid'] = intval($orgid);
	if(intval($memid) > 0)
		$_SESSION['_config']['memid'] = intval($memid);
?>
