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
*/

	$res=mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], "select sum(acount) as summe from countries"));
	$total1 =$res['summe'];

	$locid=array_key_exists('locid',$_REQUEST)?intval($_REQUEST['locid']):0;
	$regid=array_key_exists('regid',$_REQUEST)?intval($_REQUEST['regid']):0;
	$ccid=array_key_exists('ccid',$_REQUEST)?intval($_REQUEST['ccid']):0;

	echo "<ul class='top'>\n<li>";
	echo "<a href='wot.php?id=1'>"._("Home")." ("._("Listed").": $total1)</a>\n";

	$display = "";
	if($locid > 0)
	{
		$loc = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], "select * from `locations` where `id`='".$locid."'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='wot.php?id=1&locid=".$locid."'>".$loc['name']." ("._("Listed").": ".$loc['acount'].")</a>\n".
			$display;
		$regid = $loc['regid'];
	}

	if($regid > 0)
	{
		$reg = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], "select * from `regions` where `id`='".$regid."'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='wot.php?id=1&regid=".$regid."'>".$reg['name']." ("._("Listed").": ".$reg['acount'].")</a>\n".
			$display;
		$ccid = $reg['ccid'];
	}

	if($ccid > 0)
	{
		$cnt = mysqli_fetch_assoc(mysqli_query($_SESSION['mconn'], "select * from `countries` where `id`='".$ccid."'"));
		$display = "<ul class='top'>\n<li>\n".
			"<a href='wot.php?id=1&ccid=".$ccid."'>".$cnt['name']." ("._("Listed").": ".$cnt['acount'].")</a>\n".
			$display;
	}

	if($display)
		echo $display;

	if($ccid <= 0)
	{
		echo "<ul>\n";
		$query = "select * from countries where acount>0 order by `name`";
		$res = mysqli_query($_SESSION['mconn'], $query);
		while($row = mysqli_fetch_assoc($res))
		{
			echo "<li><a href='wot.php?id=1&ccid=".$row['id']."'>".$row['name']." ("._("Listed").": ".$row['acount'].")</a></li>\n";
		}
		echo "</ul>\n</li>\n</ul>\n<br>\n";
	} elseif($ccid > 0 && $regid <= 0 && $locid <= 0) {
		echo "<ul>\n";
		$query = "select * from regions where ccid='".$ccid."' and acount>0 order by `name`";
		$res = mysqli_query($_SESSION['mconn'], $query);
		while($row = mysqli_fetch_assoc($res))
		{
			echo "<li><a href='wot.php?id=1&regid=".$row['id']."'>".$row['name']." ("._("Listed").": ".$row['acount'].")</a></li>\n";
		}
		echo "</ul>\n</li>\n</ul>\n</li>\n</ul>\n<br>\n";
	} elseif($regid > 0 && $locid <= 0) {
		echo "<ul>\n";
		$query = "select * from locations where regid='".$regid."' and acount>0 order by `name`";
		$res = mysqli_query($_SESSION['mconn'], $query);
		while($row = mysqli_fetch_assoc($res))
		{
			echo "<li><a href='wot.php?id=1&locid=".$row['id']."'>".$row['name']." ("._("Listed").": ".$row['acount'].")</a></li>\n";
		}
		echo "</ul>\n</li>\n</ul>\n</li>\n</ul>\n<br>\n";
	} elseif($locid > 0){
		echo "</ul>\n</li>\n</ul>\n</li>\n</ul>\n</li>\n</ul>\n<br>\n";
	}
        if($locid>0 || $regid>0 || $ccid>0)
	{
	$query = "select *, `users`.`id` as `id` from `users`,`notary` where `listme`='1' and
			`ccid`='".$ccid."' and `regid`='".$regid."' and
			`locid`='".$locid."' and `users`.`id`=`notary`.`to` and `notary`.`deleted`=0
			group by `notary`.`to` HAVING SUM(`points`) >= 100 order by `points` desc";
	$list = mysqli_query($_SESSION['mconn'], $query);
	if(mysqli_num_rows($list) > 0)
	{
?>
<table align="center" valign="middle" border="0" cellspacing="0" cellpadding="0" class="wrapper" width="550">
  <tr>
    <td class="title"><?php echo _("Name")?></td>
    <td class="title"><?php echo _("Max Points")?></td>
    <td class="title"><?php echo _("Contact Details")?></td>
    <td class="title"><?php echo _("Email Assurer")?></td>
    <td class="title"><?php echo _("Assurer Challenge")?></td>
  </tr>

<?		while($row = mysqli_fetch_assoc($list)) { ?>
  <tr>
    <td class="DataTD" width="100"><nobr><?php echo sanitizeHTML($row['fname'])?> <?php echo substr($row['lname'], 0, 1)?>.</nobr></td>
    <td class="DataTD"><?php echo maxpoints($row['id'])?></td>
    <td class="DataTD"><?php echo sanitizeHTML($row['contactinfo'])?></td>
    <td class="DataTD"><a href="wot.php?id=9&amp;userid=<?php echo intval($row['id'])?>"><?php echo _("Email Me")?></a></td>
    <td class="DataTD"><?php echo $row['assurer']?_("Yes"):("<font color=\"#ff0000\">"._("Not yet!")."</font>")?></td>
  </tr>
<?php 		}
	}
?>
</table>
<br>
<?php } ?>
