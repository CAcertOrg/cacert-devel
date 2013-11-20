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
	$query = "select `memid` from `emailcerts` where
			`serial`='".mysql_real_escape_string($serial)."' and
			`rootcert`= (select `id` from `root_certs` where
				`Cert_Text`='".mysql_real_escape_string($issuer_cn)."') and
			`revoked`=0 and disablelogin=0 and
			UNIX_TIMESTAMP(`expire`) - UNIX_TIMESTAMP() > 0";
	$res = mysql_query($query);
	if(mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_assoc($res);
		return intval($row['memid']);
	}

	return -1;
}

/**
 * Produces a log entry with the error message with log level E_USER_WARN
 * and a random ID an returns a message that can be displayed to the user
 * including the generated ID
 *
 * @param $errormessage string
 * 		The error message that should be logged
 * @return string containing the generated ID that can be displayed to the
 * 		user
 */
function failWithId($errormessage) {
	$errorId = rand();
	trigger_error("$errormessage. ID: $errorId", E_USER_WARNING);
	return sprintf(_("Something went wrong when processing your request. ".
				"Please contact %s for help and provide them with the ".
				"following ID: %d"),
			"<a href='mailto:support@cacert.org?subject=System%20Error%20-%20".
				"ID%3A%20$errorId'>support@cacert.org</a>",
	$errorId);
}


/**
 * Runs a command on the shell and return it's exit code and output
 *
 * @param string $command
 * 		The command to run. Make sure that you escapeshellarg() any non-constant
 * 		parts as this is executed on a shell!
 * @param string|bool $input
 * 		The input that is passed to the command via STDIN, if true the real
 * 		STDIN is passed through
 * @param string|bool $output
 * 		The output the command wrote to STDOUT (this is passed as reference),
 * 		if true the output will be written to the real STDOUT. Output is ignored
 * 		by default
 * @param string|bool $errors
 * 		The output the command wrote to STDERR (this is passed as reference),
 * 		if true (default) the output will be written to the real STDERR
 *
 * @return int|bool
 * 		The exit code of the command, true if the execution of the command
 * 		failed (true because then
 * 		<code>if (runCommand('echo "foo"')) handle_error();</code> will work)
 */
function runCommand($command, $input = "", &$output = null, &$errors = true) {
	$descriptorspec = array();

	if ($input !== true) {
		$descriptorspec[0] = array("pipe", "r"); // STDIN for child
	}

	if ($output !== true) {
		$descriptorspec[1] = array("pipe", "w"); // STDOUT for child
	}

	if ($errors !== true) {
		$descriptorspec[2] = array("pipe", "w"); // STDERR for child
	}

	$proc = proc_open($command, $descriptorspec, $pipes);

	if (is_resource($proc))
	{
		if ($input !== true) {
			fwrite($pipes[0], $input);
			fclose($pipes[0]);
		}

		if ($output !== true) {
			$output = stream_get_contents($pipes[1]);
		}

		if ($errors !== true) {
			$errors = stream_get_contents($pipes[2]);
		}

		return proc_close($proc);

	} else {
		return true;
	}
}

  	// returns 0 if $userID is an Assurer
	// Otherwise :
	//	 Bit 0 is always set
	//	 Bit 1 is set if 100 Assurance Points are not reached
	//	 Bit 2 is set if Assurer Test is missing
	//	 Bit 3 is set if the user is not allowed to be an Assurer (assurer_blocked > 0)
	function get_assurer_status($userID)
	{
		$Result = 0;
		$query = mysql_query('SELECT * FROM `cats_passed` AS `tp`, `cats_variant` AS `cv` '.
			'  WHERE `tp`.`variant_id` = `cv`.`id` AND `cv`.`type_id` = 1 AND `tp`.`user_id` = \''.(int)intval($userID).'\'');
		if(mysql_num_rows($query) < 1)
		{
			$Result |= 5;
		}

		$query = mysql_query('SELECT SUM(`points`) AS `points` FROM `notary` AS `n` WHERE `n`.`to` = \''.(int)intval($userID).'\' AND `n`.`expire` < now() and `deleted` = 0');
		$row = mysql_fetch_assoc($query);
		if ($row['points'] < 100) {
			$Result |= 3;
		}

		$query = mysql_query('SELECT `assurer_blocked` FROM `users` WHERE `id` = \''.(int)intval($userID).'\'');
		$row = mysql_fetch_assoc($query);
		if ($row['assurer_blocked'] > 0) {
			$Result |= 9;
		}

		return $Result;
	}
