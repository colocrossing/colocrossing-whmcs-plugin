<?php

/**
 * Handles retrieving data from the API's null routes resource.
 * Also allows for adding and removing null routes through the API.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_NullRoutes extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'null_route', '/null-routes');
	}

	/**
	 * Retrieve a Collection of Null Routes on the provided Ip Address.
	 * @param  string 	$ip_address 	The Ip Address. If invalid empty array is returned.
	 * @param  array 	$options    	The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_NullRoute> The Null Routes
	 */
	public function findAllByIpAddress($ip_address, array $options = null)
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

	/**
	 * Adds a Null Route to an Ip Address on the provided Subnet.
	 * @param int|ColoCrossing_Object_Subnet 	$subnet 		The Subnet or Subnet Id
	 * @param string 							$ip_address  	The Ip Address
	 * @param string 							$comment     	The Comment Explaing the Reason for the Null Route
	 * @param int 								$expire_date 	The Date The Null Route is to Expire as a Unix Timestamp.
	 *                           			    				  Defaults to 4 hrs from now. Max of 30 days from now.
	 * @return boolean|ColoCrossing_Object_NullRoute 	The new Null Route object if successful, false otherwise.
	 */
	public function add($subnet, $ip_address, $comment = '', $expire_date = null)
	{
		if(is_numeric($subnet))
		{
			$client = $this->getClient();
			$subnet = $client->subnets->find($subnet);
		}

		if (empty($subnet) || !$subnet->isIpAddressInSubnet($ip_address) || !$subnet->isNullRoutesEnabled())
		{
			return false;
		}

		$data = array(
			'subnet_id' => $subnet->getId(),
			'ip_address' => $ip_address,
			'comment' => $comment
		);

		if (isset($expire_date) && is_int($expire_date))
		{
			$data['expire_date'] = date('c', $expire_date);

			if ($expire_date > strtotime('+30 days'))
			{
				return false;
			}
		}

		$null_routes = $this->findAllByIpAddress($ip_address);

		foreach ($null_routes as $key => $null_route)
		{
			$null_route_subnet = $null_route->getSubnet();

			if (isset($null_route_subnet) && $subnet->getId() == $null_route_subnet->getId())
			{
				return false;
			}
		}

		$url = $this->createCollectionUrl();

		$response = $this->sendRequest($url, 'POST', $data);

		if (empty($response))
		{
			return false;
		}

		$content = $response->getContent();

		if (empty($content) || empty($content['status']) || $content['status'] == 'error' || empty($content['null_route']) || empty($content['null_route']['id']))
		{
			return false;
		}

		return $this->find($content['null_route']['id']);
	}

	/**
	 * Removes this Null Route.
	 * @param  int|ColoCrossing_Object_NullRoute $null_route 	The Null Route or Id
	 * @return boolean		True if the removal suceeds, false otherwise.
	 */
	public function remove($null_route)
	{
		if(is_numeric($null_route))
		{
			$null_route = $this->find($null_route);
		}

		if (empty($null_route) || !$null_route->isRemovable())
		{
			return false;
		}

		$url = $this->createObjectUrl($null_route->getId());

		$response = $this->sendRequest($url, 'DELETE');

		if (empty($response))
		{
			return false;
		}

		$content = $response->getContent();

		return isset($content) && isset($content['status']) && $content['status'] == 'ok';
	}

}
