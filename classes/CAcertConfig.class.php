<?php
/**
 * Created by PhpStorm.
 * User: bdmc
 * Date: 03/12/18
 * Time: 10:19 PM
 */

class CAcertConfig
{
	/**
	 * @var string
	 * @private
	 */
	var $testmode ;

	/**
	 * @var string
	 * @private
	 */
	var $dbtype ;

	/**
	 * @var string
	 * @private
	 */
	var $dbhost ;

	/**
	 * @var string
	 * @private
	 */
	var $dbuser ;

	/**
	 * @var string
	 * @private
	 */
	var $dbpass ;

	/**
	 * @var string
	 * @private
	 */
	var $dbname ;

	/**
	 * @var string
	 * @private
	 */
	var $normalhostname ;

	/**
	 * @var string
	 * @private
	 */
	var $securehostname ;

	/**
	 * @var string
	 * @private
	 */
	var $tverify ;


	/**
	 * Default INI File Name
	 *
	 * @var string
	 * @private
	 */
	var $ini_file_name = "../cacert-config.ini" ;


	/**
	 * Internal copy of the INI file array
	 *
	 * @var array
	 * @private
	 */
	var $_values ;


	/**
	 * Base Filepath for various Includes and Requires
	 *
	 * @var string $base_filepath
	 */
	var $base_filepath ;



	/**
	 * CAcertConfig constructor.
	 */
	function __construct()
	{

		if ( is_file( $this->ini_file_name )) {
			$ini_array = parse_ini_file( $this->ini_file_name, false, INI_SCANNER_TYPED ) ;

			if ( $ini_array !== false ) {
				/*
				 * parse the INI file with extreme paranoia
				 */
				if ( in_array( 'base_filepath', $ini_array )) {
					$this->base_filepath = $ini_array[ 'base_filepath'] ;
				}
				if ( in_array( 'testmode', $ini_array )) {
					$this->testmode = $ini_array[ 'testmode'] ;
				}
				if ( in_array( 'dbtype', $ini_array )) {
					$this->dbtype = $ini_array[ 'dbtype'] ;
				}
				if ( in_array( 'dbhost', $ini_array )) {
					$this->dbhost = $ini_array[ 'dbhost'] ;
				}
				if ( in_array( 'dbuser', $ini_array )) {
					$this->dbuser = $ini_array[ 'dbuser'] ;
				}
				if ( in_array( 'dbpass', $ini_array )) {
					$this->dbpass = $ini_array[ 'dbpass'] ;
				}
				if ( in_array( 'normalhostname', $ini_array )) {
					$this->normalhostname = $ini_array[ 'normalhostname'] ;
				}
				if ( in_array( 'securehostname', $ini_array )) {
					$this->securehostname = $ini_array[ 'securehostname'] ;
				}
				if ( in_array( 'tverify', $ini_array )) {
					$this->tverify = $ini_array[ 'tverify'] ;
				}

				/*
				 * If "testmode" is not set in the configuration file,
				 * assume that it is Test.
				 *
				 * Other variables do not have default settings.
				 */
				if ( strlen( $this->testmode ) == 0 ) {
					$this->testmode = "test" ;
				}
			} else {
				$this->base_filepath = "/www" ;
				$this->testmode = "test" ;
				$this->dbtype = "mysqli" ;
				$this->dbhost = "127.0.0.1" ;
				$this->dbuser = "username" ;
				$this->dbpass = "password" ;
				$this->normalhostname = "test.cacert.org" ;
				$this->securehostname = "test.cacert.org" ;
				$this->tverify = "test.cacert.org" ;
			}
		}

	}

	/**
	 * Return the value of a field
	 *
	 * @param string $key the variable to set
	 * @return mixed the value of the variable
	 */
	function value($key) {
		if (isset ($this->_values[$key])) {
			return ($this->_values[$key]);
		} else {
			return ($this->$key);
		}
	}

	/**
	 * Set the value of a field in this object
	 *
	 * @param string $key the field to assign a value
	 * @param mixed $value the value to assign
	 */
	function set_value($key, $value) {
		$this->_values[$key] = $value;
		$this->$key = $value;
	}

	/**
	 * Static wrapper method for retrieving configuration Singleton
	 */
	static function &get_instance() {
		$config = new CAcertConfig;
		return $config;
	}

}