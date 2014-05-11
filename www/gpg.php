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
	require_once("../includes/lib/general.php");
	require_once('../includes/notary.inc.php');

        $id = 0; if(array_key_exists('id',$_REQUEST)) $id=intval($_REQUEST['id']);
	$oldid = $_REQUEST['oldid'] = array_key_exists('oldid',$_REQUEST) ? intval($_REQUEST['oldid']) : 0;

	if($_SESSION['profile']['points'] < 50)
	{
		header("location: /account.php");
		exit;
	}

	loadem("account");



	$CSR=""; if(array_key_exists('CSR',$_REQUEST)) $CSR=stripslashes($_REQUEST['CSR']);


	if($oldid == "0")
	{
		if(array_key_exists('process',$_REQUEST) && $_REQUEST['process'] != "" && $CSR == "")
		{
			$_SESSION['_config']['errmsg'] = _("You failed to paste a valid GPG/PGP key.");
			$id = $oldid;
			$oldid=0;
		}
	}

	$keyid="";

if(0)
{
  if($_SESSION["profile"]["id"] != 5897)
  {
    showheader(_("Welcome to CAcert.org"));
    echo "The OpenPGP signing system is currently shutdown due to a maintenance. We hope to get it fixed within the next few hours. We are very sorry for the inconvenience.";

    exit(0);
  }
}


function verifyName($name)
{
	if($name == "") return 0;

	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['lname'])) return 1; // John Doe
	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname']." ".$_SESSION['profile']['lname'])) return 1; // John Joseph Doe
	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname'][0]." ".$_SESSION['profile']['lname'])) return 1; // John J Doe
	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname'][0].". ".$_SESSION['profile']['lname'])) return 1; // John J. Doe

	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['lname']." ".$_SESSION['profile']['suffix'])) return 1; // John Doe Jr.
	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname']." ".$_SESSION['profile']['lname']." ".$_SESSION['profile']['suffix'])) return 1; //John Joseph Doe Jr.
	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname'][0]." ".$_SESSION['profile']['lname']." ".$_SESSION['profile']['suffix'])) return 1; //John J Doe Jr.
	if(!strcasecmp($name, $_SESSION['profile']['fname']." ".$_SESSION['profile']['mname'][0].". ".$_SESSION['profile']['lname']." ".$_SESSION['profile']['suffix'])) return 1; //John J. Doe Jr.

	return 0;
}

