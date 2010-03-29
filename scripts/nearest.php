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

// Searches for all assurers in a certain region

	include_once("../includes/mysql.php");

	$query = "select * from `users` where `locid`=33572 OR `regid`=870";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$ass = mysql_num_rows(mysql_query("select * from `notary` where `from`='".$row['id']."' and `from`!=`to`"));
		$dres = mysql_query("select sum(`points`) as `tp` from `notary` where `to`='".$row['id']."'");
		$drow = mysql_fetch_assoc($dres);
		if($drow['tp'] < 100)
			echo $row['fname']." ".$row['lname']." <".$row['email']."> (memid: ".$row['id']." assurances: $ass tp: ".$drow['tp'].")\n";
	}
?>
