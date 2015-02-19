<?php

/**
 * Handles retrieving data from the API's DDoS Zone resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_DdosZones extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'ddos_zone', '/ddos/zones', 'zone');
	}

}
