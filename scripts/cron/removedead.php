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
	require_once(dirname(__FILE__).'/../../includes/notary.inc.php');

	$query = "select * from `users`	where `users`.`verified`=0 and
			(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`users`.`created`)) >= 172800";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		mysqli_query($_SESSION['mconn'], "delete from `email` where `memid`='".$row['id']."'");
		mysqli_query($_SESSION['mconn'], "delete from `users` where `id`='".$row['id']."'");
		delete_user_agreement($row['id']);
	}

	$query = "delete from `domains` where `hash`!='' and
			(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`created`)) >= 172800";
	mysqli_query($_SESSION['mconn'], $query);

	$query = "delete from `email` where `hash`!='' and
			(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`created`)) >= 172800";
	mysqli_query($_SESSION['mconn'], $query);

	$query = "delete from `disputedomain` where `hash`!='' and
			(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`created`)) >= 21600";
	mysqli_query($_SESSION['mconn'], $query);

	$query = "delete from `disputeemail` where `hash`!='' and
			(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`created`)) >= 21600";
	mysqli_query($_SESSION['mconn'], $query);

// the folloing part is presently not used as there is no running programme that uses temporary increase
// in case that there is a new one the procedure needs a rework regarding the point claculation
/*
	$query = "select * from `notary` where `expire`!=0 and `expire`<NOW()";
	$res = mysqli_query($_SESSION['mconn'], $query);
	while($row = mysqli_fetch_assoc($res))
	{
		$query = "select sum(`points`) as `points` from `notary` where `to`='$row[to]' and `expire`=0 group by `to`";
		$dres = mysqli_query($_SESSION['mconn'], $query);
		$drow = mysqli_fetch_assoc($dres);
		if($drow['points'] >= 150)
		{
			$query = "update `notary` set `expire`=0, `points`='0' where `to`='$row[to]' and `from`='$row[from]' and `expire`='$row[expire]'";
		} else {
			$newpoints = 150 - $drow['points'];
			$query = "update `notary` set `expire`=0, `points`='0' where `to`='$row[to]' and `from`='$row[from]' and `expire`='$row[expire]'";
			mysqli_query($_SESSION['mconn'], $query);
			$query = "insert into `notary` set `expire`=0, `points`='$newpoints', `to`='$row[to]', `from`='$row[from]', `when`=NOW(), `method`='Administrative Increase', `date`=NOW()";
		}

		$data = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], "select * from `users` where `id`='$row[to]'"));
		$body  = sprintf("%s %s (%s) had a temporary increase, but this has just expired and they have been reduced to 150 points.", $data['fname'], $data['lname'], $data['email'])."\n\n";
		sendmail("cacert-board@lists.cacert.org", "[CAcert.org] Temporary Increase Expired.", $body, "website@cacert.org", "", "", "CAcert Website");

                if($data['language'] != "")
                {
                        L10n::set_translation($data['language']);
                }

                $body  = _("You are receiving this email because you had a temporary increase to 200 points. This has since expired and you have been reduced to 150 points.")."\n\n";
                $body  = _("If you needed more time or any other extenuating circumstances you should contact us immediately so this situation can be dealt with immediately.")."\n\n";

                $body .= _("Best regards")."\n";
                $body .= _("CAcert Support Team");

                sendmail($data['email'], "[CAcert.org] "._("Temporary points increase has expired."), $body, "support@cacert.org", "", "", "CAcert Website");

		mysqli_query($_SESSION['mconn'], $query);
		fix_assurer_flag($row[to]);
	}
*/
?>
