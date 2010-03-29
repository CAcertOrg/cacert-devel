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

	echo("Content-Type: text/csv; charset=utf-8");

function mb_replace($str, $match, $replacement) {
 if ($match == "") { return $str; }
	$mlen = mb_strlen($match, "UTF-8");
	while (($pos = mb_strpos($str, $match, 0, "UTF-8")) != "") {
		//print "$str, $match, $replacement, $pos\n";
		$str = mb_substr($str, 0, $pos, "UTF-8")
			. ":" . $replacement . ":"
			. mb_substr($str, $pos + $mlen,
				    mb_strlen($str, "UTF-8") - $pos - $mlen,
				    "UTF-8");
		//$pos = mb_strpos($str, $match, 0, "UTF-8");
		//print "$str, $match, $replacement, $pos\n";
	}
	$replacement .= "__C_A_S_E__M_I_S_M_A_T_C_H";
	while ($_GET['case']
		   && ($pos = mb_stripos($str, $match, 0, "UTF-8")) != "") {
		$str = mb_substr($str, 0, $pos, "UTF-8")
			. ":" . $replacement . ":"
			. mb_substr($str, $pos + $mlen,
				    mb_strlen($str, "UTF-8") - $pos - $mlen,
				    "UTF-8");
	}
	return $str;
}

function deUmlaut($str) {
	return iconv("UTF-8", "US-ASCII//TRANSLIT",
		     mb_convert_encoding($str, "UTF-8"));
}

function deUmlaut2($str) {
	return mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "Ä"), "Ae",
		 mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "Ö"), "Oe",
		 mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "Ü"), "Ue",
		 mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "ä"), "ae",
		 mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "ö"), "oe",
		 mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "ü"), "ue",
		 mb_ereg_replace(iconv("ISO-8859-1", "UTF-8", "ß"), "ss",
				 mb_convert_encoding($str, "UTF-8"))))))));
}

