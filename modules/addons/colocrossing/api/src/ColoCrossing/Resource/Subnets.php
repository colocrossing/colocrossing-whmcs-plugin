<?php

/**
 * Handles retrieving data from the API's subnets resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_Subnets extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'subnet', '/subnets');
	}

	/**
	 * Retrieve a Collection of Subnets within the same /24 as the provided Ip Address.
	 * @param  string 	$ip_address 	The Ip Address. If invalid empty array is returned.
	 * @param  array 	$options    	The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_Subnet> The Subnets
	 */
	public function findAllLikeIpAddress($ip_address, array $options = null)
	{
		if (!filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			return array();
		}

		$options = isset($options) && is_array($options) ? $options : array();
		$options['filters'] = array(
			'ip_address' => $ip_address
		);

		return $this->findAll($options);
	}

}
