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
*/ ?>
<?php 	$errmsg = _("The CAcert root certificate was successfully installed");
	if(array_key_exists('errid',$_REQUEST) && $_REQUEST['errid'] == 1)
		$errmsg = _("Can't start the CEnroll control:").' '.substr(strip_tags(array_key_exists('hex',$_REQUEST)?$_REQUEST['hex']:""), 0, 5);
	if(array_key_exists('errid',$_REQUEST) && $_REQUEST['errid'] == 2)
		$errmsg = _("Problems were detected with the CAcert root certificate download error:").' '.substr(strip_tags(array_key_exists('hex',$_REQUEST)?$_REQUEST['hex']:""), 0, 5);
?>
<p><?php echo $errmsg?></p>
