<?php

/**
 * Represents an instance of a Vitual Device resource that is owned by
 * the user from the API. Holds data for a Virtual Device and provides
 * methods to retrieve objects related to the virtual device such as
 * its Subnets. Virtual Private Servers are the most common
 * examples of this type.
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
class ColoCrossing_Object_Device_Type_Virtual extends ColoCrossing_Object_Device
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

}
