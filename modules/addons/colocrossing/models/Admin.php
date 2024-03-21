<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require_once 'Event.php';

/**
 * A WHMCS Admin User
 */
class ColoCrossing_Model_Admin extends ColoCrossing_Model_User {

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tbladmins';

	/**
	 * Retrieves the Type of the User
	 * @param  boolean $human_readable 	Specifies the format of the type requested
	 * @return string|integer 			The Type as a human readable string if $human_readable, else the
	 *                            	  	 	type as an integer to be used in the db
	 */
	public function getType($human_readable = false) {
		return $human_readable ? 'Admin' : 1;
	}

	/**
	 * Determines if this User Has Access to the specified Device
	 * @param  integer|ColoCrossing_Object_Device  $device The Device or Id
	 * @return boolean  True if the User has Access
	 */
	public function hasPermissionForDevice($device) {
		return !empty($device);
	}

	/**
     * Logs Message to Events
     * @param  string $description
     */
    public static function log($description = ''){
        ColoCrossing_Model_Event::log('admin', $description);
    }

}
