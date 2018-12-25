<?php
/**
 * Created by PhpStorm.
 * User: bdmc
 * Date: 25/12/18
 * Time: 11:57 AM
 */

/**
 * LibreSSL - CAcert web application
 * Copyright (C) 2004-2019  CAcert Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Class CAcertSetup
 *
 * General initialisation routines
 *
 *
 */
class CAcertSetup
{
	/**
	 * PageLoadTime_Start ( time the page )
	 *
	 * @var int $pageLoadTime_Start
	 */
	var $pageLoadTime_Start;

	/**
	 * How an Assurance Meeting is conducted:  Array
	 *
	 * @var array $junk
	 */
	var $junk;

	/**
	 * Page ID passed from browser
	 *
	 * @var int $id
	 */
	var $id;

	/**
	 * Old ( saved ) Page ID passed from browser
	 */
	var $oldid;


	/**
	 * CAcertSetup constructor.
	 */
	public function __construct()
	{
		session_name( "cacert" );
		session_start();

		$this->pageLoadTime_Start = microtime( true );

		$this->junk = array( _( "Face to Face Meeting" ), _( "Trusted Third Parties" ), _( "Thawte Points Transfer" ), _( "Administrative Increase" ),
			_( "CT Magazine - Germany" ), _( "Temporary Increase" ), _( "Unknown" ) );


		$_SESSION[ '_config' ][ 'filepath' ] = $CAcertConfig->base_filepath;

		if ( array_key_exists( 'HTTP_HOST', $_SERVER ) &&
			$_SERVER[ 'HTTP_HOST' ] != $_SESSION[ '_config' ][ 'normalhostname' ] &&
			$_SERVER[ 'HTTP_HOST' ] != $_SESSION[ '_config' ][ 'securehostname' ] &&
			$_SERVER[ 'HTTP_HOST' ] != $_SESSION[ '_config' ][ 'tverify' ] &&
			$_SERVER[ 'HTTP_HOST' ] != "stamp.cacert.org" ) {
			if ( array_key_exists( 'HTTPS', $_SERVER ) && $_SERVER[ 'HTTPS' ] == "on" )
				header( "location: https://" . $_SESSION[ '_config' ][ 'normalhostname' ] );
			else
				header( "location: http://" . $_SESSION[ '_config' ][ 'normalhostname' ] );
			exit;
		}

		if ( array_key_exists( 'HTTP_HOST', $_SERVER ) &&
			($_SERVER[ 'HTTP_HOST' ] == $_SESSION[ '_config' ][ 'securehostname' ] ||
				$_SERVER[ 'HTTP_HOST' ] == $_SESSION[ '_config' ][ 'tverify' ]) ) {
			if ( array_key_exists( 'HTTPS', $_SERVER ) && $_SERVER[ 'HTTPS' ] == "on" ) {
			}
			else {
				if ( $_SERVER[ 'HTTP_HOST' ] == $_SESSION[ '_config' ][ 'securehostname' ] )
					header( "location: https://" . $_SESSION[ '_config' ][ 'securehostname' ] );
				if ( $_SERVER[ 'HTTP_HOST' ] == $_SESSION[ '_config' ][ 'tverify' ] )
					header( "location: https://" . $_SESSION[ '_config' ][ 'tverify' ] );
				exit;
			}
		}

		L10n::detect_language();
		L10n::init_gettext();

		if ( array_key_exists( 'profile', $_SESSION ) && is_array( $_SESSION[ 'profile' ] ) && array_key_exists( 'id', $_SESSION[ 'profile' ] ) && $_SESSION[ 'profile' ][ 'id' ] > 0 ) {
			$locked = mysql_fetch_assoc( mysql_query( "select `locked` from `users` where `id`='" . intval( $_SESSION[ 'profile' ][ 'id' ] ) . "'" ) );
			if ( $locked[ 'locked' ] == 0 ) {
				$query                             = "select sum(`points`) as `total` from `notary` where `to`='" . intval( $_SESSION[ 'profile' ][ 'id' ] ) . "' and `deleted` = 0 group by `to`";
				$res                               = mysql_query( $query );
				$row                               = mysql_fetch_assoc( $res );
				$_SESSION[ 'profile' ][ 'points' ] = $row[ 'total' ];
			}
			else {
				$_SESSION[ 'profile' ] = "";
				unset( $_SESSION[ 'profile' ] );
			}
		}


	}
}