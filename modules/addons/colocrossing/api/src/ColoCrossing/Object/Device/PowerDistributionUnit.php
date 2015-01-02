<?php

/**
 * Represents an instance of a PDU assigned to a Device resource from
 * the API. Holds only the data for the PDU that is associated with the
 * assigned device. It provides methods to retrieve objects related to
 * the PDU such as its Type, Owner, or Ports. The Ports retrieved from
 * this object are only the one's assigned to the Device from which this
 * PDU object was retrieved.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_PowerDistributionUnit extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Device Type object that describes the capabilities
	 * of this device. Such as its power, network, and rack capabilities.
	 * @return ColoCrossing_Object_Device_Type  The Device Type
	 */
	public function getType()
	{
		return $this->getObject('type', null, 'type');
	}

	/**
	 * Retrieves the Ports on this PDU that the Device from which this PDU was
	 * retrieved from is assigned to.
	 * @return array<ColoCrossing_Object_Device_PowerPort> The Power Ports
	 */
	public function getPorts()
	{
		$additional_data = array(
			'power_distribution_unit' => $this
		);

		return $this->getObjectArray('ports', null, 'power_port', array(), $additional_data);
	}

	/**
	 * Retrieves the Port that matches the provided Id on this PDU that the Device
	 * from which this PDU was retrieved from is assigned to.
	 * @return ColoCrossing_Object_Device_PowerPort The Power Port
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

		$client = $this->getClient();

		return $this->getObjectById($this->getId(), 'detailed_device', $client->devices);
	}

}
