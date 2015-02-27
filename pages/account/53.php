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
	$town = array_key_exists('town',$_REQUEST)?mysql_real_escape_string(stripslashes($_REQUEST['town'])):"";
	$regid = array_key_exists('regid',$_REQUEST)?intval($_REQUEST['regid']):0;
	$ccid = array_key_exists('ccid',$_REQUEST)?intval($_REQUEST['ccid']):0;
	$start = array_key_exists('start',$_REQUEST)?intval($_REQUEST['start']):0;
	$limit = 25;

	echo "<div id='listshow'><ul class='top'>\n<li>";
	echo "<a href='account.php?id=53'>"._("Home")."</a>\n";

	$display = "";

	if($regid > 0)
	{
		$reg = mysql_fetch_assoc(mysql_query("select * from `regions` where `id`='$regid'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='account.php?id=53&amp;regid=$regid'>".sanitizeHTML($reg['name'])."</a> - <a href='account.php?action=add&amp;id=54&amp;regid=$regid'>"._("Add")."</a>\n".
			$display;
		$ccid = $_REQUEST['ccid'] = intval($reg['ccid']);
	}

	if($ccid > 0)
	{
		$cnt = mysql_fetch_assoc(mysql_query("select * from `countries` where `id`='$ccid'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='account.php?id=53&amp;ccid=$ccid'>".sanitizeHTML($cnt['name'])."</a> - <a href='account.php?action=add&amp;id=54&amp;ccid=$ccid'>"._("Add")."</a>\n".
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
			echo "<li><a href='account.php?id=53&amp;ccid=".intval($row['id'])."'>".sanitizeHTML($row['name'])."</a></li>\n";

		echo "</ul>\n</li>\n</ul></div>\n<br>\n";
	} elseif($regid <= 0) {
		echo "<ul>\n";
		$query = "select * from `regions` where `ccid`='$ccid' order by `name`";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
		{
			echo "<li>( <a href='account.php?action=edit&amp;id=54&regid=".intval($row['id'])."'>"._("edit")."</a> |";
			echo " <a href='account.php?action=delete&amp;id=53&regid=".intval($row['id'])."'";
			echo " onclick=\"return confirm('"._("Are you sure you want to delete this region and all connected locations?")."');\">"._("delete")."</a> )";
			echo " <a href='account.php?id=53&amp;regid=".intval($row['id'])."'>".sanitizeHTML($row['name'])."</a></li>\n";
		}

		echo "</ul>\n</li>\n</ul>\n</li>\n</ul></div>\n<br>\n";
	} elseif(intval(array_key_exists('locid',$_REQUEST)?$_REQUEST['locid']:0) <= 0) {
		echo "<ul>\n";
		if($town != "")
		{
			$query = "select * from `locations` where `regid`='$regid' and `name` < '$town'";
			$start = mysql_num_rows(mysql_query($query));
		}
		$query = "select * from `locations` where `regid`='$regid' order by `name` limit $start, $limit";
		$res = mysql_query($query);
		while($row = mysql_fetch_assoc($res))
		{
			echo "<li>( <a href='account.php?action=move&amp;id=54&amp;locid=".intval($row['id'])."'>"._("move")."</a> |";
			echo " <a href='account.php?action=aliases&amp;id=54&amp;locid=".intval($row['id'])."'>"._("aliases")."</a> |";
			echo " <a href='account.php?action=edit&amp;id=54&amp;locid=".intval($row['id'])."'>"._("edit")."</a> |";
			echo " <a href='account.php?action=delete&amp;id=53&amp;locid=".intval($row['id'])."'";
			echo " onclick=\"return confirm('Are you sure you want to delete this location?');\">"._("delete")."</a> ) ".sanitizeHTML($row['name'])." (".sanitizeHTML($row['lat']).",".sanitizeHTML($row['long']).")</li>\n";
		}

		echo "</ul>\n</li>\n</ul>\n</li>\n</ul></div>\n<br>\n";
		$st="";$prev="";$end="";$next="";
		$rc = mysql_num_rows(mysql_query("select * from `locations` where `regid`='$regid'"));
		if($start > 0)
		{
			$prev = $start - $limit;
			if($prev < 0)
				$prev = 0;

			$st = "[ <a href='account.php?id=53&amp;regid=$regid'><< "._("Start")."</a> ] ";
			$prev = "[ <a href='account.php?id=53&amp;regid=$regid&amp;start=$prev'>< "._("Previous")." $limit</a> ] ";
		}
		if($start < $rc - $limit)
		{
			$next = $start + $limit;
			$last = $rc - $limit;

			$next = "[ <a href='account.php?id=53&amp;regid=$regid&amp;start=$next'>"._("Next")." $limit ></a> ] ";
			$end = "[ <a href='account.php?id=53&amp;regid=$regid&amp;start=$last'>"._("End")." >></a> ]";
		}
		echo "<div id='search1'>$st</div><div id='search3'>$end</div>\n";
		echo "<div id='search2'>$prev</div><div id='search4'>$next</div>\n";
	}
?>
