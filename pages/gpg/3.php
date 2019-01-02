<?php /*
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
	$certid = intval($_REQUEST['cert']);
	$query = "select * from `gpg` where `id`='$certid' and `memid`='".intval($_SESSION['profile']['id'])."'";
	$res = mysql_query($query);
	if(mysql_num_rows($res) <= 0)
	{
		echo _("No such OpenPGP key attached to your account.");
		showfooter();
		exit;
	}
	$row = mysql_fetch_assoc($res);
?>
<h3><?=_("Below is your OpenPGP key")?></h3>
<pre>
<?php readfile($row['crt']); ?>
</pre>
