<?php

/**
 * Represents an instance of a Power Distribution Unit Device
 * resource that is owned by the user from the API. Holds data for a
 * Power Distribution Unit Device and provides methods to retrieve
 * objects related to the device such as its Subnets, Switches or Power Ports.
 * PDUs are the most common example of this type.
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
class ColoCrossing_Object_Device_Type_PowerDistributionUnit extends ColoCrossing_Object_Device_Type_Racked
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
	 * Retrieve the list of Switches this is attached to.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device_Type_Switch>	The Switches
	 */
	public function getSwitches(array $options = null)
	{
		return $this->getResourceChildCollection('switches', $options);
	}

	/**
	 * Retrieve the Switch this is attached to matching the provided Id.
	 * @param  int 	$id 	The Id.
	 * @return ColoCrossing_Object_Device_Type_Switch|null	The Switch
	 */
	public function getSwitch($id)
	{
		return $this->getResourceChildObject('switches', $id);
	}

	/**
	 * Retrieves the Ports on this PDU
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
	 * Retrieves the Port on this PDU matching the provided Id.
	 * @return array<ColoCrossing_Object_Device_PowerPort> The Power Port
	 */
	public function getPort($id)
	{
		$ports = $this->getPorts();

		return ColoCrossing_Utility::getObjectFromCollectionById($ports, $id);
	}

	/**
	 * Retrieves the Devices that are assigned to this PDU.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device> 	The PDU's Devices
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