function csvize($str) {
	if (strpos($str, "\"") != "" || strpos($str, ",") != "") {
		return "\"" . str_replace("\"", "\"\"", $str) . "\"";
	}
	return $str;
}
	mb_regex_encoding("UTF-8");

	$res = mysql_query("SELECT id, memid FROM gpg WHERE crt != ''");
	if (!$res) {
		echo "Query FROM gpg failed!\n";
		exit;
	}

	$keys = array();
	while ($row = mysql_fetch_row($res)) {
	    array_push($keys, $row);
	}
	mysql_free_result($res);

	foreach ($keys as $key) {
		$crt = "../crt/gpg-" . $key[0] . ".crt";
		if (!is_file($crt)) {
			echo "Missing cert $crt!\n";
			continue;
		}

		$res = mysql_query("SELECT fname, mname, lname, suffix FROM users WHERE id = " . $key[1]);
		if (!$res) {
			echo "Query FROM users failed!\n";
			exit;
		}
		$user = mysql_fetch_assoc($res);
		if (!$user) {
			echo "User #" . $key[1] . " not found?!\n";
			continue;
		}
		mysql_free_result($res);

		$res = mysql_query("SELECT email FROM email WHERE hash = '' AND memid = " . $key[1]);
		if (!$res) {
			echo "Query FROM email failed!\n";
			exit;
		}
		$addrs = array();
		while ($addr = mysql_fetch_row($res)) {
			array_push($addrs, $addr[0]);
		}
		mysql_free_result($res);

		$gpg = `gpg --with-colons --homedir /tmp $crt 2>/dev/null`;
		//echo "gpg says\n".htmlspecialchars($gpg);
		foreach (explode("\n", $gpg) as $line) {
			$bits = explode(":", $line);
			if ($bits[0] != "pub" && $bits[0] != "uid") {
				continue;
			}
			$match = false;
			$problem = "";
			$uid = " ".preg_replace('~\\\\x([0-9a-f])([0-9a-f])~ei', 'chr(hexdec("\\1\\2"))', $bits[9]);
			//print "$uid\n";
			if (iconv("UTF-8", "UTF-8", $uid)) {
				$uid = mb_ereg_replace("\\\\", "\\x5c", $uid);
				$uid = mb_ereg_replace("\\:", "\\x3a", $uid);
			} else {
				if ($tmp = iconv("ISO-8859-1", "UTF-8", $uid)) {
					$problem = ":BAD_ENCODING:";
					$uid = $tmp;
					$uid = mb_ereg_replace("\\\\", "\\x5c",
							       $uid);
					$uid = mb_ereg_replace("\\:", "\\x3a",
							       $uid);
				} else {
					$problem = ":UNKNOWN_ENCODING:";
					$uid = $bits[9];
				}
			}
			//print "$uid\n";
			foreach ($addrs as $addr) {
				//print "$uid, $addr\n";
				//print mb_convert_encoding($addr, "UTF-8")."\n";
				$uid = mb_replace($uid,
						  mb_convert_encoding($addr,
								      "UTF-8"),
						  "V_A_L_I_D__E_M_A_I_L");
			}
			//print "$uid\n";
			$uid = mb_replace($uid,
					  mb_convert_encoding($user['lname'],
							      "UTF-8"),
					  "L_N_A_M_E");
			$uid = mb_replace($uid,
					  mb_convert_encoding($user['fname'],
							      "UTF-8"),
					  "F_N_A_M_E");
			$uid = mb_replace($uid,
					  mb_convert_encoding($user['mname'],
							      "UTF-8"),
					  "M_N_A_M_E");
			$uid = mb_replace($uid,
					  mb_convert_encoding($user['suffix'],
							      "UTF-8"),
					  "S_U_F_F_I_X");
			$uid = mb_replace($uid, deUmlaut($user['lname']),
					  "L_N_A_M_E__U_M_L_A_U_T");
			$uid = mb_replace($uid, deUmlaut($user['fname']),
					  "F_N_A_M_E__U_M_L_A_U_T");
			$uid = mb_replace($uid, deUmlaut($user['mname']),
					  "M_N_A_M_E__U_M_L_A_U_T");
			$uid = mb_replace($uid, deUmlaut($user['suffix']),
					  "S_U_F_F_I_X__U_M_L_A_U_T");
//print deUmlaut2($user['lname'])."\n";
			$uid = mb_replace($uid, deUmlaut2($user['lname']),
					  "L_N_A_M_E__U_M_L_A_U_T");
			$uid = mb_replace($uid, deUmlaut2($user['fname']),
					  "F_N_A_M_E__U_M_L_A_U_T");
			$uid = mb_replace($uid, deUmlaut2($user['mname']),
					  "M_N_A_M_E__U_M_L_A_U_T");
			$uid = mb_replace($uid, deUmlaut2($user['suffix']),
					  "S_U_F_F_I_X__U_M_L_A_U_T");
			if (strlen($user['mname']) > 0) {
				$uid = mb_replace($uid,
						  mb_convert_encoding(substr($user['mname'], 0, 1) . ".",
								      "UTF-8"),
						  "M_N_A_M_E__I_N_I_T_I_A_L");
			}
			if (strlen($user['fname']) > 0) {
				$uid = mb_replace($uid,
						  mb_convert_encoding(substr($user['fname'], 0, 1) . ".",
								      "UTF-8"),
						  "F_N_A_M_E__I_N_I_T_I_A_L");
			}
			$nameRegEx = "^ (:F_N_A_M_E([^:]*):\s+"
				     . "(:M_N_A_M_E([^:]*):\s+)?"
				     . ":L_N_A_M_E([^:]*):"
				     . "(\s+:S_U_F_F_I_X([^:]*):)?)?"
				     . "(\s*\(.*\))?" // optional comment
				     . "(\s*<?:V_A_L_I_D__E_M_A_I_L([^:]*):>?)?"
				     . "\$";
			if (!mb_ereg_search_init($uid, $nameRegEx)) {
				$problem .= ":REGEX_FAILED:";
			}
			$res = mb_ereg_search_regs();
			if ($res) {
				$match = true;
				if ($res[8] != "") {
					$problem .= ":UNPARSED_COMMENT:";
				}
				if ($res[2] != "") {
					$problem .= ":".$res[2].":";
				}
				if ($res[4] != "") {
					$problem .= ":".$res[4].":";
				}
				if ($res[5] != "") {
					$problem .= ":".$res[5].":";
				}
				if ($res[7] != "") {
					$problem .= ":".$res[7].":";
				}
//print $res[0].",".$res[1].",".$res[2].",".$res[3].",".$res[4].","
//     .$res[5].",".$res[6].",".$res[7].",".$res[8].",".$res[9].","
//     .$res[10]."\n";
			} else {
				$problem = ":MISMATCH:$problem";
			}
			if (!$match || $problem != "") {
				print $key[0] . "," . csvize($problem) . ","
				      . csvize($uid) . ","
				      . csvize(preg_replace('/([^ -~])/ei', '"\\\\x".sprintf("%02x", ord("\\1"))', $bits[9])) . ","
				      . mb_convert_encoding(csvize($user['fname']), "UTF-8") . ","
				      . mb_convert_encoding(csvize($user['mname']), "UTF-8") . ","
				      . mb_convert_encoding(csvize($user['lname']), "UTF-8") . ","
				      . mb_convert_encoding(csvize($user['suffix']), "UTF-8");
				foreach ($addrs as $addr) {
					print "," . mb_convert_encoding(csvize($addr), "UTF-8");
				}
				print "\n";
			}
		}
	}

?>
