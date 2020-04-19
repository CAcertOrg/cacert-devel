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

require_once($_SESSION['_config']['filepath'].'/includes/keygen.php');

?>
 -- <?=_("or")?> --
		<form method="post" action="account.php">
			<input type="hidden" name="keytype" value="VI">
			<textarea rows="20" cols="40" name="CSR"></textarea>
			<input type="submit" name="submit" value="<?=_("Submit CSR")?>">
			<input type="hidden" name="oldid" value="17">
		</form>
