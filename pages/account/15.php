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
*/ ?>
<?
	$certid = 0; if(array_key_exists('cert',$_REQUEST)) $certid=intval($_REQUEST['cert']);

	$query = "select * from `domaincerts`,`domains` where `domaincerts`.`id`='$certid' and
			`domains`.`memid`='".intval($_SESSION['profile']['id'])."' and
			`domains`.`id`=`domaincerts`.`domid`";
	$res = mysqli_query($_SESSION['mconn'], $query);
	if(mysqli_num_rows($res) <= 0)
	{
		echo _("No such certificate attached to your account.");
		showfooter();
		exit;
	}
	$row = mysqli_fetch_assoc($res);
        $crtname=escapeshellarg($row['crt_name']);
	$cert = `/usr/bin/openssl x509 -in $crtname`;
?>
<h3><?=_("Below is your Server Certificate")?></h3>
<pre>
<?=$cert?>
</pre>
