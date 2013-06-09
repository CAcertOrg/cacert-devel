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

// Comment (to be romeved): better to disable shot open tags in php.ini

/*
   cats_import.php 
   
   API for CATS to import passed tests into main CAcert database.
*/

require_once('../../includes/lib/account.php');

function sanitize_string($buffer) {
 return htmlentities(utf8_decode($buffer), (int)ENQ_QUOTES);
}

define ('UNDEFINED', 'nd');
// Specific for testserver: Accept Test-CATS-Server
define ('ALLOWED_IP', '192.109.159.27');
//define ('ALLOWED_IP', '213.154.225.243');
define ('ALLOWED_IP2', '192.109.159.28');
define ('CONFIG_FILEPATH', '/www/');

$remote_addr = (isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:UNDEFINED;
$server_name = (isset($_SERVER['SERVER_NAME']))?$_SERVER['SERVER_NAME']:UNDEFINED;
$https = (isset($_SERVER['HTTPS']))?$_SERVER['HTTPS']:UNDEFINED;
$ssl_client_s_dn = (isset($_SERVER['SSL_CLIENT_S_DN']))?$_SERVER['SSL_CLIENT_S_DN']:UNDEFINED;

$access = FALSE;

// Access only from CATS.cacert.org with a client certificate for cats@cacert.org
if (
 ($remote_addr == ALLOWED_IP || $remote_addr == ALLOWED_IP2)  &&
 $https == 'on' && 
 // Comment (to be romeved): better to use preg_match matching the end of the line (since this is on the end of the line right?)
 // Ted: Is this specified? I don't think so, therefore I'd keep stristr
 strlen(stristr($ssl_client_s_dn, '/emailAddress=cats@cacert.org')) > 0
) $access = TRUE;

if ($access !== TRUE) {
 echo 'UNAUTHORIZED ACCESS<br>'."\r\n";
 echo 'IP: '.sanitize_string($remote_addr).'<br>'."\r\n";
 echo 'Server: '.sanitize_string($server_name).'<br>'."\r\n";
 echo 'HTTPS: '.sanitize_string($https).'<br>'."\r\n";
 echo 'Client cert: '.sanitize_string($ssl_client_s_dn).'<br>'."\r\n";
 trigger_error('Unauthorized access: ip('.$remote_addr.') server('.$server_name.') https('.$https.') cert('.$ssl_client_s_dn.')', E_USER_ERROR);
 exit();
}

// Comment (to be romeved): do you we session autostart in php.ini??
// Ted: Sessions are quite meaningless for me since the upload protocol is stateless. Should session_start be called nevertheless?
session_start();

require_once(CONFIG_FILEPATH.'includes/mysql.php');

// Comment (to be romeved): dunno the difference between stripslashes and stripcslashes
// manual is iunclear too, please make sure there are no decoding issues
// Ted: I just used it here because I saw it elsewhere and it seems to work. Would you prefer stripslashes?
if (get_magic_quotes_gpc()) {
 $serial = stripcslashes($_POST['serial']);
 $root = stripcslashes($_POST['root']);
 $type = stripcslashes($_POST['type']);
 $variant = stripcslashes($_POST['variant']);
 $date = stripcslashes($_POST['date']);
} else {
 $serial = $_POST['serial'];
 $root = $_POST['root'];
 $type = $_POST['type'];
 $variant = $_POST['variant'];
 $date = $_POST['date'];
}
  
// Explicitly select all those IDs so I can insert new rows if needed.
$query = mysql_query('SELECT `id` FROM `cats_type` WHERE `type_text` = \''.mysql_real_escape_string($type).'\';');
if (!$query) {
  echo 'Invalid query'."\r\n";
  trigger_error('Invalid query', E_USER_ERROR);
  exit();
}

if (mysql_num_rows($query) > 0) {
  $result = mysql_fetch_array($query);
  $typeID = $result['0'];
} else {
  $query = mysql_query('INSERT INTO `cats_type` (`type_text`) VALUES (\''.mysql_real_escape_string($type).'\');');
  if (!$query) {
    echo 'Invalid query'."\r\n";
    trigger_error('Invalid query', E_USER_ERROR);
    exit();
  }

  $typeID = mysql_insert_id();
}

$query = mysql_query('SELECT `id` FROM `cats_variant` WHERE `type_id` = \''.(int)intval($typeID).'\' AND `test_text` = \''.mysql_real_escape_string($variant).'\';');
if (!$query) {
  echo 'Invalid query'."\r\n";
  trigger_error('Invalid query', E_USER_ERROR);
  exit();
}

if (mysql_num_rows($query) > 0) {
  $result = mysql_fetch_array($query);
  $variantID = $result['0'];
} else {
  $query = mysql_query('INSERT INTO `cats_variant` (`type_id`, `test_text`) VALUES (\''.(int)intval($typeID).'\', \''.mysql_real_escape_string($variant).'\');');
  if (!$query) {
    echo 'Invalid query'."\r\n";
    trigger_error('Invalid query', E_USER_ERROR);
    exit();
  }

  $variantID = mysql_insert_id();
}

// Now find the userid from cert serial
$query = mysql_query('SELECT `ec`.`memid` FROM `emailcerts` AS `ec`, `root_certs` AS `rc` WHERE `ec`.`rootcert` = `rc`.`id` AND `ec`.`serial` = \''.mysql_real_escape_string($serial).'\' AND `rc`.`cert_text` = \''.mysql_real_escape_string($root).'\';');
if (!$query) {
  echo 'Invalid query'."\r\n";
  trigger_error('Invalid query', E_USER_ERROR);
  exit();
}

if (mysql_num_rows($query) > 0) {
  $result = mysql_fetch_array($query);
  $userID = $result['0'];
} else {
  echo 'Cannot find cert '.sanitize_string($serial).' / '.sanitize_string($root)."\r\n";
  // Let's treat this as an error, since it should not happen.
  trigger_error('Cannot find cert '.$serial.' / '.$root.'!'.mysql_error(), E_USER_ERROR);
  exit();
}

// The unique constraint on cats_passed assures that records are not stored multiply
$query = mysql_query('INSERT INTO `cats_passed` (`user_id`, `variant_id`, `pass_date`) VALUES (\''.(int)intval($userID).'\', \''.(int)intval($variantID).'\', \''.mysql_real_escape_string($date).'\');');
if (!$query) {
  if (mysql_errno() != 1062) { // Duplicate Entry is considered success
    echo 'Invalid query'."\r\n";
    trigger_error('Invalid query', E_USER_ERROR);
    exit();
  }
}

// Update Assurer-Flag on users table if 100 points. Should the number of points be SUM(points) or SUM(awarded)?
if (!fix_assurer_flag($userID)) {
  echo 'Invalid query'."\r\n";
  trigger_error('Invalid query', E_USER_ERROR);
  exit();
}

echo 'OK'."\r\n";    

?>
