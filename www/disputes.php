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

	loadem("account");

        $type=""; if(array_key_exists('type',$_REQUEST)) $type=$_REQUEST['type'];
        $action=""; if(array_key_exists('action',$_REQUEST)) $action=sanitizeHTML($_REQUEST['action']);

	if($type == "reallyemail")
	{
		$emailid = intval($_SESSION['_config']['emailid']);
		$hash = mysql_real_escape_string(trim($_SESSION['_config']['hash']));

		$res = mysql_query("select * from `disputeemail` where `id`='$emailid' and `hash`='$hash'");
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("Email Dispute"));
			echo _("This dispute no longer seems to be in the database, can't continue.");
			showfooter();
			exit;
		}
		$row = mysql_fetch_assoc($res);
		$oldmemid = $row['oldmemid'];

		if($action == "reject")
		{
			mysql_query("update `disputeemail` set hash='',action='reject' where `id`='".intval($emailid)."'");
			showheader(_("Email Dispute"));
			echo _("You have opted to reject this dispute and the request will be removed from the database");
			showfooter();
			exit;
		}
		if($action == "accept")
		{
			showheader(_("Email Dispute"));
			echo "<p>"._("You have opted to accept this dispute and the request will now remove this email address from the existing account, and revoke any current certificates.")."</p>";
			echo "<p>"._("The following accounts have been removed:")."<br>\n";
			$query = "select * from `email` where `id`='".intval($emailid)."' and deleted=0";
			$res = mysql_query($query);
			if(mysql_num_rows($res) > 0)
			{
				$row = mysql_fetch_assoc($res);
				echo $row['email']."<br>\n";
				account_email_delete($row['id']);
			}
			mysql_query("update `disputeemail` set hash='',action='accept' where `id`='$emailid'");
			$rc = mysql_num_rows(mysql_query("select * from `domains` where `memid`='$oldmemid' and `deleted`=0"));
			$rc2 = mysql_num_rows(mysql_query("select * from `email` where `memid`='$oldmemid' and `deleted`=0 and `id`!='$emailid'"));
			$res = mysql_query("select * from `users` where `id`='$oldmemid'");
			$user = mysql_fetch_assoc($res);
			if($rc == 0 && $rc2 == 0 && $_SESSION['_config']['email'] == $user['email'])
			{
				mysql_query("update `users` set `deleted`=NOW() where `id`='$oldmemid'");
				echo _("This was the primary email on the account, and no emails or domains were left linked so the account has also been removed from the system.");
			}

			showfooter();
			exit;
		}
	}

	if($type == "email")
	{
		$emailid = intval($_REQUEST['emailid']);
		$hash = trim(mysql_real_escape_string(stripslashes($_REQUEST['hash'])));
		if($emailid <= 0 || $hash == "")
		{
			showheader(_("Email Dispute"));
			echo _("Invalid request. Can't continue.");
			showfooter();
			exit;
		}

		$res = mysql_query("select * from `disputeemail` where `id`='$emailid' and `hash`='$hash'");
		if(mysql_num_rows($res) <= 0)
		{
			$res = mysql_query("select * from `disputeemail` where `id`='$emailid' and hash!=''");
			if(mysql_num_rows($res) > 0)
			{
				$row = mysql_fetch_assoc($res);
				mysql_query("update `disputeemail` set `attempts`='".intval($row['attempts'] + 1)."' where `id`='".$row['id']."'");
				showheader(_("Email Dispute"));
				if($row['attempts'] >= 3)
				{
					echo _("Your attempt to accept or reject a disputed email is invalid due to the hash string not matching with the email ID. Your attempt has been logged and the request will be removed from the system as a result.");
					mysql_query("update `disputeemail` set hash='',action='failed' where `id`='$emailid'");
				} else
					echo _("Your attempt to accept or reject a disputed email is invalid due to the hash string not matching with the email ID.");
				showfooter();
				exit;
			} else {
				showheader(_("Email Dispute"));
				echo _("Invalid request. Can't continue.");
				showfooter();
				exit;
			}
		}
		$_SESSION['_config']['emailid'] = $emailid;
		$_SESSION['_config']['hash'] = $hash;
		$row = mysql_fetch_assoc(mysql_query("select * from `disputeemail` where `id`='$emailid'"));
		$_SESSION['_config']['email'] = $row['email'];
		showheader(_("Email Dispute"));
		includeit("4", "disputes");
		showfooter();
		exit;
	}

	if($type == "reallydomain")
	{
		$domainid = intval($_SESSION['_config']['domainid']);
		$hash = mysql_real_escape_string(trim($_SESSION['_config']['hash']));

		$res = mysql_query("select * from `disputedomain` where `id`='$domainid' and `hash`='$hash'");
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("Domain Dispute"));
			echo _("This dispute no longer seems to be in the database, can't continue.");
			showfooter();
			exit;
		}

		if($action == "reject")
		{
			mysql_query("update `disputedomain` set hash='',action='reject' where `id`='$domainid'");
			showheader(_("Domain Dispute"));
			echo _("You have opted to reject this dispute and the request will be removed from the database");
			showfooter();
			exit;
		}
		if($action == "accept")
		{
			showheader(_("Domain Dispute"));
			echo "<p>"._("You have opted to accept this dispute and the request will now remove this domain from the existing account, and revoke any current certificates.")."</p>";
			echo "<p>"._("The following accounts have been removed:")."<br>\n";
			//new account_domain_delete($domainid, $memberID)
			$query = "select * from `domains` where `id`='$domainid' and deleted=0";
			$res = mysql_query($query);
			if(mysql_num_rows($res) > 0)
			{
				echo $_SESSION['_config']['domain']."<br>\n";
				account_domain_delete($domainid);
			}
			mysql_query("update `disputedomain` set hash='',action='accept' where `id`='$domainid'");
			showfooter();
			exit;
		}
	}

	if($type == "domain")
	{
		$domainid = intval($_REQUEST['domainid']);
		$hash = trim(mysql_real_escape_string(stripslashes($_REQUEST['hash'])));
		if($domainid <= 0 || $hash == "")
		{
			showheader(_("Domain Dispute"));
			echo _("Invalid request. Can't continue.");
			showfooter();
			exit;
		}

		$res = mysql_query("select * from `disputedomain` where `id`='$domainid' and `hash`='$hash'");
		if(mysql_num_rows($res) <= 0)
		{
			$res = mysql_query("select * from `disputedomain` where `id`='$domainid' and hash!=''");
			if(mysql_num_rows($res) > 0)
			{
				$row = mysql_fetch_assoc($res);
				mysql_query("update `disputedomain` set `attempts`='".intval($row['attempts'] + 1)."' where `id`='".$row['id']."'");
				showheader(_("Domain Dispute"));
				if($row['attempts'] >= 3)
				{
					echo _("Your attempt to accept or reject a disputed domain is invalid due to the hash string not matching with the domain ID. Your attempt has been logged and the request will be removed from the system as a result.");
					mysql_query("update `disputedomain` set hash='',action='failed' where `id`='$domainid'");
				} else
					echo _("Your attempt to accept or reject a disputed domain is invalid due to the hash string not matching with the domain ID.");
				showfooter();
				exit;
			} else {
				showheader(_("Domain Dispute"));
				echo _("Invalid request. Can't continue.");
				showfooter();
				exit;
			}
		}
		$_SESSION['_config']['domainid'] = $domainid;
		$_SESSION['_config']['hash'] = $hash;
		$row = mysql_fetch_assoc(mysql_query("select * from `disputedomain` where `id`='$domainid'"));
		$_SESSION['_config']['domain'] = $row['domain'];
		showheader(_("Domain Dispute"));
		includeit("6", "disputes");
		showfooter();
		exit;
	}

	if($oldid == "1")
	{
		csrf_check('emaildispute');
		$email = trim(mysql_real_escape_string(stripslashes($_REQUEST['dispute'])));
		if($email == "")
		{
			showheader(_("Email Dispute"));
			echo _("Not a valid email address. Can't continue.");
			showfooter();
			exit;
		}

		//check if email belongs to locked account
		$res = mysql_query("select 1 from `email`, `users` where `email`.`email`='$email' and `email`.`memid`=`users`.`id` and (`users`.`assurer_blocked`=1 or `users`.`locked`=1)");
		if(mysql_num_rows($res) > 0)
		{
			showheader(_("Email Dispute"));
			printf(_("Sorry, the email address '%s' cannot be disputed for administrative reasons. To solve this problem please get in contact with %s."), sanitizeHTML($email),"<a href='mailto:support@cacert.org'>support@cacert.org</a>");
			$duser=$_SESSION['profile']['fname']." ".$_SESSION['profile']['lname'];
			$body = sprintf("Someone has just attempted to dispute this email '%s', which belongs to a locked account:\n".
				"Username(ID): %s (%s)\n".
				"email: %s\n".
				"IP/Hostname: %s\n", $email, $duser, $_SESSION['profile']['id'], $_SESSION['profile']['email'], $_SERVER['REMOTE_ADDR'].(array_key_exists('REMOTE_HOST',$_SERVER)?"/".$_SERVER['REMOTE_HOST']:""));
			sendmail("support@cacert.org", "[CAcert.org] failed dispute on locked account", $body, $_SESSION['profile']['email'], "", "", $duser);

			showfooter();
			exit;
		}

		$res = mysql_query("select * from `disputeemail` where `email`='$email' and hash!=''");
		if(mysql_num_rows($res) > 0)
		{
			showheader(_("Email Dispute"));
			printf(_("The email address '%s' already exists in the dispute system. Can't continue."), sanitizeHTML($email));
			showfooter();
			exit;
		}

		unset($oldid);
		$query = "select * from `email` where `email`='$email' and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("Email Dispute"));
			printf(_("The email address '%s' doesn't exist in the system. Can't continue."), sanitizeHTML($email));
			showfooter();
			exit;
		}
		$row = mysql_fetch_assoc($res);
		$oldmemid = $row['memid'];
		$emailid = $row['id'];
		if($_SESSION['profile']['id'] == $oldmemid)
		{
			showheader(_("Email Dispute"));
			echo _("You aren't allowed to dispute your own email addresses. Can't continue.");
			showfooter();
			exit;
		}

		$res = mysql_query("select * from `users` where `id`='$oldmemid'");
		$user = mysql_fetch_assoc($res);
		$rc = mysql_num_rows(mysql_query("select * from `domains` where `memid`='$oldmemid' and `deleted`=0"));
		$rc2 = mysql_num_rows(mysql_query("select * from `email` where `memid`='$oldmemid' and `deleted`=0 and `id`!='$emailid'"));
		if($user['email'] == $email && ($rc > 0 || $rc2 > 0))
		{
			showheader(_("Email Dispute"));
			echo _("You only dispute the primary email address of an account if there is no longer any email addresses or domains linked to it.");
			showfooter();
			exit;
		}

		$hash = make_hash();
		$query = "insert into `disputeemail` set `email`='$email',`memid`='".intval($_SESSION['profile']['id'])."',
				`oldmemid`='$oldmemid',`created`=NOW(),`hash`='$hash',`id`='".intval($emailid)."',
				`IP`='".$_SERVER['REMOTE_ADDR']."'";
		mysql_query($query);

		$my_translation = L10n::get_translation();
		L10n::set_recipient_language($oldmemid);

		$body = sprintf(_("You have been sent this email as the email address '%s' is being disputed. You have the option to accept or reject this request, after 2 days the request will automatically be discarded. Click the following link to accept or reject the dispute:"), $email)."\n\n";
		$body .= "https://".$_SESSION['_config']['normalhostname']."/disputes.php?type=email&emailid=$emailid&hash=$hash\n\n";
		$body .= _("Best regards")."\n"._("CAcert.org Support!");

		sendmail($email, "[CAcert.org] "._("Dispute Probe"), $body, "support@cacert.org", "", "", "CAcert Support");
		L10n::set_translation($my_translation);

		showheader(_("Email Dispute"));
		printf(_("The email address '%s' has been entered into the dispute system, the email address will now be sent an email which will give the recipent the option of accepting or rejecting the request, if after 2 days we haven't received a valid response for or against we will discard the request."), sanitizeHTML($email));
		showfooter();
		exit;
	}

	if($oldid == "2")
	{
		csrf_check('domaindispute');
		$domain = trim(mysql_real_escape_string(stripslashes($_REQUEST['dispute'])));
		if($domain == "")
		{
			showheader(_("Domain Dispute"));
			echo _("Not a valid Domain. Can't continue.");
			showfooter();
			exit;
		}

		//check if domain belongs to locked account
		$res = mysql_query("select 1 from `domains`, `users` where `domains`.`domain`='$domain' and `domains`.`memid`=`users`.`id` and (`users`.`assurer_blocked`=1 or `users`.`locked`=1)");
		if(mysql_num_rows($res) > 0)
		{
			showheader(_("Domain Dispute"));
			printf(_("Sorry, the domain '%s' cannot be disputed for administrative reasons. To solve this problem please get in contact with %s."), sanitizeHTML($domain),"<a href='mailto:support@cacert.org'>support@cacert.org</a>");
			$duser=$_SESSION['profile']['fname']." ".$_SESSION['profile']['lname'];
			$body = sprintf("Someone has just attempted to dispute this domain '%s', which belongs to a locked account:\n".
				"Username(ID): %s (%s)\n".
				"email: %s\n".
				"IP/Hostname: %s\n", $domain, $duser, $_SESSION['profile']['id'], $_SESSION['profile']['email'], $_SERVER['REMOTE_ADDR'].(array_key_exists('REMOTE_HOST',$_SERVER)?"/".$_SERVER['REMOTE_HOST']:""));
			sendmail("support@cacert.org", "[CAcert.org] failed dispute on locked account", $body, $_SESSION['profile']['email'], "", "", $duser);

			showfooter();
			exit;
		}

		$query = "select * from `disputedomain` where `domain`='$domain' and hash!=''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			showheader(_("Domain Dispute"));
			printf(_("The domain '%s' already exists in the dispute system. Can't continue."), sanitizeHTML($domain));
			showfooter();
			exit;
		}
		unset($oldid);
		$query = "select * from `domains` where `domain`='$domain' and `deleted`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			$query = "select 1 from `orgdomains` where `domain`='$domain'";
			$res = mysql_query($query);
			if(mysql_num_rows($res) > 0)
			{
				showheader(_("Domain Dispute"));
				printf(_("The domain '%s' is included in an organisation account. Please send a mail to %s to dispute this domain."), sanitizeHTML($domain),'<a href="mailto:support@cacert.org">support@cacert.org</a>');
				showfooter();
				exit;
			}
			showheader(_("Domain Dispute"));
			printf(_("The domain '%s' doesn't exist in the system. Can't continue."), sanitizeHTML($domain));
			showfooter();
			exit;
		}
		$row = mysql_fetch_assoc($res);
		$oldmemid = $row['memid'];
		if($_SESSION['profile']['id'] == $oldmemid)
		{
			showheader(_("Domain Dispute"));
			echo _("You aren't allowed to dispute your own domains. Can't continue.");
			showfooter();
			exit;
		}

		$domainid = $row['id'];
		$_SESSION['_config']['domainid'] = $domainid;
		$_SESSION['_config']['memid'] = array_key_exists('memid',$_REQUEST)?intval($_REQUEST['memid']):0;
		$_SESSION['_config']['domain'] = $domain;
		$_SESSION['_config']['oldmemid'] = $oldmemid;

                $addy = array();
		$domtmp = escapeshellarg($domain);
		if(strtolower(substr($domtmp, -4, 3)) != ".jp")
	                $adds = explode("\n", trim(`whois $domtmp|grep \@`));
                if(substr($domain, -4) == ".org" || substr($domain, -5) == ".info")
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

                $rfc = array("root@$domain", "hostmaster@$domain", "postmaster@$domain", "admin@$domain", "webmaster@$domain");
                foreach($rfc as $sub)
                        if(!in_array($sub, $addy))
                                $addy[] = $sub;
                $_SESSION['_config']['addy'] = $addy;
		showheader(_("Domain Dispute"));
		includeit("5", "disputes");
		showfooter();
		exit;
	}

	if($oldid == "5")
	{
                $authaddy = trim(mysql_real_escape_string(stripslashes($_REQUEST['authaddy'])));

                if(!in_array($authaddy, $_SESSION['_config']['addy']) || $authaddy == "")
                {
                        showheader(_("My CAcert.org Account!"));
                        echo _("The address you submitted isn't a valid authority address for the domain.");
                        showfooter();
                        exit;
                }

                $query = "select * from `domains` where `domain`='".$_SESSION['_config']['domain']."' and `deleted`=0";
                $res = mysql_query($query);
                if(mysql_num_rows($res) <= 0)
                {
                        showheader(_("Domain Dispute!"));
                        printf(_("The domain '%s' isn't in the system. Can't continue."), sanitizeHTML($_SESSION['_config']['domain']));
                        showfooter();
                        exit;
                }

		$domainid = intval($_SESSION['_config']['domainid']);
		$memid = intval($_SESSION['_config']['memid']);
		$oldmemid = intval($_SESSION['_config']['oldmemid']);
		$domain = mysql_real_escape_string($_SESSION['_config']['domain']);

		$hash = make_hash();
		$query = "insert into `disputedomain` set `domain`='$domain',`memid`='".$_SESSION['profile']['id']."',
				`oldmemid`='$oldmemid',`created`=NOW(),`hash`='$hash',`id`='$domainid'";
		mysql_query($query);
		$my_translation = L10n::get_translation();
		L10n::set_recipient_language($oldmemid);

		$body = sprintf(_("You have been sent this email as the domain '%s' is being disputed. You have the option to accept or reject this request, after 2 days the request will automatically be discarded. Click the following link to accept or reject the dispute:"), $domain)."\n\n";
		$body .= "https://".$_SESSION['_config']['normalhostname']."/disputes.php?type=domain&domainid=$domainid&hash=$hash\n\n";
		$body .= _("Best regards")."\n"._("CAcert.org Support!");
		L10n::set_recipient_language($my_translation);

		sendmail($authaddy, "[CAcert.org] "._("Dispute Probe"), $body, "support@cacert.org", "", "", "CAcert Support");

		showheader(_("Domain Dispute"));
		printf(_("The domain '%s' has been entered into the dispute system, the email address you choose will now be sent an email which will give the recipent the option of accepting or rejecting the request, if after 2 days we haven't received a valid response for or against we will discard the request."), sanitizeHTML($domain));
		showfooter();
		exit;
	}

	showheader(_("Domain and Email Disputes"));
	includeit($id, "disputes");
	showfooter();
?>
