#!/usr/bin/php -q
<?php
/*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2009  CAcert Inc.

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

// read texts

$lines_EN = <<<EOF

EOF;

$lines_EN = wordwrap($lines_EN, 75, "\n");
$lines_EN = mb_convert_encoding($lines_EN, "HTML-ENTITIES", "UTF-8");


$lines_DE = <<<EOF

EOF;

$lines_DE = wordwrap($lines_DE, 75, "\n");
$lines_DE = mb_convert_encoding($lines_DE, "HTML-ENTITIES", "UTF-8");


// read last used id
$lastid = 0;
if (file_exists("send_heartbleed_lastid.txt"))
{
	$fp = fopen("send_heartbleed_lastid.txt", "r");
	$lastid = trim(fgets($fp, 4096));
	fclose($fp);
}

echo "ID now: $lastid\n";


$count = 0;

$query = "
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `alerts` as a
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and a.`memid` = u.`id`
			and a.`general` = 1
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `domains` as d, `domaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`domid` = d.`id` and d.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `domains` as d, `domlink` as dl, `domaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`id` = dl.`certid` and dl.`domid` = d.`id` and d.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `org` as o, `orgdomaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`orgid` = o.`orgid` and o.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	union distinct
	(
		select u.`id`, u.`fname`, u.`lname`, u.`email`, u.`language`
		from `users` as u, `org` as o, `orgdomains` as d, `orgdomlink` as dl, `orgdomaincerts` as dc
		where u.`deleted` = 0 and u.`id` > '$lastid'
			and dc.`id` = dl.`orgcertid` and dl.`orgdomid` = d.`id`
				and d.`orgid` = o.`orgid` and o.`memid` = u.`id`
			and dc.`expire` >= '2011-12-01'
	)
	order by `id`";

$res = mysql_query($query);

while($row = mysql_fetch_assoc($res))
{
	$mailtxt = "Dear ${row["fname"]} ${row["lname"]},\n".$lines_EN."\n\n";
	switch ($row["language"])
	{
		case "de_DE":
		case "de":
			$mailtxt .= $lines_DE;
			break;
	}

	sendmail($row['email'], "[CAcert.org] Changes at CAcert", $mailtxt, "mailing@cacert.org", "", "", "CAcert", "returns@cacert.org", "");

	$fp = fopen("send_heartbleed_lastid.txt", "w");
	fputs($fp, $row["id"]."\n");
	fclose($fp);

	$count++;
	echo "Sent ${count}th mail. User ID: ${row["id"]}\n";

	sleep (1);
}
