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

	if($_REQUEST['i'] != "")
		echo "<html><body><script language=\"JavaScript\"><!--\n";

	$s = mysql_real_escape_string($_REQUEST['s']);

	$id = mysql_real_escape_string(strip_tags($_REQUEST['id']));
	echo "parent._ac_rpc('".sanitizeHTML($id)."',";

	$bits = explode(",", $s);

	$loc = trim(mysql_real_escape_string($bits['0']));
	$reg = trim(mysql_real_escape_string($bits['1']));
	$ccname = trim(mysql_real_escape_string($bits['2']));
	$query = "select `locations`.`id` as `locid`, `locations`.`name` as `locname`, `regions`.`name` as `regname`,
			`countries`.`name` as `ccname` from `locations`, `regions`, `countries` where
			`locations`.`name` like '$loc%' and `regions`.`name` like '$reg%' and `countries`.`name` like '$ccname%' and
			`locations`.`regid`=`regions`.`id` and `locations`.`ccid`=`countries`.`id`
			order by `locations`.`acount` DESC, `locations`.`name` ASC limit 10";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$rc++;
		if($rc > 1)
			echo ",";
		echo '"'.$row['locname'].', '.$row['regname'].', '.$row['ccname'].'", "'.$row['locid'].'"';
	}
	echo ");";

	if($_REQUEST['i'] != "")
		echo "\n\n// -->\n</script></body></html>";

	exit;
?>
