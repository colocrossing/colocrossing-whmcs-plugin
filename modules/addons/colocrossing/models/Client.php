<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require_once 'Event.php';

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

	/**
	 * Determines if this Client Has Access to the specified Device
	 * @param  integer|ColoCrossing_Object_Device  $device The Device or Id
	 * @return boolean  True if the Client has Access
	 */
	public function hasPermissionForDevice($device) {
		$device_id = is_numeric($device) ? $device : $device->getId();
		$service = ColoCrossing_Model_Service::findByDevice($device_id);

		return isset($service) && $service->isActive();
	}

	/**
     * Logs Message to Events
     * @param  string $description
     */
    public static function log($description = ''){
        ColoCrossing_Model_Event::log('admin', $description);
    }

}
