<?php

/**
 * Represents an instance of a Device's Note resource from the API.
 * Holds data for a Device's Note and provides methods to retrive
 * objects related to the note such as its User or Device.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_Note extends ColoCrossing_Resource_Object
{

	/**
	 * Retreives the User object that created the Note.
	 * @return ColoCrossing_Object_User	   The Note's User
	 */
	public function getUser()
	{
		return $this->getObject('user', null, 'user');
	}

	/**
	 * Retreives the Deivce object that the Note belongs to.
	 * @return ColoCrossing_Object_Device 	The Note's Device
	 */
	public function getDevice()
	{
		$client = $this->getClient();

		return $this->getObject('device', $client->devices);
	}

	/**
	 * Retrieves the Time the Note was Created as a Unix Timestamp.
	 * @return int The create time.
	 */
	public function getTime()
	{
		$time = $this->getValue('time');

		return $time && isset($time) ? strtotime($time) : null;
	}

}
