<?php

/**
 * Represents an instance of a Rack resource that is owned by
 * the user from the API. Holds data for a Rack and provides
 * methods to retrieve objects related to the rack such as its Devices.
 * Racks are the most common example of this type.
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
class ColoCrossing_Object_Device_Type_Rack extends ColoCrossing_Object_Device
{

	/**
	 * Retrieves the Devices that are assigned to this rack.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device_Type_Racked> 	The Rack's Devices
	 */
	public function getDevices(array $options = null)
	{
		$devices = array_map(function($device) {
			return intval($device['id']);
		}, $this->getRackDevices());

		$client = $this->getClient();

		return $client->devices->findByIds($devices, $options);
	}

	/**
	 * Retrieves the Device that matches the provided Id and are assigned to this rack.
	 * @param int 	$id 	The Id
	 * @return ColoCrossing_Object_Device_Type_Racked 	The Rack's Device
	 */
	public function getDevice($id)
	{
		$devices = $this->getDevices();

		return ColoCrossing_Utility::getObjectFromCollectionById($devices, $id);
	}

}
