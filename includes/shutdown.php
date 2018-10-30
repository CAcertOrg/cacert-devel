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

	// This is the big power-off switch. It switches off all certificate issueing and assuring functions of the website.
	// Revocation services are NOT affected, and will continue to work.

	if(0)
	{ ?>
		<font color="#ff0000"><?php printf(_("This function is currently disabled. Please visit %s for more information."),
			"<a target='_blank' href='http://wiki.cacert.org/wiki/ClientSecurity'>http://wiki.cacert.org/wiki/ClientSecurity</a>")?></font>
<?php 		exit;
	}
?>
