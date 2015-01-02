<?php

/**
 * Handles retrieving data from the API's device switches sub-resource.
 * Also Allows for controlling the port of the Switch and retrieving a
 * port's Bandwidth Graphs.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child_Devices
 */
class ColoCrossing_Resource_Child_Devices_Switches extends ColoCrossing_Resource_Child_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client->devices, $client, array('singular' => 'switch', 'plural' => 'switches'), '/networks');
	}

	/**
	 * Retrieves the Bandwidth Graph of the provided Port on the provided Switch
	 * that is assigned to the provided Device.
	 * @param  int|ColoCrossing_Object_Device_Type_Switch	$switch 	The Switch or Id
	 * @param  int|ColoCrossing_Object_Device_NetworkPort 	$port   	The Port or Id
	 * @param  int|ColoCrossing_Object_Device 				$device 	The Device or Id
	 * @param  int 											$start     	The Unix Timestamp that is the start time of the graph range.
	 * @param  int 											$end      	The Unix Timestamp that is the end time of the graph range.
	 * @return resource|null	An PNG Image Resource if it is available, null otherwise
	 */
	public function getBandwidthGraph($switch, $port, $device, $start = null, $end = null)
	{
		$start = isset($start) ? $start : strtotime(date('Y').'-'.date('m').'-01'.' '.date('h').':'.date('i').':00');
		$end = isset($end) ? $end : strtotime(date('Y').'-'.date('m').'-'.date('d').' '.date('h').':'.date('i').':59');

		if ($start >= $end)
		{
			return null;
		}

		$device_id = is_numeric($device) ? $device : $device->getId();

		if(is_numeric($switch))
		{
			$switch = $this->find($switch, $device_id);
		}

		if (empty($switch) || !$switch->getType()->isNetworkDistribution())
		{
			return null;
		}

		if(is_numeric($port))
		{
			$port = $switch->getPort($port);
		}

		if (empty($port) || !$port->isBandwidthGraphAvailable())
		{
			return null;
		}

		$url = $this->createObjectUrl($switch->getId(), $device_id) . '/graphs/' . urlencode($port->getId());
		$data = array(
			'start' => date('c', $start),
			'end' => date('c', $end)
		);

		$response = $this->sendRequest($url, 'GET', $data);

		if (empty($response) || $response->getContentType() != 'image/png')
		{
			return null;
		}

		return $response->getContent();
	}

	/**
	 * Retrieves the Bandwidth Usage of the provided Port on the provided Switch
	 * that is assigned to the provided Device.
	 * @param  int|ColoCrossing_Object_Device_Type_Switch	$switch 	The Switch or Id
	 * @param  int|ColoCrossing_Object_Device_NetworkPort 	$port   	The Port or Id
	 * @param  int|ColoCrossing_Object_Device 				$device 	The Device or Id
	 * @return ColoCrossing_Object	The Bandwidth Usage
	 */
	public function getBandwidthUsage($switch, $port, $device, $start = null, $end = null)
	{
		$device_id = is_numeric($device) ? $device : $device->getId();

		if(is_numeric($switch))
		{
			$switch = $this->find($switch, $device_id);
		}

		if (empty($switch) || !$switch->getType()->isNetworkDistribution())
		{
			return null;
		}

		if(is_numeric($port))
		{
			$port = $switch->getPort($port);
		}

		if (empty($port) || !$port->isBandwidthUsageAvailable())
		{
			return null;
		}

		$url = $this->createObjectUrl($switch->getId(), $device_id) . '/bandwidths/' . urlencode($port->getId());
		$response = $this->sendRequest($url);

		if (empty($response))
		{
			return null;
		}

		$content = $response->getContent();
		$content = isset($content) && isset($content['bandwidth']) && is_array($content['bandwidth']) ? $content['bandwidth'] : null;

		if (empty($content))
		{
			return null;
		}

		return ColoCrossing_Object_Factory::createObject($this->getClient(), null, $content, 'bandwidth_usage');
	}

	/**
	 * Set the status of the provided port on the provided switch that
	 * is connected to the provided device.
	 * @param  int|ColoCrossing_Object_Device_Type_Switch	$switch 	The Switch or Id
	 * @param  int|ColoCrossing_Object_Device_NetworkPort 	$port   	The Port or Id
	 * @param  int|ColoCrossing_Object_Device 				$device 	The Device or Id
	 * @param  string 										$status    	The new Port status. 'on' or 'off'
	 * @param  string 										$comment    The comment, Optional, Max Length of 20 Chars
	 * @return boolean  		   	True if succeeds, false otherwise.
	 */
	public function setPortStatus($switch, $port, $device, $status, $comment = null)
	{
		$status = strtolower($status);

		if ($status != 'on' && $status != 'off')
		{
			return false;
		}

		if (isset($comment) && strlen($comment) > 20)
		{
			return false;
		}

		$device_id = is_numeric($device) ? $device : $device->getId();

		if(is_numeric($switch))
		{
			$switch = $this->find($switch, $device_id);
		}

		if (empty($switch) || !$switch->getType()->isNetworkDistribution())
		{
			return false;
		}

		if(is_numeric($port))
		{
			$port = $switch->getPort($port);
		}

		if (empty($port) || !$port->isControllable())
		{
			return false;
		}

		$url = $this->createObjectUrl($switch->getId(), $device_id);
		$data = array(
			'status' => $status,
			'port_id' => $port->getId(),
			'comment' => $comment
		);

		$response = $this->sendRequest($url, 'PUT', $data);

		if (empty($response))
		{
			return false;
		}

		$content = $response->getContent();

		return isset($content) && isset($content['status']) && $content['status'] == 'ok';
	}

}
