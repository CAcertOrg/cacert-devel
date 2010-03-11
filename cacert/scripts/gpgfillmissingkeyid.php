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
	require_once("../includes/mysql.php"); //general.php");

function csvize($str) 
{
	if (strpos($str, "\"") != "" || strpos($str, ",") != "") {
		return "\"" . str_replace("\"", "\"\"", $str) . "\"";
	}
	return $str;
}
	mb_regex_encoding("UTF-8");

echo "Seaching ...\n";
	$res = mysql_query("SELECT * FROM gpg WHERE crt != '' and keyid is null");
	if (!$res) {
		echo "Query FROM gpg failed!\n";
		exit;
	}
echo "Found:\n";

	$keys = array();
	while ($row = mysql_fetch_assoc($res)) {
	    echo "ID: ".$row["id"]."\n"; 
		$crt=$row["crt"];

		$gpg = `gpg --with-colons --homedir /tmp $crt 2>/dev/null`;
		echo "gpg says\n".htmlspecialchars($gpg);
		foreach (explode("\n", $gpg) as $line) 
		{
			$bits = explode(":", $line);
			if ($bits[0] != "pub" && $bits[0] != "uid") {
				continue;
			}
			if($bits[0] == "pub")
			{
				echo "KeyID: ".$bits[4]."\n";
				echo "update gpg set keyid='$bits[4]' where id=$row[id]\n";
                                echo "laenge: ".strlen($bits[4])."\n";
				if($row[id]>=1 && $row[id]<=100000 && strlen($bits[4])==16)
				{
				  mysql_query("update gpg set keyid='$bits[4]' where id=$row[id]\n");

				}
			}
			$match = false;
			$problem = "";
			$uid = " ".preg_replace('~\\\\x([0-9a-f])([0-9a-f])~ei', 'chr(hexdec("\\1\\2"))', $bits[9]);
			print "UID: $uid\n";
		}


	}
	echo "Done\n";
	mysql_free_result($res);


?>
