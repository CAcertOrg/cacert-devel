<? /*
    LibreSSL - CAcert web application
    Copyright (C) 2004-2011  CAcert Inc.

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

/**
 * Checks if the user may log in and retrieve the user id
 * 
 * Usually called with $_SERVER['SSL_CLIENT_M_SERIAL'] and
 * 	$_SERVER['SSL_CLIENT_I_DN_CN']
 * 
 * @param $serial string
 * 	usually $_SERVER['SSL_CLIENT_M_SERIAL']
 * @param $issuer_cn string
 * 	usually $_SERVER['SSL_CLIENT_I_DN_CN']
 * @return int
 * 	the user id, -1 in case of error
 */
function get_user_id_from_cert($serial, $issuer_cn)
{
	$query = "select `id` from `emailcerts` where
			`serial`='".mysql_escape_string($serial)."' and
			`rootcert`= (select `id` from `root_certs` where
				`Cert_Text`='".mysql_escape_string($issuer_cn)."') and
			`revoked`=0 and disablelogin=0 and
			UNIX_TIMESTAMP(`expire`) - UNIX_TIMESTAMP() > 0";
	$res = mysql_query($query);
	if(mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_assoc($res);
		return intval($row['id']);
	}
	
	return -1;
}

?>
