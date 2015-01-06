<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Represents a Table which holds event that occurred within the ColoCrossing Module
 */
class ColoCrossing_Model_Event extends ColoCrossing_Model {

	/**
	 * The Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'user_id', 'user_type', 'description', 'time');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'mod_colocrossing_events';

	/**
	 * A Class Wide Cache of Users
	 * @var array<string, ColoCrossing_Model_User>
	 */
	protected static $USERS = array();

	/**
	 * @return ColoCrossing_Admin|ColoCrossing_Client|null|false The User, False if not found, Null if System.
	 */
	public function getUser() {
		$id = $this->getValue('user_id');
		$type = $this->getValue('user_type');
		$key = $type . '-' . $id;

		if(isset(self::$USERS[$key])) {
			return self::$USERS[$key];
		}

		return self::$USERS[$key] = ColoCrossing_Model_User::getUser($id, $type);
	}

	/**
	 * @return string The human readable name of this user.
	 */
	public function getUserName() {
		$user = $this->getUser();

		if(isset($user)) {
			return $user->getFullName();
		} else if($user === false) {
			return 'Unknown';
		}

		return 'System';
	}

	/**
	 * @return string The Description of The Event
	 */
	public function getDescription() {
		$description = $this->getValue('description');

		return empty($description) ? '' : $description;
	}

	/**
	 * @return integer|null The Time of Event
	 */
	public function getTime() {
		$time = $this->getValue('time');

		return empty($time) ? null : intval($time);
	}

	/**
	 * Retrieves Time in Human Readable Format
	 * @return string The Formatted Time
	 */
	public function getFormattedTime() {
		$time = $this->getTime();

		return empty($time) ? 'Never' : date('m/d/Y h:i a', $time);
	}

	/**
	 * Creates an Event for the Current User at the Current Time with
	 * the description provided.
	 * @param string|integer $type The Type of User to Performing Action (Admin|Client)
	 * @param  string $description A description of the event.
	 * @return boolean True if created successfully
	 */
	public static function log($type, $description = '') {
		list($user_id, $user_type) = ColoCrossing_Model_User::getCurrentUserId($type);

		$event = self::create(array(
			'user_id' => $user_id,
			'user_type' => $user_type,
			'description' => $description,
			'time' => time()
		));

		return $event->save();
	}

}
