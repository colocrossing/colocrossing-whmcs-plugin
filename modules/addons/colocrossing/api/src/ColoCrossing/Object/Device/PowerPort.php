<?php

/**
 * Represents an instance of a PDU's Power Port resource from
 * the API. Holds data for a PDU's Power Port and provides
 * methods to retrieve objects related to the power port such as
 * its assigned Device or PDU. It also provides methods to restart,
 * turn on or turn off the port.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_PowerPort extends ColoCrossing_Object
{

	/**
	 * Retreives the Deivce object that the Power Port belongs to.
	 * @return ColoCrossing_Object_Device 	The Power Port's Device
	 */
	public function getDevice()
	{
		$client = $this->getClient();

		return $this->getObject('device', $client->devices);
	}

	/**
	 * Retrieves the device Id that is Assigned to this Port
	 * Returns Null if port is unassigned.
	 * @return int|null The Device Id
	 */
	public function getDeviceId()
	{
		$device = $this->getValue('device');

		if(empty($device) || !is_array($device))
		{
			return null;
		}

		return $device['id'];
	}

	/**
	 * Determines if this Port is Assigned to a Device
	 * @return boolean True if port is assigned
	 */
	public function isAssignedToDevice()
	{
		$device_id = $this->getDeviceId();
		return isset($device_id);
	}

	/**
	 * Determines if the port has the ability to be controlled.
	 * @return boolean True if the port status can be set, false otherwise.
	 */
	public function isControllable()
	{
		return $this->isControl() && $this->isAssignedToDevice();
	}

	/**
	 * Sets the status of the port.
	 * @param 	string $status 	The status of the port. Either 'on' or 'off'.
	 * @param 	string $comment The comment, Optional, Max Length of 20 Chars
	 * @return boolean 			True if the status is set successfully, false otherwise.
	 */
	public function setStatus($status, $comment = null)
	{
		if (!$this->isControllable())
		{
			return false;
		}

		$pdu = $this->getPowerDistributionUnit();

		$client = $this->getClient();

		return $client->devices->pdus->setPortStatus($pdu, $this, $this->getDeviceId(), $status, $comment);
	}

	/**
	 * Turns the port on.
	 * @param 	string $comment The comment, Optional, Max Length of 20 Chars
	 * @return boolean 	True if the status is set successfully, false otherwise.
	 */
	public function turnOn($comment = null)
	{
		return $this->setStatus('on', $comment);
	}

	/**
	 * Turns the port off.
	 * @param 	string $comment The comment, Optional, Max Length of 20 Chars
	 * @return boolean 	True if the status is set successfully, false otherwise.
	 */
	public function turnOff($comment = null)
	{
		return $this->setStatus('off', $comment);
	}

	/**
	 * Restarts the port.
	 * @param 	string $comment The comment, Optional, Max Length of 20 Chars
	 * @return boolean 	True if the status is set successfully, false otherwise.
	 */
	public function restart($comment = null)
	{
		return $this->setStatus('restart', $comment);
	}

}
