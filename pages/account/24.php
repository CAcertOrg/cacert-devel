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
$orgname = '';
$contactmail = '';
$town = '';
$state = '';
$country = '';
$comment = '';

	// Reset session variables regarding Org's, present empty form
if (array_key_exists('O',$_SESSION['_config']))         $_SESSION['_config']['O'] = "";
if (array_key_exists('contact',$_SESSION['_config']))   $_SESSION['_config']['contact'] = "";
if (array_key_exists('L',$_SESSION['_config']))         $_SESSION['_config']['L'] = "";
if (array_key_exists('ST',$_SESSION['_config']))        $_SESSION['_config']['ST'] = "";
if (array_key_exists('C',$_SESSION['_config']))         $_SESSION['_config']['C'] = "";
if (array_key_exists('comments',$_SESSION['_config']))  $_SESSION['_config']['comments'] = "";

?>
<form method="post" action="account.php">
<?
org_edit_org_table($orgname, $contactmail, $town, $state, $country, $comment, 0);
?>
<input type="hidden" name="oldid" value="<?=$id?>">
</form>
