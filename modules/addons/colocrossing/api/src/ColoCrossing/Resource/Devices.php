<?php

/**
 * Handles retrieving data from the API's devices resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_Devices extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'device', '/devices');
	}

	/**
	 * Retrieve a Collection of Devices with name similar to one provided.
	 * @param  string 	$name 	The Name to search for
	 * @param  array 	$options    	The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_Device> The Devices
	 */
	public function findLikeName($name, array $options = null)
	{
		$options = isset($options) && is_array($options) ? $options : array();
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['name'] = $name;

		return $this->findAll($options);
	}

	/**
	 * Retrieve a Collection of Devices with type similar to one provided.
	 * @param  string 	$type_id 	The type id to filter by
	 * @param  array 	$options    	The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_Device> The Devices
	 */
	public function findByType($type_id, array $options = null)
	{
		$options = isset($options) && is_array($options) ? $options : array();
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['type'] = $type_id;

		return $this->findAll($options);
	}

	/**
	 * Retrieve a Collection of Devices whose Ids are in the provided list
	 * @param  array<int> 	$ids 		The Ids
	 * @param  array 		$options    The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_Device> The Devices
	 */
	public function findByIds(array $ids, array $options = null)
	{
		$options = isset($options) && is_array($options) ? $options : array();
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['ids'] = array_unique($ids);

		return $this->findAll($options);
	}

	/**
	 * Cancels the Service Associated with the Provided Device
	 * @param  int|ColoCrossing_Object_Device $device 	The Device or Id
	 * @return boolean		True if the cancellation suceeds, false otherwise.
	 */
	public function cancelService($device)
	{
		if(is_numeric($device))
		{
			$device = $this->find($device);
		}

		$client = $this->getClient();

		if (empty($device) || !$client->hasPermission('device_cancellation'))
		{
			return false;
		}

		$url = $this->createObjectUrl($device->getId());
		$response = $this->sendRequest($url, 'DELETE');

		if (empty($response))
		{
			return false;
		}

		$content = $response->getContent();

		return isset($content) && isset($content['status']) && $content['status'] == 'ok';
	}

}