function verifyEmail($email)
{
	if($email == "") return 0;
	if(mysql_num_rows(mysql_query("select * from `email` where `memid`='".$_SESSION['profile']['id']."' and `email`='".mysql_real_escape_string($email)."' and `deleted`=0 and `hash`=''")) > 0) return 1;
	return 0;
}



	$ToBeDeleted=array();
	$state=0;
	if($oldid == "0" && $CSR != "")
	{
		if(!array_key_exists('CCA',$_REQUEST))
		{
			showheader(_("My CAcert.org Account!"));
			echo _("You did not accept the CAcert Community Agreement (CCA), hit the back button and try again.");
			showfooter();
			exit;
		}

		$err = runCommand('mktemp --directory /tmp/cacert_gpg.XXXXXXXXXX',
				"",
				$tmpdir);
		if (!$tmpdir)
		{
			$err = true;
		}

		if (!$err)
		{
			$err = runCommand("gpg --with-colons --homedir $tmpdir 2>&1",
					clean_gpgcsr($CSR),
					$gpg);

			`rm -r $tmpdir`;
		}

		if ($err)
		{
			showheader(_("Welcome to CAcert.org"));

			echo "<p style='color:#ff0000'>"._("There was an error parsing your key.")."</p>";
			unset($_REQUEST['process']);
			$id = $oldid;
			unset($oldid);
			exit();
		}

		$lines = "";
		$gpgarr = explode("\n", trim($gpg));
		foreach($gpgarr as $line)
		{
			#echo "Line[]: $line <br/>\n";
			if(substr($line, 0, 3) == "pub" || substr($line, 0, 3) == "uid")
			{
				if($lines != "")
					$lines .= "\n";
				$lines .= $line;
			}
		}
		$gpg = $lines;
		$expires = 0;
		$nerr=0; $nok=0;
		$multiple = 0;

		$resulttable=_("The following UIDs were found in your key:")."<br/><table border='1'><tr><td>#</td><td>"._("Name")."</td><td>"._("Email")."</td><td>Result</td>";
		$i=0;
		$lastvalidemail="";
                $npubs=0;
		foreach(explode("\n", $gpg) as $line)
		{
			$bits = explode(":", $line);
			$resulttable.="<tr><td>".++$i."</td>";
			$name = $comment = "";
			if($bits[0] == "pub")
			{
				$npubs++;
			}
			if($npubs>1)
			{
				showheader(_("Welcome to CAcert.org"));
				echo "<font color='#ff0000'>"._("Please upload only one key at a time.")."</font>";
				unset($_REQUEST['process']);
				$id = $oldid;
				unset($oldid);
				exit();
			}
			if($bits[0] == "pub" && (!$keyid || !$when))
			{
				$keyid = $bits[4];
				$when = $bits[5];
				if($bits[6] != "")
					$expires = 1;
			}
			$name="";
			$comm="";
			$mail="";
			$uidformatwrong=0;

			if(sizeof($bits)<10) $uidformatwrong=1;

			if(preg_match("/\@.*\@/",$bits[9]))
			{
				showheader(_("Welcome to CAcert.org"));

				echo "<font color='#ff0000'>"._("Multiple Email Adresses per UID are not allowed.")."</font>";
				unset($_REQUEST['process']);
				$id = $oldid;
				unset($oldid);
				exit();
			}

			// Name (Comment) <Email>
			if(preg_match("/^([^\(\)\[@<>]+) \(([^\(\)@<>]*)\) <([\w=\/%.-]*\@[\w.-]*|[\w.-]*\![\w=\/%.-]*)>/",$bits[9],$matches))
			{
			  $name=trim(gpg_hex2bin($matches[1]));
			  $nocomment=0;
			  $comm=trim(gpg_hex2bin($matches[2]));
			  $mail=trim(gpg_hex2bin($matches[3]));
			}
			// Name <EMail>
			elseif(preg_match("/^([^\(\)\[@<>]+) <([\w=\/%.-]*\@[\w.-]*|[\w.-]*\![\w=\/%.-]*)>/",$bits[9],$matches))
			{
			  $name=trim(gpg_hex2bin($matches[1]));
			  $nocomment=1;
			  $comm="";
			  $mail=trim(gpg_hex2bin($matches[2]));
			}
			// Unrecognized format
			else
			{
				$nocomment=1;
				$uidformatwrong=1;
			}
  		  	$nameok=verifyName($name);
			$emailok=verifyEmail($mail);


			if($comm != "")
				$comment[] = $comm;

			$resulttable.="<td bgcolor='#".($nameok?"c0ffc0":"ffc0c0")."'>".sanitizeHTML($name)."</td>";
                        $resulttable.="<td bgcolor='#".($emailok?"c0ffc0":"ffc0c0")."'>".sanitizeHTML($mail)."</td>";

			$uidok=0;
			if($bits[1]=="r")
			{
				$rmessage=_("Error: UID is revoked");
			}
			elseif($uidformatwrong==1)
			{
				$rmessage=_("The format of the UID was not recognized. Please use 'Name (comment) &lt;email@domain>'");
			}
			elseif($mail=="" and $name=="")
			{
				$rmessage=_("Error: Both Name and Email address are empty");
			}
			elseif($emailok and $nameok)
			{
				$uidok=1;
				$rmessage=_("Name and Email OK.");
			}
			elseif(!$emailok and !$nameok)
			{
				$rmessage=_("Name and Email both cannot be matched with your account.");
			}
			elseif($emailok and $name=="")
			{
				$uidok=1;
				$rmessage=_("The email is OK. The name is empty.");
			}
			elseif($nameok and $mail=="")
			{
				$uidok=1;
				$rmessage=_("The name is OK. The email is empty.");
			}
			elseif(!$emailok)
			{
				$rmessage=_("The email address has not been registered and verified in your account. Please add the email address to your account first.");
			}
			elseif(!$nameok)
			{
				$rmessage=_("The name in the UID does not match the name in your account. Please verify the name.");
			}

			else
			{
				$rmessage=_("Error");
			}
			if($uidok)
			{
				$nok++;
				$resulttable.="<td>$rmessage</td>";
				$lastvalidemail=$mail;
			}
			else
			{
				$nerr++;
				//$ToBeDeleted[]=$i;
				//echo "Adding UID $i\n";
				$resulttable.="<td bgcolor='#ffc0c0'>$rmessage</td>";
			}
			$resulttable.="</tr>\n";

			if($emailok) $multiple++;
		}
		$resulttable.="</table>";

		if($nok==0)
		{
			showheader(_("Welcome to CAcert.org"));
			echo $resulttable;

			echo "<font color='#ff0000'>"._("No valid UIDs found on your key")."</font>";
			unset($_REQUEST['process']);
			$id = $oldid;
			unset($oldid);
			exit();
		}
		elseif($nerr)
		{
			$resulttable.=_("The unverified UIDs have been removed, the verified UIDs have been signed.");
		}


 	}


	if($oldid == "0" && $CSR != "")
	{
		write_user_agreement(intval($_SESSION['profile']['id']), "CCA", "certificate creation", "", 1);

		//set variable for comment
		if(trim($_REQUEST['description']) == ""){
			$description= "";
		}else{
			$description= trim(mysql_real_escape_string(stripslashes($_REQUEST['description'])));
		}

		$query = "insert into `gpg` set `memid`='".intval($_SESSION['profile']['id'])."',
						`email`='".mysql_real_escape_string($lastvalidemail)."',
						`level`='1',
						`expires`='".mysql_real_escape_string($expires)."',
						`multiple`='".mysql_real_escape_string($multiple)."',
						`keyid`='".mysql_real_escape_string($keyid)."',
						`description`='".mysql_real_escape_string($description)."'";
		mysql_query($query);
		$insert_id = mysql_insert_id();


		$cwd = '/tmp/gpgspace'.$insert_id;
		mkdir($cwd,0755);

		$fp = fopen("$cwd/gpg.csr", "w");
		fputs($fp, clean_gpgcsr($CSR));
		fclose($fp);


		system("gpg --homedir $cwd --import $cwd/gpg.csr");


		$cmd_keyid = escapeshellarg($keyid);
		$gpg = trim(`gpg --homedir $cwd --with-colons --fixed-list-mode --list-keys $cmd_keyid 2>&1`);
		$lines = "";
		$gpgarr = explode("\n", $gpg);
		foreach($gpgarr as $line)
		{
			//echo "Line[]: $line <br/>\n";
			if(substr($line, 0, 4) == "uid:")
			{
				$name = $comment = "";
				$bits = explode(":", $line);

				$pos = strpos($bits[9], "(") - 1;
				$nocomment = 0;
				if($pos < 0)
				{
					$nocomment = 1;
					$pos = strpos($bits[9], "<") - 1;
				}
				if($pos < 0)
				{
					$pos = strlen($bits[9]);
				}

				$name = trim(gpg_hex2bin(trim(substr($bits[9], 0, $pos))));
				$nameok=verifyName($name);
				if($nocomment == 0)
				{
					$pos += 2;
					$pos2 = strpos($bits[9], ")");
					$comm = trim(gpg_hex2bin(trim(substr($bits[9], $pos, $pos2 - $pos))));
					if($comm != "")
						$comment[] = $comm;
					$pos = $pos2 + 3;
				} else {
					$pos = strpos($bits[9], "<") + 1;
				}

				$mail="";
				if (preg_match("/<([\w.-]*\@[\w.-]*)>/", $bits[9],$match)) {
					//echo "Found: ".$match[1];
					$mail = trim(gpg_hex2bin($match[1]));
				}
				else
				{
					//echo "Not found!\n";
				}

				$emailok=verifyEmail($mail);

				$uidid=$bits[7];

			if($bits[1]=="r")
			{
				$ToBeDeleted[]=$uidid;
			}
			elseif($mail=="" and $name=="")
			{
				//echo "$uidid will be deleted\n";
				$ToBeDeleted[]=$uidid;
			}
			elseif($emailok and $nameok)
			{
			}
			elseif($emailok and $name=="")
			{
			}
			elseif($nameok and $mail=="")
			{
			}
			elseif(!$emailok and !$nameok)
			{
				//echo "$uidid will be deleted\n";
				$ToBeDeleted[]=$uidid;
			}
			elseif(!$emailok)
			{
				//echo "$uidid will be deleted\n";
				$ToBeDeleted[]=$uidid;
			}
			elseif(!$nameok)
			{
				//echo "$uidid will be deleted\n";
				$ToBeDeleted[]=$uidid;
			}

			}
		}

		if(count($ToBeDeleted)>0)
		{
			$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array("pipe", "w") // stderr is a file to write to
			);

			$stderr = fopen('php://stderr', 'w');

			//echo "Keyid: $keyid\n";

			$cmd_keyid = escapeshellarg($keyid);
			$process = proc_open("/usr/bin/gpg --homedir $cwd --no-tty --command-fd 0 --status-fd 1 --logger-fd 2 --edit-key $cmd_keyid", $descriptorspec, $pipes);

			//echo "Process: $process\n";
			//fputs($stderr,"Process: $process\n");

			if (is_resource($process)) {
			//echo("it is a resource\n");
			// $pipes now looks like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout
			// Any error output will be appended to /tmp/error-output.txt
				while (!feof($pipes[1]))
				{
					$buffer = fgets($pipes[1], 4096);
					//echo $buffer;

			if($buffer == "[GNUPG:] GET_BOOL keyedit.sign_all.okay\n")
			{
				fputs($pipes[0],"yes\n");
			}
			elseif($buffer == "[GNUPG:] GOT_IT\n")
			{
			}
			elseif(ereg("^\[GNUPG:\] GET_BOOL keyedit\.remove\.uid\.okay\s*",$buffer))
			{
				fputs($pipes[0],"yes\n");
			}
			elseif(ereg("^\[GNUPG:\] GET_LINE keyedit\.prompt\s*",$buffer))
			{
				if(count($ToBeDeleted)>0)
				{
					$delthisuid=array_pop($ToBeDeleted);
					//echo "Deleting an UID $delthisuid\n";
					fputs($pipes[0],"uid ".$delthisuid."\n");
				}
				else
				{
					//echo "Saving\n";
					fputs($pipes[0],$state?"save\n":"deluid\n");
					$state++;
				}
			}
			elseif($buffer == "[GNUPG:] GOOD_PASSPHRASE\n")
			{
			}
			elseif(ereg("^\[GNUPG:\] KEYEXPIRED ",$buffer))
			{
				echo "Key expired!\n";
				exit;
			}
			elseif($buffer == "")
			{
				//echo "Empty!\n";
			}
			else
			{
				echo "ERROR: UNKNOWN $buffer\n";
			}


			}
			//echo "Fertig\n";
			fclose($pipes[0]);

			//echo stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			// It is important that you close any pipes before calling
			// proc_close in order to avoid a deadlock
			$return_value = proc_close($process);

			//echo "command returned $return_value\n";
		}
		else
		{
			echo "Keine ressource!\n";
		}


		}


		$csrname=generatecertpath("csr","gpg",$insert_id);
		$cmd_keyid = escapeshellarg($keyid);
		$do=`gpg --homedir $cwd --batch --export-options export-minimal --export $cmd_keyid >$csrname`;

		mysql_query("update `gpg` set `csr`='$csrname' where `id`='$insert_id'");
		waitForResult('gpg', $insert_id);

		showheader(_("Welcome to CAcert.org"));
		echo $resulttable;
		$query = "select * from `gpg` where `id`='$insert_id' and `crt`!=''";
		$res = mysql_query($query);
		if(mysql_num_rows($res) <= 0)
		{
			echo _("Your certificate request has failed to be processed correctly, please try submitting it again.")."<br>\n";
			echo _("If this is a re-occuring problem, please send a copy of the key you are trying to signed to support@cacert.org. Thank you.");
		} else {
			echo "<pre>";
			readfile(generatecertpath("crt","gpg",$insert_id));
			echo "</pre>";
		}

		showfooter();
		exit;
	}

	if($oldid == 2 && array_key_exists('change',$_REQUEST) && $_REQUEST['change'] != "")
	{
		showheader(_("My CAcert.org Account!"));
		foreach($_REQUEST as $id => $val)
		{
			if(substr($id,0,14)=="check_comment_")
			{
				$cid = intval(substr($id,14));
				$comment=trim(mysql_real_escape_string(stripslashes($_REQUEST['comment_'.$cid])));
				mysql_query("update `gpg` set `description`='$comment' where `id`='$cid' and `memid`='".$_SESSION['profile']['id']."'");
			}
		}
		echo(_("Certificate settings have been changed.")."<br/>\n");
		showfooter();
		exit;
	}

	$id = intval($id);

	showheader(_("Welcome to CAcert.org"));
	includeit($id, "gpg");
	showfooter();
?>
