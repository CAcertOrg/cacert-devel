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

	$sendmail = 0;
	$locid = 2189758;

        $fp = fopen("email.txt", "r");
        while(!feof($fp))
        {
                $line = trim(fgets($fp, 4096));
                $lines .= wordwrap($line, 75, "\n")."\n";
        }
        fclose($fp);

	$query = "select * from `locations` where `id`='$locid'";
	$loc = mysql_fetch_assoc(mysql_query($query));
	$query = "select * from `locations` where (`lat` > ".$loc['lat']."-10 and `lat`<".$loc['lat']."+10 and
			`long`>".$loc['long']."-10 and `long`<".$loc['long']."+10) or `regid`=4576";
	$res = mysql_query($query);
	while($row = mysql_fetch_assoc($res))
	{
		$query = "select * from `users` where `id`='1'";
		$query = "select * from `users`,`alerts` where `users`.`locid`='".$row['id']."' and `users`.`id`=`alerts`.`memid` and
				(`general`='1' or `country`='1' or `regional`='1' or `radius`='1')";
		$dres = mysql_query($query);
		if(mysql_num_rows($dres) > 0)
		{
			$ddres = mysql_query("select * from `regions` where `id`='$row[regid]'");
			$ddrow = mysql_fetch_assoc($ddres);
			echo "Location: ".$row['name'].", $ddrow[name]\n";
		}
		while($user = mysql_fetch_assoc($dres))
		{
			$ddres = mysql_query("select sum(`points`) as `tp` from `notary` where `to`='".$user['id']."'");
			$ddrow = mysql_fetch_assoc($ddres);
			echo $user['fname']." ".$user['lname']." (".$user['email'].") - ".$user['radius']."/$ddrow[tp]\n";

			if($sendmail == 1)
			{
echo "Mail sent!\n";
				$body = "Hi ".$user['fname'].",\n\n".$lines;
				sendmail($user['email'], "[CAcert.org] CAcert Assurers at Ohio Linux Festival", $body, "duane@cacert.org", "", "", "CAcert Team");
			}
		}
	}
?>
