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

	include_once("/www/includes/general.php");

	function clean($key)
	{
		global $db_conn;
		return($db_conn->real_escape_string(strip_tags(trim($_REQUEST[$key]))));
	}

	function checkhostname($ref)
	{
		global $db_conn;
		$ref = trim($ref);
		if($ref[count($ref)-1] == "." || $ref[count($ref)-1] == ":")
			$ref = substr($ref, 0, -1);

		$stampid = 0;
		$query = "select * from `stampcache` where `hostname`='$ref'";
		$res = $db_conn->query($query);
		if($res->num_rows > 0)
		{
			$row = $res->fetch_assoc();
			if($row['cacheexpire'] >= date("U"))
				return(array($row['valid'], $row));
			else {
				if($row['certid'] > 0)
				{
					if($row['org'] == 0)
						$query = "select * from `domaincerts` where `id`='".intval($row['certid'])."' and `expire`>NOW() and `revoked`=0";
					else
						$query = "select * from `orgdomaincerts` where `id`='".intval($row['certid'])."' and `expire`>NOW() and `revoked`=0";
					if($_REQUEST['debug'] == 1)
						echo $query."<br>\n";
					$res = $db_conn->query($query);
					if($res->num_rows > 0)
					{
						$query = "update `stampcache` set `cacheexpire`='".(date("U")+600)."' where `id`='$row[id]'";
						if($_REQUEST['debug'] == 1)
							echo $query."<br>\n";
						$db_conn->query($query);
						return(array($row['valid'], $row));
					}
				}
				$stampid = $row['id'];
			}
		}

		$query = "select *,`domaincerts`.`id` as `certid`,`domaincerts`.`created` as `issued` from `domlink`,`domains`,`domaincerts`
				where `domlink`.`domid`=`domains`.`id` and `domlink`.`certid`=`domaincerts`.`id` and
				`domaincerts`.`revoked`=0 and `domaincerts`.`expire` > NOW() and
				(`domaincerts`.`subject` like '%=DNS:$ref/%' OR `domaincerts`.`subject` like '%=$ref/%' OR
					`domaincerts`.`subject` like '%=DNS:$ref' OR `domaincerts`.`subject` like '%=$ref')
				group by `domaincerts`.`id` order by `domaincerts`.`id`";
		if($_REQUEST['debug'] == 1)
			echo $query."<br>\n";
		$res = $db_conn->query($query);
		if($res->num_rows <= 0)
		{
			$bits = explode(".", $ref);
			for($i = 1; $i < count($bits); $i++)
			{
				if($ref2 != "")
					$ref2 .= ".";
				$ref2 .= $bits[$i];
			}
			$query = "select *,`domaincerts`.`id` as `certid`,`domaincerts`.`created` as `issued` from `domlink`,`domains`,`domaincerts`
					where `domlink`.`domid`=`domains`.`id` and `domlink`.`certid`=`domaincerts`.`id` and
					`domaincerts`.`revoked`=0 and `domaincerts`.`expire` > NOW() and
					(`domaincerts`.`subject` like '%=DNS:$ref/%' or `domaincerts`.`subject` like '%=DNS:*.$ref2/%' OR
					`domaincerts`.`subject` like '%=DNS:$ref' or `domaincerts`.`subject` like '%=DNS:*.$ref2' OR
					`domaincerts`.`subject` like '%=$ref/%' or `domaincerts`.`subject` like '%=*.$ref2/%' OR
					`domaincerts`.`subject` like '%=$ref' or `domaincerts`.`subject` like '%=*.$ref2')
					group by `domaincerts`.`id` order by `domaincerts`.`id`";
			if($_REQUEST['debug'] == 1)
				echo $query."<br>\n";
			$res = $db_conn->query($query);
			if($res->num_rows <= 0)
			{
				$query = "select *,`orgdomaincerts`.`id` as `certid`,`orgdomaincerts`.`created` as `issued` from `orgdomaincerts`,`orgdomlink`,`orgdomains` where
						(`orgdomaincerts`.`subject` like '%=DNS:$ref/%' or `orgdomaincerts`.`subject` like '%=DNS:*.$ref2/%' OR
						`orgdomaincerts`.`subject` like '%=DNS:$ref' or `orgdomaincerts`.`subject` like '%=DNS:*.$ref2' OR
						`orgdomaincerts`.`subject` like '%=$ref/%' or `orgdomaincerts`.`subject` like '%=*.$ref2/%' OR
						`orgdomaincerts`.`subject` like '%=$ref' or `orgdomaincerts`.`subject` like '%=*.$ref2') AND
						`orgdomaincerts`.`id`=`orgdomlink`.`orgcertid` and `orgdomlink`.`orgdomid`=`orgdomains`.`id` and
						`orgdomaincerts`.`revoked`=0 and `orgdomaincerts`.`expire` > NOW()
						group by `orgdomaincerts`.`id` order by `orgdomaincerts`.`id`";
				if($_REQUEST['debug'] == 1)
					echo $query."<br>\n";
				$res = $db_conn->query($query);
				if($res->num_rows <= 0)
				{
					$invalid = 1;
				} else {
					$org = 1;
				}
			}
		}

		if($invalid == 0)
		{
			$cert = $res->fetch_assoc();
			if($org == 0)
			{
				$query = "SELECT *, sum(`points`) AS `total` FROM `users`, `notary` WHERE `users`.`id` = '$cert[memid]' AND
						`notary`.`to` = `users`.`id` and `notary`.`when` <= '$cert[issued]' and `notary`.`deleted`=0 GROUP BY `notary`.`to`";
				$user = $db_conn->query($query)->fetch_assoc();
			} else {
				$query = "select * from `orginfo` where `id`='$cert[orgid]'";
				$orgi = $db_conn->query($query)->fetch_assoc();
			}

			if($stampid <= 0)
			{
				$query = "insert into `stampcache` set `certid`='$cert[certid]',`cacheexpire`='".(date("U")+600)."',`issued`='$cert[issued]',
						`expire`='$cert[expire]',`subject`='$cert[subject]',`hostname`='$ref',`org`='$org',`points`='$user[total]',
						`O`='$orgi[O]',`L`='$orgi[L]',`ST`='$orgi[ST]',`C`='$orgi[C]',`valid`='$invalid'";
			} else {
				$query = "update `stampcache` set `certid`='$cert[certid]',`cacheexpire`='".(date("U")+600)."',`issued`='$cert[issued]',
						`expire`='$cert[expire]',`subject`='$cert[subject]',`hostname`='$ref',`org`='$org',`points`='$user[total]',
						`O`='$orgi[O]',`L`='$orgi[L]',`ST`='$orgi[ST]',`C`='$orgi[C]',`valid`='$invalid' where `id`='$stampid'";
			}
			$db_conn->query($query);
		} else if($stampid > 0) {
			$db_conn->query("update `stampcache` set `cacheexpire`='".(date("U")+600)."' where `id`='$stampid'");
		} else {
			$query = "insert into `stampcache` set `cacheexpire`='".(date("U")+600)."',`hostname`='$ref',`valid`='$invalid'";
			$db_conn->query($query);
		}

		$arr = array("issued" => $cert['issued'], "expire" => $cert['expire'], "subject" => $cert['subject'], "hostname" => $ref,
				"org" => $org, "points" => $user['total'], "O" => $orgi['O'], "L" => $orgi['L'], "ST" => $orgi['ST'],
				"C" => $orgi['C']);

		return(array($invalid, $arr));
	}
?>
