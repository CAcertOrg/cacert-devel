<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2020  CAcert Inc.

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
	header('Content-Type: text/html; charset=UTF-8');

	if(array_key_exists("i", $_REQUEST) && $_REQUEST['i'] != "")
		echo '<!DOCTYPE html><html lang="en"><body><script type="application/javascript">';

	/** @var mysqli $db_conn */
	$s = $db_conn->real_escape_string($_REQUEST['s']);

	$id = $db_conn->real_escape_string(strip_tags($_REQUEST['id']));

	$bits = explode(",", $s);

	$location_name = count($bits) > 0 ? $db_conn->real_escape_string(trim($bits['0'])) : '';
	$region_name = count($bits) > 1 ? $db_conn->real_escape_string(trim($bits['1'])) : '';
	$country_name = count($bits) > 2 ? $db_conn->real_escape_string(trim($bits['2'])) : '';
	$query = sprintf("SELECT locations.id AS locid, locations.name AS locname, regions.name AS regname, countries.name AS ccname
FROM locations, regions, countries
WHERE locations.name LIKE '%s%%' AND regions.name LIKE '%s%%' AND countries.name LIKE '%s%%'
  AND locations.regid = regions.id AND locations.ccid = countries.id
ORDER BY locations.acount DESC, locations.name
LIMIT 10", $location_name, $region_name, $country_name);
	$res = $db_conn->query($query);
	$rc = 0;
	$locations = [];
	while($row = $res->fetch_assoc())
	{
		array_push($locations, sprintf("\"%s, %s, %s\", \"%s\"", $row['locname'], $row['regname'], $row['ccname'],
			$row['locid']));
	}
	printf("parent._ac_rpc('%s',%s);", sanitizeHTML($id), implode(",", $locations));

	if(array_key_exists("i", $_REQUEST) && $_REQUEST['i'] != "")
		echo "</script></body></html>";

	exit;
