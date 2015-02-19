<?php

/**
 * Handles retrieving data from the API's DDoS Zone Attacks sub-resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child_DdosZones
 */
class ColoCrossing_Resource_Child_DdosZones_Attacks extends ColoCrossing_Resource_Child_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client->ddos_zones, $client, 'attack', '/attacks');
	}

}
