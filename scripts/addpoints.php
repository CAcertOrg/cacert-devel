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

// This script seems to add points for assurances that didn't received their points automatically before.

	include_once("../includes/mysql.php");

	$query = "select * from `notary` group by `from`";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$query = "select *,sum(`points`) as `points` from `users`, `notary` where `users`.`id`=`notary`.`to` and `users`.`id`='".$row['from']."' group by `notary`.`to`";
		$drow = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], $query));
		if($drow['points'] < 100 || $drow['points'] >= 150)
			continue;
		$query = "select * from `notary` where `from`='".$drow['id']."' and `to`='".$drow['id']."'";
		$num = mysqli_num_rows(mysqli_query($_SESSION['mconn'], $query));
		$query = "select * from `notary` where `from`='".$drow['id']."' and `to`!='".$drow['id']."'";
		$newnum = mysqli_num_rows(mysqli_query($_SESSION['mconn'], $query));
		if($num < $newnum)
		{
			echo $drow['fname']." ".$drow['lname']." <".$drow['email']."> (memid: ".$drow['id']." points: ".$drow['points']." - num: $num newnum: $newnum)\n";
			for($i = $newnum; $i > $num; $i--)
			{
				$newpoints = 2;
				if($drow['points'] + 1>= 150)
					break;
				if($drow['points'] + 2 > 150)
					$newpoints = 1;
				$query = "insert into `notary` set `from`='".$drow['id']."', `to`='".$drow['id']."',
						`points`='$newpoints', `method`='Administrative Increase', `date`=NOW()";
				mysqli_query($_SESSION['mconn'], $query);
				$drow['points'] += $newpoints;
				fix_assurer_flag($drow['id']);
			}
		}
	}
?>
