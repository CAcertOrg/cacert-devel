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


if(0)
{
	$query = "select locations.id from locations, regions where locations.regid=regions.id and locations.ccid!=regions.ccid;";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$query = "update users set `assurer`='1' where `id`='${row['uid']}'";
		echo "inconsistence in location ".$row['locations.id']."\n";
		//mysqli_query($_SESSION['mconn'], $query);
	}
}

if(0)
{
	$query = "select id from locations where regid<1 or ccid<1;";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		//$query = "update users set `assurer`='1' where `id`='${row['uid']}'";
		echo "inconsistence in location ".$row['id']."\n";
		//mysqli_query($_SESSION['mconn'], $query);
	}
}
if(1)
{
	$query = "select users.id, locations.regid from users inner join locations on users.locid=locations.id where users.regid!=locations.regid or users.ccid!=locations.ccid;";
	$res = mysqli_query($_SESSION['mconn'], $query);
	echo mysqli_error();
	while($row = mysqli_fetch_assoc($res))
	{
		echo "inconsistence in user #".$row['id']."\n";
		$query = "update users set regid=".$row['regid']." where `id`=".$row['id'].";";

                echo "query: $query\n";
		if($row['regid']=="1182") mysqli_query($_SESSION['mconn'], $query);
	}
}

exit();

	mysqli_query($_SESSION['mconn'], "update `locations` set `acount`=0");
	$query = "SELECT `users`.`locid` AS `locid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`locid` != 0 and users.listme=1
			GROUP BY `users`.`locid`";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$query = "update `locations` set `acount`='${row['total']}' where `id`='${row['locid']}'";
		echo $query."\n";
		mysqli_query($_SESSION['mconn'], $query);
	}


	mysqli_query($_SESSION['mconn'], "update `regions` set `acount`=0");
	$query = "SELECT `users`.`regid` AS `regid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`regid` != 0 and users.listme=1
			GROUP BY `users`.`regid`";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$query = "update `regions` set `acount`='${row['total']}' where `id`='${row['regid']}'";
		echo $query."\n";
		mysqli_query($_SESSION['mconn'], $query);
	}




	mysqli_query($_SESSION['mconn'], "update `countries` set `acount`=0");
	$query = "SELECT `users`.`ccid` AS `ccid`, count(*) AS `total` FROM `users`
			WHERE users.assurer='1' AND `users`.`ccid` != 0 and users.listme=1
			GROUP BY `users`.`ccid`";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$query = "update `countries` set `acount`='${row['total']}' where `id`='${row['ccid']}'";
		echo $query."\n";
		mysqli_query($_SESSION['mconn'], $query);
	}




?>
