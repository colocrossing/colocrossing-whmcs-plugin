<?php

/**
 * Handles retrieving data from the API's bandwidth usage resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_BandwidthUsages extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'bandwidth_usage', '/bandwidth/usages');
	}

}
