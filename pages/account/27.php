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
    $orgid = intval($_REQUEST['orgid']);
    $row = mysql_fetch_assoc(mysql_query("select * from `orginfo` where `id`='" . $orgid . "'"));
    $orgname = $row['O'];
    $contactmail = $row['contact'];
    $town = $row['L'];
    $state = $row['ST'];
    $country = $row['C'];
    $comment = $row['comments'];
?>
<form method="post" action="account.php">
<?
    org_edit_org_table($orgname, $contactmail, $town, $state, $country, $comment, 1);
?>
<input type="hidden" name="oldid" value="<?=intval($id)?>">
<input type="hidden" name="orgid" value="<?=$orgid?>">
<input type="hidden" name="csrf" value="<?=make_csrf('orgdetchange')?>" />
</form>
