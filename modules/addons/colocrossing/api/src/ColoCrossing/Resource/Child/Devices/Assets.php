<?php

/**
 * Handles retrieving data from the API's device assets sub-resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child_Devices
 */
class ColoCrossing_Resource_Child_Devices_Assets extends ColoCrossing_Resource_Child_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client->devices, $client, 'asset', '/assets');
	}

}
