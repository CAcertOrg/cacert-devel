<?php
//just temoprary file to find all function needed for account delete

function account_email_delete($mailid){
//deletes an email entry from an acount
//revolkes all certifcates for that email address
//called from www/account.php if($process != "" && $oldid == 2)
//called from www/diputes.php if($type == "reallyemail") / if($action == "accept")

	$query = "select `emailcerts`.`id` 
		from `emaillink`,`emailcerts` where
		`emailid`='$mailid' and `emaillink`.`emailcertsid`=`emailcerts`.`id` and
		`revoked`=0 and UNIX_TIMESTAMP(`expire`)-UNIX_TIMESTAMP() > 0
			group by `emailcerts`.`id`";
	$dres = mysql_query($query);
	while($drow = mysql_fetch_assoc($dres)){
		mysql_query("update `emailcerts` set `revoked`='1970-01-01 10:00:01' where `id`='".$drow['id']."'");
	}
	$query = "update `email` set `deleted`=NOW() where `id`='$mailid'";
	mysql_query($query);
}

function account_domain_delete($domainid){
//deletes an domain entry from an acount
//revolkes all certifcates for that domain address
//called from www/account.php if($process != "" && $oldid == 9)
//called from www/diputes.php if($type == "reallydomain") / if($action == "accept")

	$query = "select distinct `domaincerts`.`id`
		from `domaincerts`, `domlink`
		where `domaincerts`.`domid` = '$domainid'
		or (
		`domaincerts`.`id` = `domlink`.`certid`
		and `domlink`.`domid` = '$domainid'";
	$dres = mysql_query($query);
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
		where `id` = '$domainid'");
}

function account_delete($id, $arbno, $adminid){
//deletes an account following the deleted account routnie V3 
// called from www/account.php if($oldid == 50 && $process != "")
//change password

	$pool = 'abcdefghijklmnopqrstuvwxyz';
	$pool .= '0123456789!()§';
	$pool .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	srand ((double)microtime()*1000000);
	$password="";
	for($index = 0; $index < 30; $index++)
	{
		$password .= substr($pool,(rand()%(strlen ($pool))), 1);
	}
	mysql_query("update `users` set `password`=sha1('".$password."') where `id`='".$id."'");

//create new mail for arbitration number
	$query = "insert into `email` set `email`='".$arbno."@cacert.org',`memid`='".$id."',`created`=NOW(),`modified`=NOW(), `attempts`=-1";
	mysql_query($query);
	$emailid = mysql_insert_id();

//set new mail as default
	$query = "update `users` set `email`='".$arbno."@cacert.org' where `id`='".$id."'";
	mysql_query($query);

//delete all other email address
	$query = "select * from `email` where `memid`='".$id."' and `id`!='".$emailid."'" ;
	$res=mysql_query($query);
	while($row = mysql_fetch_assoc($res)){
		account_email_delete($row['id']);
	}

//delete all domains
	$query = "select * from `domains` where `memid`='".$id."'";
	$res=mysql_query($query);
	while($row = mysql_fetch_assoc($res)){
		account_email_delete($row['id']);
	}

//clear alert settings
	mysql_query("update `alerts` set `general`='1' where `memid`='$id'");
	mysql_query("update `alerts` set `country`='1' where `memid`='$id'");
	mysql_query("update `alerts` set `regional`='1' where `memid`='$id'");
	mysql_query("update `alerts` set `radius`='1' where `memid`='$id'");

//set default location
	$query = "update `users` set `locid`='2256755', `regid`='243', `ccid`='12' where `id`='".$id."'";
	mysql_query($query);

//clear listings
	$query = "update `users` set `listme`=' ',`contactinfo`=' ' where `id`='".$id."'";
	mysql_query($query);

//set lanuage to default
	//set default language
	mysql_query("update `users` set `language`='en_AU' where `id`='".$id."'");
	//delete secondary langugaes
	mysql_query("delete from `addlang` where `userid`='".$id."'");

//change secret questions 
	for($i=1;$i<=5;$i++){
		$q="";
		$a="";
		for($index = 0; $index < 30; $index++)
		{
			$q .= substr($pool,(rand()%(strlen ($pool))), 1);
			$a .= substr($pool,(rand()%(strlen ($pool))), 1);
		}
		$query = "update `users` set `Q".$i."`='".$q."',
						`A".$i."`='".$a."',
						where `id`='".$id."'";
		mysql_query($query);
	}

//change personal information to arbitration number and DOB=1900-01-01
	$query = "select `fname`,`mname`,`lname`,`suffix`,`dob` from `users` where `id`='$userid'";
	$details = mysql_fetch_assoc(mysql_query($query));
	$query = "insert into `adminlog` set `when`=NOW(),`old-lname`='${details['lname']}',`old-dob`='${details['dob']}',
		`new-lname`='$arbno',`new-dob`='1900-01-01',`uid`='$id',`adminid`='".$adminid."'";
	mysql_query($query);
	$query = "update `users` set `fname`='".$arbno."',
		`mname`='".$arbno."',
		`lname`='".$arbno."',
		`suffix`='".$arbno."',
		`dob`='1900-01-01'
		where `id`='".$id."'";
	mysql_query($query);

//clear all admin flags
	mysql_query("update `users` set `assurer`='0' where `id`='$id'");
	mysql_query("update `users` set `assurer_blocked`='0' where `id`='$id'");
	mysql_query("update `users` set `codesign`='0' where `id`='$id'");
	mysql_query("update `users` set `orgadmin`='0' where `id`='$id'");
	mysql_query("update `users` set `ttpadmin`='0' where `id`='$id'");
	mysql_query("update `users` set `locadmin`='0' where `id`='$id'");
	mysql_query("update `users` set `admin`='0' where `id`='$id'");
	mysql_query("update `users` set `adadmin`='0' where `id`='$id'");
	mysql_query("update `users` set `tverify`='0' where `id`='$id'");

//block account
	mysql_query("update `users` set `locked`='1' where `id`='$id'");
}

?>
