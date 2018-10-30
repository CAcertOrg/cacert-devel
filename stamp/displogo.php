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
	$img = "/www/stamp/images/CAverify.png";
	$arr = explode("//", mysqli_real_escape_string($_SESSION['mconn'], trim($_REQUEST['refer'])), 2);
	$arr = explode("/", $arr['1'], 2);
	$ref = $arr['0'];

	$arr = explode("//", mysqli_real_escape_string($_SESSION['mconn'], trim($_SERVER['HTTP_REFERER'])), 2);
	$arr = explode("/", $arr['1'], 2);
	$siteref = $arr['0'];

	if($_REQUEST['debug'] != 1)
		header('Content-type: image/png');
	$im = imagecreatefrompng($img);

	if($ref == "" || ($ref != $siteref && $siteref != ""))
	{
		$tc = imagecolorallocate ($im, 255, 0, 0);
		imagestring ($im, 2, 1, 30, "INVALID DOMAIN", $tc);
		imagestring ($im, 2, 1, 45, "Click to Report", $tc);
		imagepng($im);
		exit;	
	}

	list($invalid, $info) = checkhostname($ref);

	if($invalid > 0)
	{
		$tc = imagecolorallocate ($im, 255, 0, 0);
		imagestring ($im, 2, 1, 30, "INVALID DOMAIN", $tc);
		imagestring ($im, 2, 1, 45, "Click to Report", $tc);
		imagepng($im);
		exit;
	}

	$tz = intval($_REQUEST['tz']);
	$now = date("Y-m-d", gmmktime("U") + ($tz * 3600));

	$tc = imagecolorallocate ($im, 0, 0, 0);
	imagestring ($im, 4, 1, 27, "Valid Cert!", $tc);
	imagestring ($im, 1, 7, 42, "Click to Verify", $tc);
	imagestring ($im, 1, 20, 52, $now, $tc);
	imagepng($im);
?>
