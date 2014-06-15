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
	//include "../includes/general.php";

function gpg_hex2bin($data)
	{
		while(strstr($data, "\\x"))
		{
			$pos = strlen($data) - strlen(strstr($data, "\\x"));
			$before = substr($data, 0, $pos);
			$char = chr(hexdec(substr($data, $pos + 2, 2)));
			$after = substr($data, $pos + 4);
			$data = $before.$char.$after;
		}
		return(utf8_decode($data));
	}


function csvize($str) 
{
	if (strpos($str, "\"") != "" || strpos($str, ",") != "") {
		return "\"" . str_replace("\"", "\"\"", $str) . "\"";
	}
	return $str;
}
	mb_regex_encoding("UTF-8");

echo "Seaching ...\n";
	$res = mysql_query("SELECT * FROM gpg WHERE crt != '' and email=''");
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
		//echo "gpg says\n".htmlspecialchars($gpg);
		foreach (explode("\n", $gpg) as $line) 
		{
			$bits = explode(":", $line);
			if ($bits[0] != "pub" && $bits[0] != "uid") {
				continue;
			}
			if($bits[0] == "pub")
			{


				if (preg_match("/<([\w.-]*\@[\w.-]*)>/", $bits[9],$match)) 
				{
                                  //echo "Found: ".$match[1];
                                   $mail = trim(gpg_hex2bin($match[1]));


				echo "EMail: *$mail**\n";
				echo "update gpg set email='$mail' where id=$row[id]\n";
				  mysql_query("update gpg set email='$mail' where id=$row[id];");
				}
			}
		}


	}
	echo "Done\n";
	mysql_free_result($res);


?>
