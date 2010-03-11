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
	if(array_key_exists('iagree',$_REQUEST) && $_REQUEST['iagree'] == "yes")
	{
		$output_file = $fname = readlink("../tarballs/current.tar.bz2");

		header('Pragma: public');

		header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
		header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
		header('Content-Transfer-Encoding: none');
		header('Content-Type: application/octetstream; name="' . $output_file . '"'); //This should work for IE & Opera
		header('Content-Type: application/octet-stream; name="' . $output_file . '"'); //This should work for the rest
		header('Content-Disposition: inline; filename="' . $output_file . '"');
		header("Content-length: ".intval(filesize($_SESSION['_config']['filepath']."/tarballs/$fname")));
		readfile($_SESSION['_config']['filepath']."/tarballs/$fname");
		exit;
	}
	loadem("index");
	showheader(_("CAcert Source License"));
?>
<body>
<p align="center">CAcert Inc.<br>
Source Code License Terms</p>
<pre>
<? include("../LICENSE"); ?>
</pre>
<form method="post">
<input type="checkbox" name="iagree" value="yes"> Tick this box to acknowledge you agree to these terms and conditions<br>
<input type="submit" name="process" value="Confirm, I agree to these terms and conditions">
</form>
<? showfooter(); ?>
