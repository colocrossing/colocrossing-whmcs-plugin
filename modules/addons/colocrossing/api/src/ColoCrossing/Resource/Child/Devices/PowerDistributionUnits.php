<?php

/**
 * Handles retrieving data from the API's device PDUs sub-resource.
 * Also Allows for controlling the port of the PDU.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child_Devices
 */
class ColoCrossing_Resource_Child_Devices_PowerDistributionUnits extends ColoCrossing_Resource_Child_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client->devices, $client, 'pdu', '/power');
	}

	/**
	 * Set the status of the provided port on the provided pdu that
	 * is connected to the provided device.
	 * @param  int|ColoCrossing_Object_Device_Type_PowerDistributionUnit	$pdu 		The PDU or Id
	 * @param  int|ColoCrossing_Object_Device_NetworkPort 					$port   	The Port or Id
	 * @param  int|ColoCrossing_Object_Device 								$device 	The Device or Id
	 * @param  string 														$status    	The new Port status. 'on', 'off', or 'restart'
	 * @param  string 														$comment    The comment, Optional, Max Length of 20 Chars
	 * @return boolean  		   	True if succeeds, false otherwise.
	 */
	public function setPortStatus($pdu, $port, $device, $status, $comment = null)
	{
		$status = strtolower($status);

		if ($status != 'on' && $status != 'off' && $status != 'restart')
		{
			return false;
		}

		if (isset($comment) && strlen($comment) > 20)
		{
			return false;
		}

		$device_id = is_numeric($device) ? $device : $device->getId();

		if(is_numeric($pdu))
		{
			$pdu = $this->find($pdu, $device_id);
		}

		if (empty($pdu) || !$pdu->getType()->isPowerDistribution())
		{
			return false;
		}

		if(is_numeric($port))
		{
			$port = $pdu->getPort($port);
		}

		if (empty($port) || !$port->isControllable())
		{
			return false;
		}

		$url = $this->createObjectUrl($pdu->getId(), $device_id);
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
