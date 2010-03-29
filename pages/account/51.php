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
<? if($_SESSION['profile']['tverify'] <= 0) { echo _("You don't have access to this area."); } else { ?>
<?
	$uid = intval($_GET['photoid']);
	$query = "select * from `tverify` where `id`='$uid' and `modified`=0";
	$res = mysql_query($query);
	if(mysql_num_rows($res) > 0) { ?>
<img src="account.php?id=51&amp;photoid=<?=$uid ?>&amp;img=show" border="0" width="800">
<? } else {
        $query = "select * from `tverify` where `id`='$uid' and `modified`=1";
        $res = mysql_query($query);
        if(mysql_num_rows($res) > 0)
        {
                echo _("This UID has already been voted on.");
        } else {
                echo _("Unable to locate a valid request for that UID.");
        }
 } } ?>
