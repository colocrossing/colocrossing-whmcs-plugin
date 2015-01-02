<?php

/**
 * Represents an instance of a Switch's Network Port resource from
 * the API. Holds data for a Switch's Network Port and provides
 * methods to retrieve objects related to the network port such as
 * its Device, Switch or Bandwidth Graph. It also provides methods
 * to turn on or turn off the port.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_NetworkPort extends ColoCrossing_Object
{

	/**
	 * Retreives the Deivce object that the Network Port belongs to.
	 * @return ColoCrossing_Object_Device 	The Network Port's Device
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
	 * Determines if a Bandwidth Graph is available for this Port
	 * @return boolean True if graph is available, false otherwise.
	 */
	public function isBandwidthGraphAvailable()
	{
		return !!$this->getHasGraph() && $this->isAssignedToDevice();
	}

	/**
	 * Retrieves the Bandwidth Graph for this port.
	 * @param  int $start 		The start time of the graph. Defaults to start of current month if null.
	 * @param  int $end   		The end time of the graph. Defaults to now if null.
	 * @return resource|null   	The image resource of the graph
	 */
	public function getBandwidthGraph($start = null, $end = null)
	{
		if (!$this->isBandwidthGraphAvailable())
		{
			return null;
		}

		$switch = $this->getSwitch();

		$client = $this->getClient();

		return $client->devices->switches->getBandwidthGraph($switch, $this, $this->getDeviceId(), $start, $end);
	}

	/**
	 * Determines if a Bandwidth Usage is available for this Port
	 * @return boolean True if graph is available, false otherwise.
	 */
	public function isBandwidthUsageAvailable()
	{
		return !!$this->getHasBandwidthUsage() && $this->isAssignedToDevice();
	}

	/**
	 * Retrieves the Bandwidth Usage for this port.
	 * @return ColoCrossing_Object 	The Bandwidth Usage
	 */
	public function getBandwidthUsage()
	{
		if (!$this->isBandwidthUsageAvailable())
		{
			return null;
		}

		$switch = $this->getSwitch();

		$client = $this->getClient();

		return $client->devices->switches->getBandwidthUsage($switch, $this, $this->getDeviceId());
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

		$switch = $this->getSwitch();

		$client = $this->getClient();

		return $client->devices->switches->setPortStatus($switch, $this, $this->getDeviceId(), $status, $comment);
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

}
