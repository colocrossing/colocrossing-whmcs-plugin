<?php

/**
 * Represents an instance of a Network resource from the API.
 * Holds data for a Network and provides methods to retrive
 * objects related to the subnet such as its Subnets or Null Routes.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_Network extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the list of Network Subnet objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Subnet>	The Network Subnets
	 */
	public function getSubnets(array $options = null)
	{
		return $this->getResourceChildCollection('subnets', $options);
	}

	/**
	 * Retrieves the Network Subnet object matching the provided Id.
	 * @param  int 		$id 					The Id.
	 * @return ColoCrossing_Object_Subnet|null	The Network Subnet
	 */
	public function getSubnet($id)
	{
		return $this->getResourceChildObject('subnets', $id);
	}

	/**
	 * Retrieves the Network Subnet object matching the provided Ip Address.
	 * @param  string		$ip_address 		The Ip Address.
	 * @return ColoCrossing_Object_Subnet|null	The Network Subnet
	 */
	public function getSubnetByIpAddress($ip_address)
	{
		$client = $this->getClient();
		$subnets = $client->networks->subnets->findAllLikeIpAddress($this->getId(), $ip_address, array('page_size' => 100));

		foreach ($subnets as $key => $subnet)
		{
			if ($subnet->isIpAddressInSubnet($ip_address))
			{
				return $subnet;
			}
		}

		return null;
	}

	/**
	 * Retrieves the list of Network Null Route objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_NullRoute>	The Network Null Routes
	 */
	public function getNullRoutes(array $options = null)
	{
		return $this->getResourceChildCollection('null_routes', $options);
	}

	/**
	 * Retrieves the list of Network Null Route objects.
	 * @param string 	$ip_address 	The Ip Address
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_NullRoute>	The Network Null Routes
	 */
	public function getNullRoutesByIpAddress($ip_address, array $options = null)
	{
		$client = $this->getClient();

		return $client->networks->null_routes->findAllByIpAddress($this->getId(), $ip_address, $options);
	}

	/**
	 * Retrieves the Network Null_Route object matching the provided Id.
	 * @param  int 		$id 					The Id.
	 * @return ColoCrossing_Object_NullRoute|null	The Network Null_Route
	 */
	public function getNullRoute($id)
	{
		$null_routes = $this->getNullRoutes();

		return ColoCrossing_Utility::getObjectFromCollectionById($null_routes, $id);
	}

	/**
	 * Computes the Total Number of Ip Addesses in the Network Accoring to the CIDR.
	 * @return int The Total Number of Ip Addresses
	 */
	public function getNumberOfIpAddresses()
	{
		$cidr = intval($this->getCidr());
		return pow(2, 32 - $cidr);
	}

}
