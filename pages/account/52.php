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

if($_SESSION['profile']['tverify'] <= 0) {
    echo _("You don't have access to this area.");
} else {
	$uid = intval($_GET['uid']);
	$query = "select * from `tverify` where `id`='".intval($uid)."' and `modified`=0";
	$res = mysql_query($query);
	if(mysql_num_rows($res) > 0) {
		$row = mysql_fetch_assoc($res);
		$memid = intval($row['memid']);

		$query2 = "select * from `tverify-vote` where `tverify`='".intval($uid)."' and `memid`='".intval($_SESSION['profile']['id'])."'";
		$rc2 = mysql_num_rows(mysql_query($query2));
		if($rc2 > 0) {
			showheader(_("My CAcert.org Account!"));
			echo _("You have already voted on this request.");
			showfooter();
			exit;
		}

		$query = "select sum(`points`) as `points` from `notary` where `to`='".intval($memid)."' and `deleted` = 0";
		$notary = mysql_fetch_assoc(mysql_query($query));
		$query = "select * from `users` where `id`='".intval($memid)."'";
		$user = mysql_fetch_assoc(mysql_query($query));
		$tobe = 50 - $notary['points'];
		if($row['URL'] != '' && $row['photoid'] != '') {
			$tobe = 150 - $notary['points'];
		} else if($row['URL'] != '') {
			$tobe = 90 - $notary['points'];
		}
		if(intval($tobe) <= 0) {
			$tobe = 0;
		}
?>
<?=_("Request Details")?>:<br>
<?=_("Name on file")?>: <?=sanitizeHTML($user['fname']." ".$user['mname']." ".$user['lname']." ".$user['suffix'])?><br>
<?=_("Primary email address")?>: <?=sanitizeHTML($user['email'])." (".intval($user['id']).")"?><br>
<?=_("Certificate Subject")?>: <?=sanitizeHTML($row['CN'])?><br>
<?		if($row['URL'] != '') { ?>
<?=_("Notary URL")?>: <a href="<?=$row['URL']?>"><?=$row['URL']?></a><br>
<?		} ?>
<?		if($row['photoid'] != '') { ?>
<?=_("Photo ID URL")?>: <a href="/account.php?id=51&amp;photoid=<?=intval($row['id'])?>"><?=_("Here")?></a><br>
<?		} ?>
<?=_("Current Points")?>: <?=intval($notary['points'])?><br>
<?=_("Potential Points")?>: <?=intval($tobe)?><br>
<?=_("Date of Birth")?>: <?=$user['dob']?> (YYYY-MM-DD)<br>

<br>
<form method="post" action="account.php">
<?=_("Comment")?>: <input type="text" name="comment"><br>
<input type="submit" name="agree" value="<?=_("I agree with this Application")?>">
<input type="submit" name="disagree" value="<?=_("I don't agree with this Application")?>">
<input type="hidden" name="oldid" value="<?=intval($_GET['id'])?>">
<input type="hidden" name="uid" value="<?=intval($uid)?>">
</form>
<?
	} else {
		$query = "select * from `tverify` where `id`='".intval($uid)."' and `modified`=1";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0) {
			echo _("This UID has already been voted on.")."<br/>";
		} else {
			if($uid) echo _("Unable to locate a valid request for that UID.")."<br/>";
		}

		// Search for open requests:
		$query = "select * from `tverify` where `modified`=0";
		$res = mysql_query($query);
		if(mysql_num_rows($res) > 0) {
			echo "<br/>"._("The following requests are still open:")."<br/><ul>";
			while($row = mysql_fetch_assoc($res)) {
				$uid=intval($row['id']);
				$query3 = "select * from `tverify-vote` where `tverify`='".intval($uid)."' and `memid`='".intval($_SESSION['profile']['id'])."'";
				$rc3 = mysql_num_rows(mysql_query($query3));
				if($rc3 <= 0)
				{
					echo "<li><a href='account.php?id=52&amp;uid=".intval($row['id'])."'>".intval($row['id'])."</a></li>\n";
				}
			}
			echo "</ul>\n<br>\n";
		} else {
			echo "<br/>"._("There are no pending requests where you haven't voted yet.");
		}
	}
}

?>
