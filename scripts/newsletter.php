#!/usr/bin/php -q
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
	include_once("../includes/mysql.php");

	$lines = "";
	$fp = fopen("koelnemail.txt", "r");
	while(!feof($fp))
	{
		$line = trim(fgets($fp, 4096));
		$lines .= wordwrap($line, 75, "\n")."\n";
	}
	fclose($fp);

	$query = "select * from `locations` where `id`='417638'";
        $loc = $db_conn->query($query)->fetch_assoc();
        $query = "select `users`.* from `users`,`alerts`,`locations` where 
			((`lat` > ".$loc['lat']."-0.1 and `lat`<".$loc['lat']."+0.1 and `long`>".$loc['long']."-0.1 and `long`<".$loc['long']."+0.1)
)and
			(`alerts`.`general`=1 OR `alerts`.`country`=1 OR `alerts`.`regional`=1 OR `alerts`.`radius`=1) AND
			`locations`.`id` = `users`.`locid` and `users`.`id`=`alerts`.`memid`";
	//$query = "select * from `users` where `email`='pg@futureware.at'";
	$res = $db_conn->query($query);
	while($row = $res->fetch_assoc())
	{
			sendmail($row['email'], "[CAcert.org] Keysigningparty Koeln", $lines, "support@cacert.org", "", "", "CAcert Support", "returns@cacert.org", 1);
echo $row['email']."\n";
	}


//	OR `users`.`email` like '%.at' OR `users`.`email` like '%.fr' OR `users`.`email` like '%.de' OR
//	`users`.`email` like '%.nl' OR `users`.`email` like '%.be' OR `users`.`email` like '%.ch'
//	 OR `users`.`email` like '%.es' OR `users`.`email` like '%.pt' OR `users`.`email` like '%.ee'

?>
