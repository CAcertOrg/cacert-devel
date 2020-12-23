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
*/ ?>
<?
	loadem("index");

	$id = intval($id);

	showheader(_("Welcome to CAcert.org"));

	if($id > 0)
	{
		$query = "select * from `news` where `id`='$id'";
		$row = $db_conn->query($query)->fetch_assoc();

		echo "<h3>".$row['short']."</h3>\n";
		echo "<p>Posted by ".$row['who']." at ".$row['when']."</p>\n";

		echo "<p>".str_replace("\n", "<br>\n", $row['story'])."</p>\n";
	} else {
		$query = "select *, UNIX_TIMESTAMP(`when`) as `TS` from news order by `when` desc";
		$res = $db_conn->query($query);
		while($row = $res->fetch_assoc())
		{
			echo "<p><b>".date("Y-m-d", $row['TS'])."</b> - ".$row['short']."</p>\n";
			if($row['story'] != "")
				echo "<p>[ <a href='news.php?id=".$row['id']."'>"._("Full Story")."</a> ]</p>\n";
		}
	}

	echo "<p>[ <a href='javascript:history.go(-1)'>"._("Go Back")."</a> ]</p>\n";

	showfooter();
?>
