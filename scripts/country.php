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

	$query = "select * from `users` where ccid=13 OR email like '%.at'";
	$res = $db_conn->query($query);
	while($row = $res->fetch_assoc())
	{
		echo $row['fname']." ".$row['lname']." <".$row['email']."> (memid: ".$row['id']." ccid: ".$row['ccid'].")\n";
	}
?>
