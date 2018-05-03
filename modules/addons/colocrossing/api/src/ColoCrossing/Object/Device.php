<?php

/**
 * Represents an instance of a Generic Base Device resource
 * that is owned by the user from the API. Holds data for a
 * Generic Base Device and provides methods to retrive
 * objects related to the device such as its Type, Owner,
 * Subusers, Assets, or Notes.
 *
 * In order to retrieve a device of this type, the device must
 * be assigned to you and not be a shared device. Trying to create
 * a shared device of this type will result in and Authorization_Error
 * to be thrown.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_Device extends ColoCrossing_Resource_Object
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
	 * Retrieves the User object of the Owner of this. It will always be the
	 * user associated with the API Token.
	 * @return ColoCrossing_Object_User The Owner of the Device.
	 */
	public function getOwner()
	{
		return $this->getObject('owner', null, 'user');
	}

	/**
	 * Retrieves the IPMI Configuration object of this device.
	 * Will include information about Null Route if it exists.
	 * @return ColoCrossing_Object_Device_Ipmi_Configuration The Ipmi Configuration.
	 */
	public function getIpmiConfiguration()
	{
		return $this->getObject('ipmi_config', null, 'ipmi_config')
	}

	/**
	 * Retrieves list of User objects that are assigned as subusers to this device.
	 * @return array<ColoCrossing_Object_User>	The Subusers of the Device.
	 */
	public function getSubusers()
	{
		return $this->getObjectArray('subusers', null, 'user', array());
	}

	/**
	 * Retrieves User object that is assigned as a subuser of this device.
	 * @param int 		$id 					The Id of the subuser.
	 * @return ColoCrossing_Object_User|null	The Subuser of the Device.
	 */
	public function getSubuser($id)
	{
		$subusers = $this->getSubusers();

		return ColoCrossing_Utility::getObjectFromCollectionById($subusers, $id);
	}

	/**
	 * Retrieves the list of Device Asset objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Asset>	The Device Assets
	 */
	public function getAssets(array $options = null)
	{
		return $this->getResourceChildCollection('assets', $options);
	}

	/**
	 * Retrieves the Device Asset object matching the provided Id.
	 * @param  int 		$id 							The Id.
	 * @return ColoCrossing_Object_Asset|null	The Device Asset
	 */
	public function getAsset($id)
	{
		return $this->getResourceChildObject('assets', $id);
	}

	/**
	 * Retrieves the list of Device Note objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device_Note>	The Device Notes
	 */
	public function getNotes(array $options = null)
	{
		return $this->getResourceChildCollection('notes', $options);
	}

	/**
	 * Retrieves the Device Note object matching the provided Id.
	 * @param  int 		$id 						The Id.
	 * @return ColoCrossing_Object_Device_Note|null	The Device Note
	 */
	public function getNote($id)
	{
		return $this->getResourceChildObject('notes', $id);
	}

	/**
	 * Updates this Device with the Provided Nickname and Hostname
	 * @param string    $nickname
	 * @param string    $hostname
	 * @return boolean  True if successful, false otherwise.
	 */
	public function update($nickname, $hostname)
	{
		$client = $this->getClient();

		if($client->devices->update($this, $nickname, $hostname))
		{
			$this->setValue('nickname', $nickname);
			$this->setValue('hostname', $hostname);

			return true;
		}

		return false;
	}

	/**
	 * Cancels the Service of this Device.
	 * If this succeeds, this device will no longer be available.
	 * This action requires your client to have the 'device_cancellation' permission
	 * @return boolean		True if the cancellation suceeds, false otherwise.
	 */
	public function cancelService()
	{
		$client = $this->getClient();

		return $client->devices->cancelService($this);
	}

}
