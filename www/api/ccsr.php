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

require_once '../../includes/lib/check_weak_key.php';

	$username = mysql_real_escape_string($_REQUEST['username']);
	$password = mysql_real_escape_string($_REQUEST['password']);

	$query = "select * from `users` where `email`='$username' and (`password`=old_password('$password') or `password`=sha1('$password'))";
	$res = mysql_query($query);
	if(mysql_num_rows($res) != 1)
		die("403,That username couldn't be found\n");
	$user = mysql_fetch_assoc($res);
	$memid = $user['id'];
	$emails = array();
	foreach($_REQUEST['email'] as $email)
	{
		$email = mysql_real_escape_string(trim($email));
		$query = "select * from `email` where `memid`='".intval($memid)."' and `hash`='' and `deleted`=0 and `email`='$email'";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_assoc($res);
			$id = $row['id'];
			$emails[$id] = $email;
		}
	}
	if(count($emails) <= 0)
		die("404,Wasn't able to match any emails sent against your account");
	$query = "select sum(`points`) as `points` from `notary` where `to`='".intval($memid)."' and `notary`.`deleted`=0 group by `to`";
	$row = mysql_fetch_assoc(mysql_query($query));
	$points = $row['points'];

	$name = "CAcert WoT User\n";
	$newname = mysql_real_escape_string(trim($_REQUEST['name']));
	if($points >= 50)
	{
		if($newname == $user['fname']." ".$user['lname'] ||
			$newname == $user['fname']." ".$user['mname']." ".$user['lname'] ||
			$newname == $user['fname']." ".$user['lname']." ".$user['suffix'] ||
			$newname == $user['fname']." ".$user['mname']." ".$user['lname']." ".$user['suffix'])
			$name = $newname;
	}

	$codesign = 0;
	if($user['codesign'] == "1" && $_REQUEST['codesign'] == "1" && $points >= 100)
		$codesign = 1;

	$CSR = trim($_REQUEST['optionalCSR']);

	if (($weakKey = checkWeakKeyCSR($CSR)) !== "")
	{
		die("403, $weakKey");
	}

	$incsr = tempnam("/tmp", "ccsrIn");
	$checkedcsr = tempnam("/tmp", "ccsrOut");
	$fp = fopen($incsr, "w");
	fputs($fp, $CSR);
	fclose($fp);
	$incsr_esc = escapeshellarg($incsr);
	$checkedcsr_esc = escapeshellarg($checkedcsr);
	$do = `/usr/bin/openssl req -in $incsr_esc -out $checkedcsr_esc`;
	@unlink($incsr);
	if(filesize($checkedcsr) <= 0)
		die("404,Invalid or missing CSR");

	$csrsubject = "/CN=$name";
	foreach($emails as $id => $email)
		$csrsubject .= "/emailAddress=".$email;

	$query = "insert into `emailcerts` set `CN`='".mysql_real_escape_string($user['email'])."', `keytype`='MS',
				`memid`='".intval($user['id'])."', `created`=FROM_UNIXTIME(UNIX_TIMESTAMP()),
				`subject`='".mysql_real_escape_string($csrsubject)."', `codesign`='".intval($codesign)."'";
	mysql_query($query);
	$certid = mysql_insert_id();
	$CSRname = generatecertpath("csr","client",$certid);
	rename($checkedcsr, $CSRname);

	mysql_query("update `emailcerts` set `csr_name`='$CSRname' where `id`='$certid'");

	foreach($emails as $emailid => $email)
		mysql_query("insert into `emaillink` set `emailcertsid`='$certid', `emailid`='".intval($emailid)."'");

	$do = `../../scripts/runclient`;
	sleep(10); // THIS IS BROKEN AND SHOULD BE FIXED
	$query = "select * from `emailcerts` where `id`='$certid' and `crt_name` != ''";
	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
		die("404,Your certificate request has failed. ID: ".intval($certid));
	$cert = mysql_fetch_assoc($res);
	echo "200,Authentication Ok\n";
	readfile("../".$cert['crt_name']);
?>
