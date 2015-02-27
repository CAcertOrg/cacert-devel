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
	loadem("index");

	$type = array_key_exists('type',$_REQUEST)?$_REQUEST['type']:"";

        if(array_key_exists('Notify',$_REQUEST) && $_REQUEST['Notify'] != "")
        {
                $body = sprintf("An abuse of the CAcert Email Ping system has been reported.\n\n");
                if($type=="email") $body .= "EmailID: ".intval($_REQUEST['emailid'])."\n";
                if($type=="domain") $body .= "DomainID: ".intval($_REQUEST['domainid'])."\n";
                $body .= "Hash: ".sanitizeHTML($_REQUEST['hash'])."\n\n";

		$body .= "Best regards"."\n";
		$body .= "CAcert Website";

		sendmail("support@cacert.org", "[CAcert.org] Verification Abuse", $body, "support@cacert.org", "", "", "");

		showheader(_("Notification"), _("Notification"));
		echo _("Email has been sent.");
		showfooter();
		exit;
        }


	if($type == "email")
	{
		$id = 1;
		$emailid = intval($_REQUEST['emailid']);
		$hash = mysql_real_escape_string(stripslashes($_REQUEST['hash']));

		$query = "select * from `email` where `id`='$emailid' and hash!='' and deleted=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_assoc($res);
			$row['attempts']++;
			if($row['attempts'] >= 6)
			{
				mysql_query("update `email` set `hash`='', `attempts`='$row[attempts]', `deleted`=NOW() where `id`='$emailid'");
				showheader(_("Error!"), _("Error!"));
				echo _("You've attempted to verify the same email address a fourth time with an invalid hash, subsequently this request has been deleted in the system");
				showfooter();
				exit;
			}
			mysql_query("update `email` set `attempts`='$row[attempts]' where `id`='$emailid'");
		}

		$query = "select * from `email` where `id`='$emailid' and `hash`='$hash' and hash!='' and deleted=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("Error!"), _("Error!"));
			echo _("The ID or Hash has already been verified, or something weird happened.");
			showfooter();
			exit;
		}
		$row = mysql_fetch_assoc($res);
		if(array_key_exists('Yes',$_REQUEST) && $_REQUEST['Yes'] != "")
		{
			$query = "update `email` set `hash`='',`modified`=NOW() where `id`='$emailid'";
			mysql_query($query);
			$query = "update `users` set `verified`='1' where `id`='".intval($row['memid'])."' and `email`='".$row['email']."' and `verified`='0'";
			mysql_query($query);
			showheader(_("Updated"), _("Updated"));
			echo _("Your account and/or email address has been verified. You can now start issuing certificates for this address.");
		} else if(array_key_exists('No',$_REQUEST) && $_REQUEST['No'] != "") {
			header("location: /index.php");
			exit;
		} else {
			showheader(_("Updated"), _("Updated"));
			printf(_("Are you sure you want to verify the email %s?"), $row['email']);
			echo "<br>\n<form method='post' action='/verify.php'>";
			echo "<input type='hidden' name='emailid' value='$emailid'>";
			echo "<input type='hidden' name='hash' value='$hash'>";
			echo "<input type='hidden' name='type' value='email'>";
			echo "<input type='submit' name='Yes' value='"._("Yes verify this email")."'><br>\n";
			echo "<input type='submit' name='Notify' value='"._("Notify support about this")."'><br>\n";
			echo "<input type='submit' name='No' value='"._("Do not verify this email")."'></form>\n";
		}
		showfooter();
		exit;
	}
	elseif($type == "domain")
	{
		$id = 7;
		$domainid = intval($_REQUEST['domainid']);
		$hash = mysql_real_escape_string(stripslashes($_REQUEST['hash']));

		$query = "select * from `domains` where `id`='$domainid' and hash!='' and deleted=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_assoc($res);
			$row['attempts']++;
			if($row['attempts'] >= 6)
			{
				$query = "update `domains` set `hash`='', `attempts`='$row[attempts]', `deleted`=NOW() where `id`='$domainid'";
				showheader(_("Error!"), _("Error!"));
				echo _("You've attempted to verify the same domain a fourth time with an invalid hash, subsequantly this request has been deleted in the system");
				showfooter();
				exit;
			}
			$query = "update `domains` set `attempts`='".intval($row['attempts'])."' where `id`='$domainid'";
			mysql_query($query);
		}

		$query = "select * from `domains` where `id`='$domainid' and `hash`='$hash' and hash!='' and deleted=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			showheader(_("Error!"), _("Error!"));
			echo _("The ID or Hash has already been verified, the domain no longer exists in the system, or something weird happened.");
			showfooter();
			exit;
		}
		$row = mysql_fetch_assoc($res);
		if(array_key_exists('Yes',$_REQUEST) && $_REQUEST['Yes'] != "")
		{
			$query = "update `domains` set `hash`='',`modified`=NOW() where `id`='$domainid'";
			mysql_query($query);
			showheader(_("Updated"), _("Updated"));
			echo _("Your domain has been verified. You can now start issuing certificates for this domain.");
		} else if(array_key_exists('No',$_REQUEST) && $_REQUEST['No'] != "") {
			header("location: /index.php");
			exit;
		} else {
			showheader(_("Updated"), _("Updated"));
			printf(_("Are you sure you want to verify the domain %s?"), $row['domain']);
			echo "<br>\n<form method='post' action='/verify.php'>";
			echo "<input type='hidden' name='domainid' value='$domainid'>";
			echo "<input type='hidden' name='hash' value='$hash'>";
			echo "<input type='hidden' name='type' value='domain'>";
			echo "<input type='submit' name='Yes' value='"._("Yes verify this domain")."'><br>\n";
			echo "<input type='submit' name='Notify' value='"._("Notify support about this")."'><br>\n";
			echo "<input type='submit' name='No' value='"._("Do not verify this domain")."'></form>\n";
		}
		showfooter();
		exit;
	}
	else
	{
		showheader(_("Error!"), _("Error!"));
		echo _("Parameters are missing. Please try the complete URL.");
		showfooter();
		exit;
	}
?>
