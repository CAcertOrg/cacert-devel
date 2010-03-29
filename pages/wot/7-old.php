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
exit;
if($_GET['action'] != "update")
{
	$total1 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and `users`.`id`=`notary`.`to`
						group by `notary`.`to` HAVING SUM(`points`) >= 100"));

	$town = mysql_escape_string(stripslashes($_GET['town']));
	$start = intval($_GET['start']);
	$limit = 25;

	echo "<div id='listshow'><ul class='top'>\n<li>";
	echo "<a href='wot.php?id=7'>"._("Home")." ("._("Listed").": $total1)</a>\n";

	$display = "";
	$ccid=intval($_GET['ccid']);
	$locid=intval($_GET['locid']);
	$regid=intval($_GET['regid']);

	if($locid > 0)
	{
		$total4 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and `locid`='".$locid."' and
						`users`.`id`=`notary`.`to` group by `notary`.`to` HAVING SUM(`points`) >= 100"));
		$loc = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='".$locid."'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='wot.php?id=7&locid=".$locid."'>$loc[name] ("._("Listed").": $total4)</a>\n".
			$display;
		$regid = $loc['regid'];
	}

	if($regid > 0)
	{
		$total3 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and `regid`='".$regid."' and
						`users`.`id`=`notary`.`to` group by `notary`.`to` HAVING SUM(`points`) >= 100"));
		$reg = mysql_fetch_assoc(mysql_query("select * from `regions` where `id`='".$regid."'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='wot.php?id=7&regid=".$regid."'>$reg[name] ("._("Listed").": $total3)</a>\n".
			$display;
		$ccid = $reg['ccid'];
	}

	if($ccid > 0)
	{
		$total2 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and
						`ccid`='".$ccid."' and `users`.`id`=`notary`.`to`
						group by `notary`.`to` HAVING SUM(`points`) >= 100"));
		$cnt = mysql_fetch_assoc(mysql_query("select * from `countries` where `id`='".$ccid."'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='wot.php?id=7&ccid=".$ccid."'>$cnt[name] ("._("Listed").": $total2)</a>\n".
			$display;
	}

	if($display)
		echo $display;

	if($ccid <= 0)
	{
		echo "<ul>\n";
		$query = "select * from `countries` order by `name`";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
			echo "<li><a href='wot.php?id=7&ccid=$row[id]'>$row[name]</a></li>\n";

		echo "</ul>\n</li>\n</ul></div>\n<br>\n";
	} elseif($regid <= 0) {
		echo "<ul>\n";
		$query = "select * from `regions` where `ccid`='".$ccid."' order by `name`";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
			echo "<li><a href='wot.php?id=7&regid=$row[id]'>$row[name]</a></li>\n";

		echo "</ul>\n</li>\n</ul>\n</li>\n</ul></div>\n<br>\n";
	} elseif($locid <= 0) {
		echo "<ul>\n";
		if($town != "")
		{
			$query = "select * from `locations` where `regid`='".$regid."' and `name` < '$town'";
			$start = mysql_num_rows(mysql_query($query));
		}
		$query = "select * from `locations` where `regid`='".$regid."' order by `name` limit $start, $limit";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
			echo "<li><a href='wot.php?id=7&locid=$row[id]'>$row[name]</a></li>\n";

		echo "</ul>\n</li>\n</ul>\n</li>\n</ul></div>\n<br>\n";
		$rc = mysql_num_rows(mysql_query("select * from `locations` where `regid`='".$regid."'"));
		if($start > 0)
		{
			$prev = $start - $limit;
			if($prev < 0)
				$prev = 0;

			$st = "[ <a href='wot.php?id=7&regid=".$regid."'><< Start</a> ] ";
			$prev = "[ <a href='wot.php?id=7&regid=".$regid."&start=$prev'>< Previous $limit</a> ] ";
		}
		if($start < $rc - $limit)
		{
			$next = $start + $limit;
			$last = $rc - $limit;

			$next = "[ <a href='wot.php?id=7&regid=".$regid."&start=$next'>Next $limit ></a> ] ";
			$end = "[ <a href='wot.php?id=7&regid=".$regid."&start=$last'>End >></a> ]";
		}
		echo "<div id='search1'>$st</div><div id='search3'>$end</div>\n";
		echo "<div id='search2'>$prev</div><div id='search4'>$next</div>\n";
?>
<div align="left">
<form method="get" action="wot.php">
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="200">
  <tr>
    <td colspan="2" class="title"><?=_("Search this region")?></td>
  </tr>
  <tr>
    <td class="DataTD" width="125"><?=_("Location Name")?>: </td>
    <td class="DataTD" width="125"><input type="text" name="town" value="<?=sanitizeHTML($_GET['town'])?>" size="10"></td>
  </tr>
  <tr>
    <td class="DataTD" colspan="2"><input type="submit" name="process" value="<?=_("Search")?>"></td>
  </tr>
</table>
<input type="hidden" name="regid" value="<?=$regid?>">
<input type="hidden" name="id" value="7">
</form>
</div>
<?
	} else {
		echo "</ul>\n</li>\n</ul>\n</li>\n</ul>\n</li>\n</ul>\n<br>\n";
		echo "<p><a href='wot.php?id=7&action=update&locid=".$locid."'>";
		echo _("Make my location here");
		echo "</a></p>\n";
		echo "<p>"._("If you are happy with this location, click 'Make my location here' to update your location details.")."</p><br>\n";
	}
} else {
	$total1 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and `users`.`id`=`notary`.`to`
						group by `notary`.`to` HAVING SUM(`points`) >= 100"));

	if($locid > 0)
	{
		$total4 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and `locid`='".$locid."' and
						`users`.`id`=`notary`.`to` group by `notary`.`to` HAVING SUM(`points`) >= 100"));
		$loc = mysql_fetch_assoc(mysql_query("select * from `locations` where `id`='".$locid."'"));
		$regid = $loc['regid'];
	}

	if($regid) > 0)
	{
		$total3 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and `regid`='".$regid."' and
						`users`.`id`=`notary`.`to` group by `notary`.`to` HAVING SUM(`points`) >= 100"));
		$reg = mysql_fetch_assoc(mysql_query("select * from `regions` where `id`='".$regid."'"));
		$ccid = $reg['ccid'];
	}

		$total2 = mysql_num_rows(mysql_query("select * from `users`,`notary` where `listme`='1' and
						`ccid`='".$ccid."' and `users`.`id`=`notary`.`to`
						group by `notary`.`to` HAVING SUM(`points`) >= 100"));

	$_SESSION['profile']['ccid'] = $ccid;
	$_SESSION['profile']['regid'] = $regid;
	$_SESSION['profile']['locid'] = $locid;

	mysql_query("update `users` set `ccid`='".$ccid."',`regid`='".$regid."',`locid`='".$locid."'
			where `id`='".$_SESSION['profile']['id']."'");

	echo _("Your details have been updated.");
}
?>
