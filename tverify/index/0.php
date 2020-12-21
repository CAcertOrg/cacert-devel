<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2020  CAcert Inc.

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
	$continue = 1;
	//Checking for Thawte Freemail members, who aren´t notaries
	if($_SERVER['SSL_CLIENT_S_DN_CN'] == 'Thawte Freemail Member')
	{
		$continue = 0;
		echo _("I wasn't able to locate your name on your certificate, as such you can't continue with this process.");
	}

	//Extracting the Email address from the certificate that is presented, looking up the email in the database to find the user that has registered it.
	if($continue == 1)
	{
		$addy = array();
		$emails = explode("/", trim($_SERVER['SSL_CLIENT_S_DN']));
		foreach($emails as $email)
		{
			$bits = explode("=", $email);
			if($bits['0'] == "emailAddress")
			{
				$query = "select * from `email` where `email`='".$bits['1']."' and `deleted`=0 and hash=''";
				$account = $db_conn->query($query);
				if($account->num_rows)
					$addy[] = $bits['1'];
			}
		}
	}

	//Verifying that we found a record with that email address
	if(count($addy) <= 0 && $continue == 1)
	{
		$continue = 0;
		echo _("I wasn't able to match any email accounts on your certificate to any accounts in our database, as such I can't continue with this process.");
	}

	//If we found one, we extract the member-id from the sql result of the query we did above, and fetch the name of that user
	if($continue == 1)
	{
		$row = $account->fetch_assoc();
		$memid = $row['memid'];


                //Fetching the name of the user we have in the database:
                $query = "select `fname`, `mname`, `lname`, `suffix` from `users` where `id`='$memid' and `deleted`=0";
 		$res = $db_conn->query($query);
 		$row = $res->fetch_assoc();

		//Building the user´s name, and ignoring punctuation
		$cacert_name=$row['fname']." ".$row['mname']." ".$row['lname']." ".$row['suffix'];
		$cacert_name=strtr($cacert_name,",.","");
		$cacert_name=trim(str_replace("  ", " ", $cacert_name));

		//Generate a short name form without the middle name
		$cacert_short_name=$row['fname']." ".$row['lname']." ".$row['suffix'];
		$cacert_short_name=strtr($cacert_short_name,",.","");
		$cacert_short_name=trim(str_replace("  ", " ", $cacert_short_name));


		$tverifybits = explode(" ", trim($_SERVER['SSL_CLIENT_S_DN_G']), 2);
		$firstname = trim($tverifybits['0']);
		$givenname = trim($_SERVER['SSL_CLIENT_S_DN_G']);
		$lastname = trim($_SERVER['SSL_CLIENT_S_DN_S']);
		$tverify_name=strtr("$givenname $lastname",",.","");
		$tverify_short_name=strtr("$firstname $lastname",",.","");

		if(($cacert_name != $tverify_name) and ($cacert_short_name == $tverify_name))
		{
			$continue = 0;
			printf(_("Your CAcert account contains a middle name (%s), but we cannot verify this middle name with the certificate."),$row['mname']);

		}

		if($continue and ($cacert_name != $tverify_name) and ($cacert_name == $tverify_short_name))
		{
			printf(_("Your certificate containes a middle name (%s) which isn´t listed in your CAcert account. In case you might want to get certificates with your middle name included in the future, you should add the middle name to your CAcert account before continueing."));
		}

		if($continue and ($cacert_name != $tverify_name) and ($cacert_name != $tverify_short_name) and ($cacert_short_name == $tverify_short_name))
		{
			printf(_("There is a problem with your middle name. You could remove the middle name in your CAcert account, which should help to continue with the TVerify process, but then you can´t use it in your certificates."));
		}

		if($continue and ($cacert_name != $tverify_name) and ($cacert_name != $tverify_short_name))
		{
			$continue = 0;
			printf(_("The name and email address on your certificate (%s) could not be exactly matched to any stored in our database (%s), as such I'm not able to continue with this process."),$tverify_name,$cacert_name);
		}
	}

	if($_SERVER['SSL_CLIENT_VERIFY'] == "SUCCESS" && $continue == 1)
	{
		$_SESSION['_config']['uid'] = $memid;
		$_SESSION['_config']['CN'] = trim($_SERVER['SSL_CLIENT_S_DN']);
?>
<p style="border:dotted 1px #900;padding:0.3em;background-color:#ffe;">
<?=_("By just submitting your Thawte certificate you can be issued 50 points automatically to any matching account in the system that you operate.")?><br>
<?=_("To receive an additional 40 points you must also include a valid link to your notary listing on the Thawte website.")?><br>
<?=_("If you meet the above criteria you are also elligible to receive an additional 60 points by submitting a legible government issued copy of your photo ID. If details on your photo ID aren't legible you may be excluded from receiving these points.")?></p>
<? if($_SESSION['_config']['errmsg'] != "") { ?><p>&nbsp;</p><p style="border:dotted 1px #900;padding:0.3em;background-color:#ffe;"><?
	echo $_SESSION['_config']['errmsg']."</p>";
	unset($_SESSION['_config']['errmsg']);
} ?>
<form method="post" action="index.php" enctype="multipart/form-data">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper">
  <tr>
    <td colspan="2" class="title"><?=_("Points Transfer and Verification")?></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?=_("Email Address")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="email" value="<?=$row['email']?>"></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?=_("Notary URL")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="notaryURL" value="<?=htmlentities($_POST['notaryURL'])?>"></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?=_("Photo ID")?>: </td>
    <td class="DataTD" width="125"><input type="file" name="photoid"></td>
  </tr>
  <tr> 
    <td class="DataTD"><?=_("Pass Phrase")?>: </td>
    <td class="DataTD"><input type="password" name="pword"></td>
  </tr> 
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Submit Application for Points Transfer")?>"></td>
  </tr>

</table>     
<input type="hidden" name="id" value="1">
</form> 
<?	} else if($continue == 1) {
		echo _("1I'm sorry, I couldn't verify your certificate");
	}
?>
