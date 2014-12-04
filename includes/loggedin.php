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

	include_once("../includes/lib/general.php");
	require_once("../includes/lib/l10n.php");
	include_once("../includes/mysql.php");
	require_once('../includes/notary.inc.php');

	if(!isset($_SESSION['profile']) || !is_array($_SESSION['profile'])) {
		$_SESSION['profile'] = array( 'id' => 0, 'loggedin' => 0 );
	}
	if(!isset($_SESSION['profile']['id']) || !isset($_SESSION['profile']['loggedin'])) {
		$_SESSION['profile']['id'] = 0;
		$_SESSION['profile']['loggedin'] = 0;
	}

	if($_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'] && $_SESSION['profile']['id'] > 0 && $_SESSION['profile']['loggedin'] != 0)
	{
		$uid = $_SESSION['profile']['id'];
		$_SESSION['profile']['loggedin'] = 0;
		$_SESSION['profile'] = "";
		foreach($_SESSION as $key => $value)
		{
			if($key == '_config' || $key == 'mconn' || 'csrf_' == substr($key, 0, 5))
				continue;
			if(is_int($key) || is_string($key))
				unset($_SESSION[$key]);
			unset($$key);
			//session_unregister($key);
		}

		$_SESSION['profile'] = mysql_fetch_assoc(mysql_query("select * from `users` where `id`='".intval($uid)."'"));
		if($_SESSION['profile']['locked'] == 0)
			$_SESSION['profile']['loggedin'] = 1;
		else
			unset($_SESSION['profile']);
	}

	if($_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'] && ($_SESSION['profile']['id'] == 0 || $_SESSION['profile']['loggedin'] == 0))
	{
		$user_id = get_user_id_from_cert($_SERVER['SSL_CLIENT_M_SERIAL'],
				$_SERVER['SSL_CLIENT_I_DN_CN']);

		if($user_id >= 0)
		{
			$_SESSION['profile']['loggedin'] = 0;
			$_SESSION['profile'] = "";
			foreach($_SESSION as $key => $value)
			{
				if($key == '_config' || $key == 'mconn' || 'csrf_' == substr($key, 0, 5))
					continue;
				if(is_int($key) || is_string($key))
					unset($_SESSION[$key]);
				unset($$key);
				//session_unregister($key);
			}

			$_SESSION['profile'] = mysql_fetch_assoc(mysql_query(
					"select * from `users` where `id`='".intval($user_id)."'"));
			if($_SESSION['profile']['locked'] == 0)
				$_SESSION['profile']['loggedin'] = 1;
			else
				unset($_SESSION['profile']);
		} else {
			$_SESSION['profile']['loggedin'] = 0;
			$_SESSION['profile'] = "";
			foreach($_SESSION as $key => $value)
			{
				if($key == '_config' || $key == 'mconn' || 'csrf_' == substr($key, 0, 5))
					continue;
				unset($_SESSION[$key]);
				unset($$key);
				//session_unregister($key);
			}

			$_SESSION['_config']['oldlocation'] = $_SERVER['REQUEST_URI'];
			header("Location: https://{$_SESSION['_config']['securehostname']}/index.php?id=4");
			exit;
		}
	}

	if($_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'] && ($_SESSION['profile']['id'] <= 0 || $_SESSION['profile']['loggedin'] == 0))
	{
		header("Location: https://{$_SESSION['_config']['normalhostname']}");
		exit;
	}

	if($_SERVER['HTTP_HOST'] == $_SESSION['_config']['securehostname'] && $_SESSION['profile']['id'] > 0 && $_SESSION['profile']['loggedin'] > 0)
	{
		$query = "select sum(`points`) as `total` from `notary` where `to`='".intval($_SESSION['profile']['id'])."' and `deleted` = 0 group by `to`";
		$res = mysql_query($query);
		$row = mysql_fetch_assoc($res);
		$_SESSION['profile']['points'] = $row['total'];

		if($_SESSION['profile']['language'] == "")
		{
			$query = "update `users` set `language`='".L10n::get_translation()."'
							where `id`='".intval($_SESSION['profile']['id'])."'";
			mysql_query($query);
		} else {
			L10n::set_translation($_SESSION['profile']['language']);
			L10n::init_gettext();
		}
	}

	if(array_key_exists("id",$_REQUEST) && $_REQUEST['id'] == "logout")
	{
		$normalhost=$_SESSION['_config']['normalhostname'];
		$_SESSION['profile']['loggedin'] = 0;
		$_SESSION['profile'] = "";
		foreach($_SESSION as $key => $value)
		{
			unset($_SESSION[$key]);
			unset($$key);
			//session_unregister($key);
		}

		header("Location: https://{$normalhost}/index.php");
		exit;
	}

	if($_SESSION['profile']['loggedin'] < 1)
	{
		$_SESSION['_config']['oldlocation'] = $_SERVER['REQUEST_URI'];
		header("Location: https://{$_SERVER['HTTP_HOST']}/index.php?id=4");
		exit;
	}

	if (!isset($_SESSION['profile']['ccaagreement']) || !$_SESSION['profile']['ccaagreement']) {
		$_SESSION['profile']['ccaagreement']=get_user_agreement_status($_SESSION['profile']['id'],'CCA');
		if (!$_SESSION['profile']['ccaagreement']) {
			$_SESSION['_config']['oldlocation'] = $_SERVER['REQUEST_URI'];
			header("Location: https://{$_SERVER['HTTP_HOST']}/index.php?id=52");
			exit;
		}
	}
?>
