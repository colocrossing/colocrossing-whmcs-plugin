<?php

/**
 * Represents an instance of a Switch Device resource that is owned by
 * the user from the API. Holds data for a Switch Device and provides
 * methods to retrieve objects related to the device such as its Subnets,
 * PDUs or Switch Ports.
 * Switches and Firewalls are the most common examples of this type.
 *
 * In order to retrieve a device of this type, the device must
 * be assigned to you and not be a shared device. Trying to create
 * a shared device of this type will result in and Authorization_Error
 * to be thrown.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device_Type
 */
class ColoCrossing_Object_Device_Type_Switch extends ColoCrossing_Object_Device_Type_Racked
{

	/**
	 * Retrieves the list of Device Subnet objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Subnet>	The Device Subnets
	 */
	public function getSubnets(array $options = null)
	{
		return $this->getResourceChildCollection('subnets', $options);
	}

	/**
	 * Retrieves the Device Subnet object matching the provided Id.
	 * @param  int 		$id 					The Id.
	 * @return ColoCrossing_Object_Subnet|null	The Device Subnet
	 */
	public function getSubnet($id)
	{
		return $this->getResourceChildObject('subnets', $id);
	}

	/**
	 * Retrieve the list of Power Distribution Units this is attached to.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device_Type_PowerDistributionUnit>	The Power Distribution Units
	 */
	public function getPowerDistributionUnits(array $options = null)
	{
		return $this->getResourceChildCollection('pdus', $options);
	}

	/**
	 * Retrieve the Power Distribution Unit this is attached to matching the provided Id.
	 * @param  int 	$id 	The Id.
	 * @return ColoCrossing_Object_Device_Type_PowerDistributionUnit|null	The Power Distribution Unit
	 */
	public function getPowerDistributionUnit($id)
	{
		return $this->getResourceChildObject('pdus', $id);
	}

	/**
	 * Retrieves the Ports on this Switch
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
	 * Retrieves the Port on this Switch matching the provided Id.
	 * @return array<ColoCrossing_Object_Device_NetworkPort> The Network Port
	 */
	public function getPort($id)
	{
		$ports = $this->getPorts();

		return ColoCrossing_Utility::getObjectFromCollectionById($ports, $id);
	}

	/**
	 * Retrieves the Devices that are assigned to this Switch.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device> 	The Switch's Devices
	 */
	public function getDevices(array $options = null)
	{
		$ports = $this->getPorts();
		$devices = array();

		foreach ($ports as $index => $port)
		{
			if($port->isAssignedToDevice())
			{
				$devices[] = $port->getDeviceId();
			}
		}

		$client = $this->getClient();

		return $client->devices->findByIds($devices, $options);
	}

}
