<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Parent Class for a WHMCS User. Can be either a Admin or Client.
 */
abstract class ColoCrossing_Model_User extends ColoCrossing_Model {

	/**
	 * The User Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'firstname', 'lastname', 'email');

	/**
	 * Retrieves the Type of the User
	 * @param  boolean $human_readable 	Specifies the format of the type requested
	 * @return string|integer 			The Type as a human readable string if $human_readable, else the
	 *                            	  	 	type as an integer to be used in the db
	 */
	public abstract function getType($human_readable = false);

	/**
	 * @return string The First Name of the User
	 */
	public function getFirstName() {
		$first_name = $this->getValue('firstname');

		return empty($first_name) ? '' : $first_name;
	}

	/**
	 * @return string The Last Name of the User
	 */
	public function getLastName() {
		$last_name = $this->getValue('lastname');

		return empty($last_name) ? '' : $last_name;
	}

	/**
	 * @return string The Full Name of the User
	 */
	public function getFullName() {
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	/**
	 * @return string The Email
	 */
	public function getEmail() {
		$email = $this->getValue('email');

		return empty($email) ? '' : $email;
	}

	/**
	 * Determines if this User Has Access to the specified Device
	 * @param  integer|ColoCrossing_Object_Device  $device The Device or Id
	 * @return boolean  True if the User has Access
	 */
	public abstract function hasPermissionForDevice($device);

	/**
	 * Retrieves the User of the specified type and id
	 * @param integer $id The User ID
	 * @return ColoCrossing_Model_Admin|ColoCrossing_Model_Client|null|false The User, False if not found, Null if System.
	 */
	public static function getUser($id, $type) {
		$type = strtolower($type);

		switch ($type) {
			case 'admin':
			case 1:
				$admin = ColoCrossing_Model_Admin::find($id);
				return isset($admin) ? $admin : false;
			case 'client':
			case 2:
				$client = ColoCrossing_Model_Client::find($id);
				return isset($client) ? $client : false;
		}

		return null;
	}

	/**
	 * Retrieves the Currently Signed In User
	 * @param string|integer $type The Type of User to get (Admin|Client)
	 * @return ColoCrossing_Model_Admin|ColoCrossing_Model_Client|null|false The User, False if not found, Null if System.
	 */
	public static function getCurrentUser($type) {
		$type = strtolower($type);

		switch ($type) {
			case 'admin':
			case 1:
				return isset($_SESSION['adminid'])? self::getUser($_SESSION['adminid'], 1) : null;
			case 'client':
			case 2:
				return isset($_SESSION['uid'])? self::getUser($_SESSION['uid'], 2) : null;
		}

		return null;
	}

	/**
	 * Retrieves the Currently Signed In User
	 * @param string|integer $type The Type of User to get (Admin|Client)
	 * @return array(int, int) 	An array where the 1st element is the id and the 2nd
	 *                          	is the type
	 */
	public static function getCurrentUserId($type) {
		$current_user = self::getCurrentUser($type);

		if(empty($current_user)) {
			return array(0, 0);
		}

		return array($current_user->getId(), $current_user->getType());
	}

}
