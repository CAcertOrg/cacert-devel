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
	header("Content-type: image/png");
        $r = 255;
        $g = 255;
        $b = 255;
        $font = 1;
        $x = 25;
        $y = 4;	
	$im = imagecreatefrompng($_SESSION['_config']['filepath']."/www/images/secured.png");
	$tc = imagecolorallocate ($im, $r, $g, $b);
	imagestring ($im, $font, $x, $y, "CAcert.org", $tc);
	imagepng($im);
?>
