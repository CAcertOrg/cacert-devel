<?php
/*
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

/**
 * Function to recalculate the cached Assurer status
 *
 * @param int $userID
 * 	if the user ID is not given the flag will be recalculated for all users
 *
 * @return bool
 * 	false if there was an error on fixing the flag. This does NOT return the
 * 	new value of the flag
 */
function fix_assurer_flag($userID = NULL)
{
	// Update Assurer-Flag on users table if 100 points and CATS passed.
	//
	// We may have some performance issues here if no userID is given
	// there are ~150k assurances and ~220k users currently
	// but the exists-clause on cats_passed should be a good filter
	$sql = '
		UPDATE `users` AS `u` SET `assurer` = 1
		WHERE '.(
					($userID === NULL) ?
					'`u`.`assurer` = 0' :
					'`u`.`id` = \''.intval($userID).'\''
				).'
			AND EXISTS(
				SELECT 1 FROM `cats_passed` AS `cp`, `cats_variant` AS `cv`
				WHERE `cp`.`variant_id` = `cv`.`id`
					AND `cv`.`type_id` = 1
					AND `cp`.`user_id` = `u`.`id`
			)
			AND (
				SELECT SUM(`points`) FROM `notary` AS `n`
				WHERE `n`.`to` = `u`.`id`
					AND (`n`.`expire` > now()
					     OR `n`.`expire` IS NULL)
					AND `n`.`deleted` = 0
			) >= 100';

	$query = mysql_query($sql);
	if (!$query) {
		return false;
	}
	// Challenge has been passed and non-expired points >= 100

	// Reset flag if requirements are not met
	//
	// Also a bit performance critical but assurer flag is only set on
	// ~5k accounts
	$sql = '
		UPDATE `users` AS `u` SET `assurer` = 0
		WHERE '.(
					($userID === NULL) ?
					'`u`.`assurer` <> 0' :
					'`u`.`id` = \''.intval($userID).'\''
				).'
			AND (
				NOT EXISTS(
					SELECT 1 FROM `cats_passed` AS `cp`,
						`cats_variant` AS `cv`
					WHERE `cp`.`variant_id` = `cv`.`id`
						AND `cv`.`type_id` = 1
						AND `cp`.`user_id` = `u`.`id`
				)
				OR (
					SELECT SUM(`points`) FROM `notary` AS `n`
					WHERE `n`.`to` = `u`.`id`
						AND (
							`n`.`expire` > now()
							OR `n`.`expire` IS NULL
						)
						AND `n`.`deleted` = 0
				) < 100
			)';

	$query = mysql_query($sql);
	if (!$query) {
		return false;
	}

	return true;
}


/**
 * Supported hash algorithms for signing certificates
 */
class HashAlgorithms {
	/**
	 * Default hash algorithm identifier for signing
	 * @var string
	 */
	public static $default = 'sha256';

	/**
	 * Get display strings for the supported hash algorithms
	 * @return array(string=>array('name'=>string, 'info'=>string))
	 *     - [$hash_identifier]['name'] = Name that should be displayed in UI
	 *     - [$hash_identifier]['info'] = Additional information that can help
	 *       with the selection of a suitable algorithm
	 */
	public static function getInfo() {
		return array(
				'sha256' => array(
						'name' => 'SHA-256',
						'info' => _('Currently recommended, because the other algorithms might break on some older versions of the GnuTLS library (older than 3.x) still shipped in Debian for example.'),
					),
				'sha384' => array(
						'name' => 'SHA-384',
						'info' => '',
					),
				'sha512' => array(
						'name' => 'SHA-512',
						'info' => _('Highest protection against hash collision attacks of the algorithms offered here.'),
					),
			);
	}

	/**
	 * Check if the input is a supported hash algorithm identifier otherwise
	 * return the identifier of the default hash algorithm
	 *
	 * @param string $hash_identifier
	 * @return string The cleaned identifier
	 */
	public static function clean($hash_identifier) {
		if (array_key_exists($hash_identifier, self::getInfo() )) {
			return $hash_identifier;
		} else {
			return self::$default;
		}
	}
}
