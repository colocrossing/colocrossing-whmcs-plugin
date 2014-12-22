<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A WHMCS Client
 */
class ColoCrossing_Model_Client extends ColoCrossing_Model_User {

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblclients';

	/**
	 * Retrieves the Type of the User
	 * @param  boolean $human_readable 	Specifies the format of the type requested
	 * @return string|integer 			The Type as a human readable string if $human_readable, else the
	 *                            	  	 	type as an integer to be used in the db
	 */
	public function getType($human_readable = false) {
		return $human_readable ? 'Client' : 2;
	}

}
