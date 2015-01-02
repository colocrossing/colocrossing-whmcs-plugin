<?php

/**
 * Represents an instance of a Network Endpoint Device resource
 * that is owned by the user from the API. Holds data for a
 * Network Endpoint Device and provides methods to retrieve
 * objects related to the device such as its Subnets or Switches.
 * Cross Connects are the most common example of this type.
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
class ColoCrossing_Object_Device_Type_NetworkEndpoint extends ColoCrossing_Object_Device_Type_Racked
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

}
