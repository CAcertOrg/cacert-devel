<?php
/**
 * Created by PhpStorm.
 * User: bdmc
 * Date: 25/11/18
 * Time: 10:59 PM
 */

class config
{

	/**
	 * The "regular" web site URL
	 *
	 * @var string
	 * @private
	 */
	var $normalhostname ;

	/**
	 * The "secure" web site URL
	 *
	 * @var string
	 * @private
	 */
	var $securehostname ;

	/**
	 * The "TVerify" host name
	 *
	 * @var string
	 * @private
	 */
	var $tverify ;

	/**
	 * The database user to login as
	 *
	 * @var string
	 * @private
	 */
	var $database_user;

	/**
	 * The database password to login with
	 *
	 * @var password
	 * @private
	 */
	var $database_password;

	/**
	 * The database host to connect to
	 *
	 * @var string
	 * @private
	 */
	var $database_host;

	/**
	 * The name of the database to connect to
	 *
	 * @var string
	 * @private
	 */
	var $database_name;

	/**
	 * The type of database we are connecting to
	 *
	 * @var string
	 * @private
	 */
	var $database_type;

	/**
	 * Enabled debugging at the database level (echo all queries)
	 *
	 * @var bool
	 * @private
	 */
	var $database_debug = false;

	/**
	 * Is this machine running in "Test" mode?
	 *
	 * @var boolean
	 * @private
	 */
	var $testmode ;

	/**
	 * A copy of the INI Values array
	 *
	 * @var array
	 * @private
	 */
	var $_values ;


	/**
	 * Return the value of a field
	 *
	 * @param string $key the variable to get
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
		$config = new config();
		return $config;
	}

	/**
	 * Constructor
	 */
	function __construct()
	{
		//
		// Default Testmode to True
		//
		// Overwrite with value in configuration file
		//
		define( TESTMODE, true ) ;

		$ini_values = parse_ini_file( "../cacert.ini", false, INI_SCANNER_TYPED ) ;
		if ( $ini_values === false ) {
			// generate error and report it

			$this->testmode = true ;
		} else {
			$this->database_debug = $ini_values[ 'database_debug'] ;
			$this->database_host = $ini_values[ 'database_host'] ;
			$this->database_name = $ini_values[ 'database_name'] ;
			$this->database_password = $ini_values[ 'database_password'] ;
			$this->database_type = $ini_values[ 'database_type'] ;
			$this->database_user = $ini_values[ 'database_user'] ;
			$this->normalhostname = $ini_values[ 'normalhostname'] ;
			$this->securehostname = $ini_values[ 'securehostname'] ;
			$this->tverify = $ini_values[ 'tverify'] ;
			$this->testmode = $ini_values[ 'testmode'] ;

			$this->_values = $ini_values ;
		}
	}


}
