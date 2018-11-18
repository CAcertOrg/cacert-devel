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
	header('Content-Type: text/html; charset=UTF-8');

	if(isset($_REQUEST['i']) && $_REQUEST['i'] != "")
		echo "<html><body><script language=\"JavaScript\"><!--\n";

	$s = mysqli_real_escape_string($_SESSION['mconn'], $_REQUEST['s']);

	$id = mysqli_real_escape_string($_SESSION['mconn'], strip_tags($_REQUEST['id']));
	echo "parent._ac_rpc('".sanitizeHTML($id)."',";

	$bits = explode(",", $s);

	$loc = trim(mysqli_real_escape_string($_SESSION['mconn'], $bits['0']));
	$reg = trim(mysqli_real_escape_string($_SESSION['mconn'], isset($bits[1])?$bits[1]:""));
	$ccname = trim(mysqli_real_escape_string($_SESSION['mconn'], isset($bits[2])?$bits[2]:""));
	$query = "select `locations`.`id` as `locid`, `locations`.`name` as `locname`, `regions`.`name` as `regname`,
			`countries`.`name` as `ccname` from `locations`, `regions`, `countries` where
			`locations`.`name` like '$loc%' and `regions`.`name` like '$reg%' and `countries`.`name` like '$ccname%' and
			`locations`.`regid`=`regions`.`id` and `locations`.`ccid`=`countries`.`id`
			order by `locations`.`acount` DESC, `locations`.`name` ASC limit 10";
	$res = mysqli_query($_SESSION['mconn'], $query);
	$rc = 0;
	while($row = mysqli_fetch_assoc($res))
	{
		$rc++;
		if($rc > 1)
			echo ",";
		echo '"'.$row['locname'].', '.$row['regname'].', '.$row['ccname'].'", "'.$row['locid'].'"';
	}
	echo ");";

	if(isset($_REQUEST['i']) && $_REQUEST['i'] != "")
		echo "\n\n// -->\n</script></body></html>";

	exit;
?>
