<?php

/**
 * Represents an instance of a Switch assigned to a Device resource from
 * the API. Holds only the data for the Switch that is associated with the
 * assigned device from which this object was retrieved. It provides methods
 * to retrieve objects related to the Switch such as its Type, Owner, or
 * Ports. The Ports retrieved from this object are only the one's assigned
 * to the Device from which this Switch object was retrieved.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_Switch extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Device Type object that describes the capabilities
	 * of this device. Such as its power, network, and rack capabilities.
	 * @return ColoCrossing_Object_Type  The Device Type
	 */
	public function getType()
	{
		return $this->getObject('type', null, 'type');
	}

	/**
	 * Retrieves the Ports on this Switch that the Device from which this Switch was
	 * retrieved from is assigned to.
	 * @return array<ColoCrossing_Object_Device_NetworkPort> The Network Ports
	 */
	public function getPorts()
	{
		$additional_data = array(
			'switch' => $this
		);

		return $this->getObjectArray('ports', null, 'network_port', array(), $additional_data);
	}

	/**
	 * Retrieves the Port that matches the provided Id on this Switch that the Device
	 * from which this Switch was retrieved from is assigned to.
	 * @return ColoCrossing_Object_Device_NetworkPort The Network Port
	 */
	public function getPort($id)
	{
		$ports = $this->getPorts();

		return ColoCrossing_Utility::getObjectFromCollectionById($ports, $id);
	}

	/**
	 * Retrieves the User object of the Owner of this. It will always be the
	 * user associated with the API Token.
	 * @return ColoCrossing_Object_User The Owner of the Device.
	 */
	public function getOwner()
	{
		return $this->getObject('owner', null, 'user');
	}

	/**
	 * Determines if the detailed version of this device is available
	 * @return boolean True if the detailed device is available, false otherwise.
	 */
	public function isDetailedDeviceAvailable()
	{
		$owner = $this->getOwner();

		return empty($owner);
	}

	/**
	 * Retrieves the detailed Device object if it is available.
	 * @return ColoCrossing_Object_Device|null The detailed Device object.
	 */
	public function getDetailedDevice()
	{
		if (!$this->isDetailedDeviceAvailable())
		{
			return null;
		}

		$device_id = $this->getId();
		$client = $this->getClient();

		return $this->getObjectById($device_id, 'detailed_device', $client->devices);
	}

}
