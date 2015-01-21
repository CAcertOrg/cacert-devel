#!/usr/bin/php -q
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
	include_once("../includes/mysql.php");

	$fp = fopen("exp-report.txt", "w");

	$d = dir("../crt/");
	while (false !== ($entry = $d->read()))
	{
		if(substr($entry, 0, 3) == "gpg")
			continue;
		$file = "../crt/$entry";
		if(!is_file($file))
			continue;

		$file_esc = escapeshellarg($file);
		if(substr($file, -3) == "der")
			$do = trim(`openssl x509 -inform der -in $file_esc -text -noout 2>&1 |grep 'Exponent'`);
		else
			$do = trim(`openssl x509 -in $file_esc -text -noout 2>&1 |grep 'Exponent'`);

		if($do == "")
			continue;

		list($crud, $exp, $crud) = explode(" ", $do);
		if($exp >= 65537)
			continue;

		list($a, $crud) = explode(".", $entry, 2);
		list($type, $id) = explode("-", $a);

		$id = intval($id);

		if($type == "client")
		{
			$query = "select `memid`,`serial`,`CN`,`subject`,`keytype`,`emailcerts`.`codesign` as `codesign`,`crt_name`,
					`emailcerts`.`created` as `created`,`emailcerts`.`revoked` as `revoked`,
					`emailcerts`.`expire` as `expire`, `rootcert`, `md`, `fname`, `lname`, `language`
					from `emailcerts`,`users` where `emailcerts`.`id`='$id' and `users`.`id`=`emailcerts`.`memid`";
			$res = mysql_query($query);
			if(mysql_num_rows($res) <= 0)
			{
				echo $query."\n";
				echo "$file: $do\n";
				continue;
			}

			$row = mysql_fetch_assoc($res);
			$email = $row['email'];
		} else if($type == "orgclient") {
			$query = "select `memid`,`serial`,`CN`,`subject`,`keytype`,`orgemailcerts`.`codesign` as `codesign`,`crt_name`,
					`orgemailcerts`.`created` as `created`,`orgemailcerts`.`revoked` as `revoked`,
					`orgemailcerts`.`expire` as `expire`, `rootcert`, `md`, `fname`, `lname`, `language`
					from `orgemailcerts`,`org`,`users` where `orgemailcerts`.`id`='$id' and
							`orgemailcerts`.`orgid`=`org`.`id` and `users`.`id`=`org`.`memid`";
			$res = mysql_query($query);
			if(mysql_num_rows($res) <= 0)
			{
				echo $query."\n";
				echo "$file: $do\n";
				continue;
			}

			$row = mysql_fetch_assoc($res);
			$email = $row['email'];
		} else if($type == "server") {
			$query = "select `memid`,`serial`,`CN`,`subject`,`crt_name`,
					`domaincerts`.`created` as `created`,`domaincerts`.`revoked` as `revoked`,
					`domaincerts`.`expire` as `expire`, `rootcert`, `md`, `fname`, `lname`, `language`
					from `domaincerts`,`domains`,`users` where `domaincerts`.`id`='$id' and
							`domains`.`id`=`domaincerts`.`domid` and `users`.`id`=`domains`.`memid`";
			$res = mysql_query($query);
			if(mysql_num_rows($res) <= 0)
			{
				echo $query."\n";
				echo "$file: $do\n";
				continue;
			}

			$row = mysql_fetch_assoc($res);
			$email = $row['email'];
		} else if($type == "orgserver") {
			$query = "select `memid`,`serial`,`CN`,`subject`,`crt_name`,
					`orgdomaincerts`.`created` as `created`,`orgdomaincerts`.`revoked` as `revoked`,
					`orgdomaincerts`.`expire` as `expire`, `rootcert`, `md`, `fname`, `lname`, `language`
					from `orgdomaincerts`,`org`,`users` where `orgdomaincerts`.`id`='$id' and
							`orgdomaincerts`.`orgid`=`org`.`id` and `users`.`id`=`org`.`memid`";
			$res = mysql_query($query);
			if(mysql_num_rows($res) <= 0)
			{
				echo $query."\n";
				echo "$file: $do\n";
				continue;
			}

			$row = mysql_fetch_assoc($res);
			$email = $row['email'];
		} else {
			echo "$file: $do\n";
			continue;
		}

		$body = "New Report:\n\n$do\n";

		foreach($row as $key => $val)
			$body .= "$key: $val\n";

		$body .= "\n\n".file_get_contents($file);
		fputs($fp, $body."\n\n===============================================================\n\n");
		echo "$file: $do\n";
	}
?>
