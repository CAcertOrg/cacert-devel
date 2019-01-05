<?php /*
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
<?php 	include("../includes/account.php");

	if($id == 6)
	{
		include_once("../pages/account/6.php");
		exit;
	} else if($id == 19) {
		include_once("../pages/account/19.php");
		exit;
	} else if($oldid == 40 && $_REQUEST['process'] != "") {
		$who = stripslashes($_REQUEST['who']);
		$email = stripslashes($_REQUEST['email']);
		$subject = stripslashes($_REQUEST['subject']);
		$message = stripslashes($_REQUEST['message']);

		//check for spam via honeypot
		if(!isset($_REQUEST['robotest']) || !empty($_REQUEST['robotest'])){ 
			echo _("Form could not be sent.");
			showfooter();
			exit;
		}

		$message = "From: $who\nEmail: $email\nSubject: $subject\n\nMessage:\n".$message;
		if (isset($process[0])){
			sendmail("cacert-support@lists.cacert.org", "[website form email]: ".$subject, $message, "website-form@cacert.org", "cacert-support@lists.cacert.org, $email", "", "CAcert-Website");
			showheader(_("Welcome to CAcert.org"));
			echo _("Your message has been sent to the general support list.");
			showfooter();
			exit;
		}
		if (isset($process[1])){
			sendmail("support@cacert.org", "[CAcert.org] ".$subject, $message, $email, "", "", "CAcert Support");
			showheader(_("Welcome to CAcert.org"));
			echo _("Your message has been sent.");
			showfooter();
			exit;
		}

	} else if($id == 51 && $_GET['img'] == "show") {
		$query = "select * from `tverify` where `id`='".intval($_GET['photoid'])."' and `modified`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res))
		{
			$row = mysql_fetch_assoc($res);
			readfile($row['photoid']);
		} else {
			die("No such file.");
		}
		exit;
	} else if ($id == 37) {
		$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
		$newUrl = $protocol . '://wiki.cacert.org/FAQ/AboutUs';
		header('Location: '.$newUrl, true, 301); // 301 = Permanently Moved    	
	} else {
		showheader(_("My CAcert.org Account!"));
		includeit($id, "account");
		showfooter();
		exit;
	}
?>
