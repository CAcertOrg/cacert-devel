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

	$query = "select `users`.`fname` as `fname`, `email`.`id` as `id`, `email`.`email` as `email` from `users`,`email`
			where `users`.`verified`=0 and
			(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`users`.`created`)) >= 300 and
			`users`.`id`=`email`.`memid` and `users`.`email`=`email`.`email`";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
                        $rnd = fopen("/dev/urandom", "r");
                        $hash = md5(fgets($rnd, 64));
                        fclose($rnd);

		mysqli_query($_SESSION['mconn'], "update `email` set `hash`='$hash' where `id`='".$row['id']."'");

		$body = "Hi ".$row['fname']."\n\n";
		$body .= "Due to some bugs with the new website we initially had issues with emails being sent out. This email is being sent to those effected so they can be re-sent their email probe to over come earlier issues. We apologise for any inconvenience this may have cause. To verify your account, simply click on the link below.\n\n";
		$body .= "http://www.cacert.org/verify.php?type=email&emailid=".$row['id']."&hash=$hash\n\n";
		$body .= "Best Regards\nCAcert Support Team";
echo $row['email']."\n";
		sendmail($row['email'], "[CAcert.org] Email Probe", $body, "support@cacert.org", "", "", "CAcert Support");
	}
?>
