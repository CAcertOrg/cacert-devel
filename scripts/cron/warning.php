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

	require_once(dirname(__FILE__).'/../../includes/mysql.php');
	require_once(dirname(__FILE__).'/../../includes/lib/l10n.php');

	$days = array("1" => "3", "15" => "2", "30" => "1", "45" => "0");

	foreach($days as $day => $warning)
	{
		$query = "SELECT `emailcerts`.`id`,`users`.`fname`,`users`.`lname`,`users`.`email`,`emailcerts`.`memid`,
				`emailcerts`.`subject`, `emailcerts`.`crt_name`,`emailcerts`.`CN`, `emailcerts`.`serial`,
				(UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP(NOW())) / 86400 as `daysleft`
				FROM `users`,`emailcerts`
				WHERE UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP(NOW()) > -7 * 86400 and
				UNIX_TIMESTAMP(`emailcerts`.`expire`) - UNIX_TIMESTAMP(NOW()) < $day * 86400 and
				`emailcerts`.`renewed`=0 and `emailcerts`.`warning` <= '$warning' and
				`emailcerts`.`revoked`=0 and `users`.`id`=`emailcerts`.`memid`";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
		{
			L10n::set_recipient_language(intval($row['id']));
			if($row['subject'] == "")
			{
				$row['crt_name'] = str_replace("../", "www/", $row['crt_name']);
				$row['crt_name'] = "/home/cacert/".$row['crt_name'];
				$crt_name = escapeshellarg($row['crt_name']);
				$subject = `openssl x509 -in $crt_name -text -noout|grep Subject:`;
				$bits = explode("/", $subject);
				foreach($bits as $val)
				{
					$sub = explode("=", trim($val));
					if($sub['0'] == "emailAddress")
					{
						$row['subject'] = "/CN=".$row['CN']."/emailAddress=".$sub['1'];
						break;
					}
				}
			}
			if($row['subject'] == "")
				$row['subject'] = "/CN=".$row['CN'];
			$row['daysleft'] = ceil($row['daysleft']);
			$body = sprintf(_("Hi %s"), $row['fname']).",\n\n";
			$body .= _("You are receiving this email as you are the listed contact for:")."\n\n";
			$body .= $row['subject']."\n\n";
			$body .= sprintf(_("Your certificate with the serial number %s is ".
						"set to expire in approximately %s days time. You can ".
						"renew it by going to the following URL:"),
					$row['serial'],
					$row['daysleft'])."\n\n";
			$body .= "https://www.cacert.org/account.php?id=5\n\n";
			$body .= _("Best Regards")."\n"._("CAcert Support");
			sendmail($row['email'], "[CAcert.org] "._("Your Certificate is about to expire"), $body, "support@cacert.org", "", "", "CAcert Support");
echo $row['fname']." ".$row['lname']." <".$row['email']."> (memid: ".$row['memid']." Subj: ".$row['subject']." timeleft: ".$row['daysleft'].")\n";
			$query = "update `emailcerts` set `warning`='".($warning+1)."' where `id`='".$row['id']."'";
			mysql_query($query);
		}
	}

	foreach($days as $day => $warning)
	{
		$select_clause =
					"`domaincerts`.`id`,
					`users`.`fname`, `users`.`lname`, `users`.`email`,
					`domains`.`memid`,
					`domaincerts`.`subject`, `domaincerts`.`crt_name`,
					`domaincerts`.`CN`,
					`domaincerts`.`serial`,
					(UNIX_TIMESTAMP(`domaincerts`.`expire`) -
						UNIX_TIMESTAMP(NOW())) / 86400 AS `daysleft`";
		$where_clause =
					"UNIX_TIMESTAMP(`domaincerts`.`expire`) -
						UNIX_TIMESTAMP(NOW()) > -7 * 86400
					AND UNIX_TIMESTAMP(`domaincerts`.`expire`) -
						UNIX_TIMESTAMP(NOW()) < $day * 86400
					AND `domaincerts`.`renewed` = 0
					AND `domaincerts`.`warning` <= '$warning'
					AND `domaincerts`.`revoked` = 0
					AND `domains`.`memid` = `users`.`id`";
		$query =
			"SELECT $select_clause
				FROM `users`, `domaincerts`, `domains`
				WHERE $where_clause
				AND `domaincerts`.`domid` = `domains`.`id`
			UNION DISTINCT
			SELECT $select_clause
				FROM `users`,
					`domaincerts` LEFT JOIN `domlink` ON
						(`domaincerts`.`id` = `domlink`.`certid`),
					`domains`
				WHERE $where_clause
				AND `domlink`.`domid` = `domains`.`id`";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
		{
			L10n::set_recipient_language(intval($row['memid']));
			if($row['subject'] == "")
				$row['subject'] = $row['CN'];

			$row['daysleft'] = ceil($row['daysleft']);
			$body = sprintf(_("Hi %s"), $row['fname']).",\n\n";
			$body .= _("You are receiving this email as you are the listed contact for:")."\n\n";
			$body .= $row['subject']."\n\n";
			$body .= sprintf(_("Your certificate with the serial number %s is ".
						"set to expire in approximately %s days time. You can ".
						"renew it by going to the following URL:"),
					$row['serial'],
					$row['daysleft'])."\n\n";
			$body .= "https://www.cacert.org/account.php?id=12\n\n";
			$body .= _("Best Regards")."\n"._("CAcert Support");
			sendmail($row['email'], "[CAcert.org] "._("Your Certificate is about to expire"), $body, "support@cacert.org", "", "", "CAcert Support");
echo $row['fname']." ".$row['lname']." <".$row['email']."> (memid: ".$row['memid']." Subj: ".$row['CN']." timeleft: ".$row['daysleft'].")\n";
			$query = "update `domaincerts` set `warning`='".($warning+1)."' where `id`='".$row['id']."'";
			mysql_query($query);
		}
	}
?>
