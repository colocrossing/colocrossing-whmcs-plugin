<?php

/**
 * Handles retrieving data from the API's device Ipmi Null Route sub-resource.
 * Also Allows for controlling the status of the null route.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child_Devices
 */
class ColoCrossing_Resource_Child_Devices_IpmiNullRoute extends ColoCrossing_Resource_Child_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client->devices, $client, 'ipmi_null_route', '/ipmi');
	}

	/**
	 * Set the status of the provided port on the provided pdu that
	 * is connected to the provided device.
	 * @param  int|ColoCrossing_Object_Device_IpmiConfiguration         	$config 	The IPMI Configuration Object
	 * @param  int|ColoCrossing_Object_Device 								$device 	The Device or Id
	 * @param  string 														$action    	The action 'lift', 'replace', 'renew'
	 * @return boolean  		   	True if succeeds, false otherwise.
	 */
	public function setNullRouteStatus($config, $device, $action)
	{
		$status = strtolower($status);

		if ($status != 'lift' && $status != 'replace' && $status != 'renew')
		{
			return false;
		}

		$device_id = is_numeric($device) ? $device : $device->getId();

		$url = $this->createObjectUrl($action, $device_id);

		$data = array();
		$response = $this->sendRequest($url, 'PUT', $data);

		if (empty($response))
		{
			return false;
		}

		$content = $response->getContent();

		return isset($content) && isset($content['status']) && $content['status'] == 'ok';
	}

}
