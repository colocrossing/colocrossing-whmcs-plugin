<?php

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
	 * Retrieves the User of the specified type and id
	 * @param integer $id The User ID
	 * @return ColoCrossing_Model_Admin|ColoCrossing_Model_Client|null|false The User, False if not found, Null if System.
	 */
	public static function getUser($id, $type) {
		switch ($type) {
			case 'Admin':
			case 1:
				$admin = ColoCrossing_Model_Admin::find($id);
				return isset($admin) ? $admin : false;
			case 'Client':
			case 2:
				$client = ColoCrossing_Model_Client::find($id);
				return isset($client) ? $client : false;
		}

		return null;
	}

	/**
	 * Retrieves the Currently Signed In User
	 * @return ColoCrossing_Model_Admin|ColoCrossing_Model_Client|null|false The User, False if not found, Null if System.
	 */
	public static function getCurrentUser() {
		if(isset($_SESSION['adminid'])) {
			return self::getUser($_SESSION['adminid'], 1);
		} else if(isset($_SESSION['clientid'])) {
			return self::getUser($_SESSION['clientid'], 2);
		}

		return null;
	}

	/**
	 * Retrieves the Currently Signed In User
	 * @return array(int, int) 	An array where the 1st element is the id and the 2nd
	 *                          	is the type
	 */
	public static function getCurrentUserId() {
		$current_user = self::getCurrentUser();

		if(empty($current_user)) {
			return array(0, 0);
		}

		return array($current_user->getId(), $current_user->getType());
	}

}
