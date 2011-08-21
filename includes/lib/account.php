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

function fix_assurer_flag($userID)
{
	// Update Assurer-Flag on users table if 100 points.
	// Should the number of points be SUM(points) or SUM(awarded)?
	$query = mysql_query('UPDATE `users` AS `u` SET `assurer` = 1 WHERE '.
		'`u`.`id` = \''.(int)intval($userID).'\' AND '.
		'EXISTS(SELECT 1 FROM `cats_passed` AS `cp`, `cats_variant` AS `cv` '.
			'WHERE `cp`.`variant_id` = `cv`.`id` AND `cv`.`type_id` = 1 AND '.
			'`cp`.`user_id` = `u`.`id`) AND '.
		'(SELECT SUM(`points`) FROM `notary` AS `n` WHERE `n`.`to` = `u`.`id` '.
			'AND (`n`.`expire` > now() OR `n`.`expire` IS NULL)) >= 100');
	// Challenge has been passed and non-expired points >= 100
	
	if (!$query) {
		return false;
	}
 
	// Reset flag if requirements are not met
	$query = mysql_query('UPDATE `users` AS `u` SET `assurer` = 0 WHERE '.
		'`u`.`id` = \''.(int)intval($userID).'\' AND '.
		'(NOT EXISTS(SELECT 1 FROM `cats_passed` AS `cp`, `cats_variant` AS '.
			'`cv` WHERE `cp`.`variant_id` = `cv`.`id` AND `cv`.`type_id` = 1 '.
			'AND `cp`.`user_id` = `u`.`id`) OR '.
		'(SELECT SUM(`points`) FROM `notary` AS `n` WHERE `n`.`to` = `u`.`id` '.
			'AND (`n`.`expire` > now() OR `n`.`expire` IS NULL)) < 100)');
	
	if (!$query) {
		return false;
	}
	
	return true;
}