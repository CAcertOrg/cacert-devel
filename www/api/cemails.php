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
	$username = mysql_real_escape_string($_REQUEST['username']);
	$password = mysql_real_escape_string($_REQUEST['password']);

	$query = "select * from `users` where `email`='$username' and (`password`=old_password('$password') or `password`=sha1('$password'))";
	$res = mysqli_query($_SESSION['mconn'], $query);
	if(mysqli_num_rows($res) != 1)
		die("403,That username couldn't be found\n");
	echo "200,Authentication Ok\n";
	$user = mysqli_fetch_assoc($res);
	$memid = $user['id'];
	$query = "select sum(`points`) as `points` from `notary` where `to`='$memid' group by `to`";
	$row = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], $query));
	$points = $row['points'];
	echo "CS=".intval($user['codesign'])."\n";
	echo "NAME=CAcert WoT User\n";
	if($points >= 50)
	{
		echo "NAME=".sanitizeHTML($user['fname'])." ".sanitizeHTML($user['lname'])."\n";
		if($user['mname'] != "")
			echo "NAME=".sanitizeHTML($user['fname'])." ".sanitizeHTML($user['mname'])." ".sanitizeHTML($user['lname'])."\n";
		if($user['suffix'] != "")
			echo "NAME=".sanitizeHTML($user['fname'])." ".sanitizeHTML($user['lname'])." ".sanitizeHTML($user['suffix'])."\n";
		if($user['mname'] != "" && $user['suffix'] != "")
			echo "NAME=".sanitizeHTML($user['fname'])." ".sanitizeHTML($user['mname'])." ".sanitizeHTML($user['lname'])." ".sanitizeHTML($user['suffix'])."\n";
	}
	$query = "select * from `email` where `memid`='$memid' and `hash`='' and `deleted`=0";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
		echo "EMAIL=".$row['email']."\n";
?>
