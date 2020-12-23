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

require_once '../../includes/lib/check_weak_key.php';

	$username = $db_conn->real_escape_string($_REQUEST['username']);
	$password = $db_conn->real_escape_string($_REQUEST['password']);

	$query = "select * from `users` where `email`='$username' and (`password`=old_password('$password') or `password`=sha1('$password'))";
	$res = $db_conn->query($query);
	if($res->num_rows != 1)
		die("403,That username couldn't be found\n");
	$user = $res->fetch_assoc();
	$memid = $user['id'];
	$emails = array();
	foreach($_REQUEST['email'] as $email)
	{
		$email = $db_conn->real_escape_string(trim($email));
		$query = "select * from `email` where `memid`='".intval($memid)."' and `hash`='' and `deleted`=0 and `email`='$email'";
		$res = $db_conn->query($query);
		if($res->num_rows > 0)
		{
			$row = $res->fetch_assoc();
			$id = $row['id'];
			$emails[$id] = $email;
		}
	}
	if(count($emails) <= 0)
		die("404,Wasn't able to match any emails sent against your account");
	$query = "select sum(`points`) as `points` from `notary` where `to`='".intval($memid)."' and `notary`.`deleted`=0 group by `to`";
	$row = $db_conn->query($query)->fetch_assoc();
	$points = $row['points'];

	$name = "CAcert WoT User\n";
	$newname = $db_conn->real_escape_string(trim($_REQUEST['name']));
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
	$do = shell_exec("/usr/bin/openssl req -in $incsr_esc -out $checkedcsr_esc");
	@unlink($incsr);
	if(filesize($checkedcsr) <= 0)
		die("404,Invalid or missing CSR");

	$csrsubject = "/CN=$name";
	foreach($emails as $id => $email)
		$csrsubject .= "/emailAddress=".$email;

	$query = "insert into `emailcerts` set `CN`='".$db_conn->real_escape_string($user['email'])."', `keytype`='MS',
				`memid`='".intval($user['id'])."', `created`=FROM_UNIXTIME(UNIX_TIMESTAMP()),
				`subject`='".$db_conn->real_escape_string($csrsubject)."', `codesign`='".intval($codesign)."'";
	$db_conn->query($query);
	$certid = $db_conn->insert_id;
	$CSRname = generatecertpath("csr","client",$certid);
	rename($checkedcsr, $CSRname);

	$db_conn->query("update `emailcerts` set `csr_name`='$CSRname' where `id`='$certid'");

	foreach($emails as $emailid => $email)
		$db_conn->query("insert into `emaillink` set `emailcertsid`='$certid', `emailid`='".intval($emailid)."'");

	$do = shell_exec("../../scripts/runclient");
	sleep(10); // THIS IS BROKEN AND SHOULD BE FIXED
	$query = "select * from `emailcerts` where `id`='$certid' and `crt_name` != ''";
	$res = $db_conn->query($query);
	if($res->num_rows <= 0)
		die("404,Your certificate request has failed. ID: ".intval($certid));
	$cert = $res->fetch_assoc();
	echo "200,Authentication Ok\n";
	readfile("../".$cert['crt_name']);
?>
