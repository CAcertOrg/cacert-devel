#!/usr/bin/php -q
<?php /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2009  CAcert Inc.

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
	$fp = fopen("assurer.txt", "r");
	while(!feof($fp))
	{
		$line = trim(fgets($fp, 4096));
		$lines .= wordwrap($line, 75, "\n")."\n";
	}
	fclose($fp);

	$query = "
select u.email, fname, lname, sum(n.points) from users u, notary n
 where n.to=u.id
   and not EXISTS(SELECT 1 FROM `cats_passed` AS `tp`, `cats_variant` AS `cv` WHERE `tp`.`variant_id` = `cv`.`id` AND `cv`.`type_id` = 1 AND `tp`.`user_id` = `u`.`id`)
   and exists(select 1 from notary n2 where n2.from=u.id and year(n2.`when`)>2007)
   and (select count(*) from notary n3 where n3.from=u.id) > 1
 group by email, fname, lname
 having sum(points)>99;
			";
//	echo $query;
// comment next line when starting to send mail not only to me 
	$res = mysqli_query($_SESSION['mconn'], $query);
	$xrows = mysqli_num_rows($res);
	while($row = mysqli_fetch_assoc($res))
	{
		 echo $row['pts']."..".$row['email']."...\n";
    // uncomment next line to send mails ...
		sendmail($row['email'], "[CAcert.org] Assurer Test", $lines, "teus@cacert.org", "", "", "CAcert Events Organisation", "returns@cacert.org", 1);
	}
  // 1x cc to events.cacert.org
	sendmail("philipp@cacert.org", "[CAcert.org] Assurer Test", $lines, "teus@cacert.org", "", "", "CAcert Events Organisation", "returns@cacert.org", 1);
	// 1x mailing report to events.cacert.org
  sendmail("philipp@cacert.org", "[CAcert.org] Assurer Report", "assurer information sent to $xrows recipients.", "support@cacert.org", "", "", "CAcert Assurer Organisation", "returns@cacert.org", 1);	
?>
