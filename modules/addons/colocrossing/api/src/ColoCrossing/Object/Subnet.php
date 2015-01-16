<?php

/**
 * Represents an instance of a Subnet resource from the API.
 * Holds data for a Subnet and provides methods to retrive
 * objects related to the subnet such as its Network, Device,
 * Null Routes, or Reverse DNS Records.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_Subnet extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Network object that this Subnet is on.
	 * If the Network is assigned to you, then the Detailed Network object
	 * returned. Otherwise a generic object is returned that holds the Id,
	 * Ip Address, CIDR, and Type.
	 * @return ColoCrossing_Object_Network|ColoCrossing_Object|null	The Network
	 */
	public function getNetwork()
	{
		$client = $this->getClient();
		$network = $this->getValue('network');

		if (empty($network) || !is_array($network))
		{
			return null;
		}

		$resource = isset($network['owner']) && is_array($network['owner']) ? $client->networks : null;

		return $this->getObject('network', $resource);
	}

	/**
	 * Determines if Subnet is Assigned to Device
	 * @return boolean True if Subnet is Assigned to Device
	 */
	public function isAssigned()
	{
		$device = $this->getValue('device');

		return isset($device);
	}

	/**
	 * Retrieves the Device Object that this is Assigned to. Returns null
	 * if subnet unassigned.
	 * @return ColoCrossing_Object_Device|null 	The Device
	 */
	public function getDevice()
	{
		$client = $this->getClient();

		return $this->getObject('device', $client->devices);
	}

	/**
	 * Retrieves the Device Name that this is Assigned to. Returns null
	 * if subnet unassigned.
	 * @return string 	The Device Name
	 */
	public function getDeviceName()
	{
		if(!$this->isAssigned())
		{
			return null;
		}

		$device = $this->getValue('device');

		return $device['name'];
	}

	/**
	 * Retrieves the Device Id that this is Assigned to. Returns null
	 * if subnet unassigned.
	 * @return string 	The Device Id
	 */
	public function getDeviceId()
	{
		if(!$this->isAssigned())
		{
			return null;
		}

		$device = $this->getValue('device');

		return $device['id'];
	}


	/**
	 * Retrieves the list of Subnet Null Route objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_NullRoute>	The Subnet Null Routes
	 */
	public function getNullRoutes(array $options = null)
	{
		return $this->getResourceChildCollection('null_routes', $options);
	}

	/**
	 * Retrieves the list of Subnet Null Route objects.
	 * @param string 	$ip_address 	The Ip Address
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_NullRoute>	The Subnet Null Routes
	 */
	public function getNullRoutesByIpAddress($ip_address, array $options = null)
	{
		$client = $this->getClient();

		return $client->subnets->null_routes->findAllByIpAddress($this->getId(), $ip_address, $options);
	}

	/**
	 * Retrieves the Subnet Null_Route object matching the provided Id.
	 * @param  int 		$id 						The Id.
	 * @return ColoCrossing_Object_NullRoute|null	The Subnet Null Route
	 */
	public function getNullRoute($id)
	{
		$null_routes = $this->getNullRoutes();

		return ColoCrossing_Utility::getObjectFromCollectionById($null_routes, $id);
	}

	/**
	 * Adds a Null Route to an Ip Address on this Subnet.
	 * @param string 	$ip_address  The Ip Address
	 * @param string 	$comment     The Comment Explaing the Reason for the Null Route
	 * @param int 		$expire_date The Date The Null Route is to Expire as a Unix Timestamp.
	 *                           		Defaults to 4 hrs from now. Max of 30 days from now.
	 * @return boolean|ColoCrossing_Object_NullRoute
	 *         						 The new Null Route object if successful, false otherwise.
	 */
	public function addNullRoute($ip_address, $comment = '', $expire_date = null)
	{
		$client = $this->getClient();

		return $client->null_routes->add($this, $ip_address, $comment, $expire_date);
	}

	/**
	 * Retrieves the list of Reverse DNS Record objects.
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Subnet_ReverseDNSRecord>	The Subnet Reverse DNS Records
	 */
	public function getReverseDNSRecords(array $options = null)
	{
		if (!$this->isReverseDnsEnabled())
		{
			return array();
		}

		return $this->getResourceChildCollection('rdns_records', $options);
	}

	/**
	 * Retrieves the Reverse DNS Record object matching the provided Id.
	 * @param  int 		$id 									The Id.
	 * @return ColoCrossing_Object_Subnet_ReverseDNSRecord|null	The Subnet Reverse DNS Record
	 */
	public function getReverseDNSRecord($id)
	{
		if (!$this->isReverseDnsEnabled())
		{
			return null;
		}

		return $this->getResourceChildObject('rdns_records', $id);
	}

	/**
	 * Updates Multiple Reverse DNS Records in this Subnet all at once.
	 * @param  array<array(id, value)> $rdns_records  List of Arrays that have an id attribute with the Id
	 *                                  				of the record and a value attribute with the value
	 *                                  				of the record.
	 * @return boolean|int 		True if successful, false otherwise. If a ticket to review the request
	 *                            	must be created, then the ticket id is returned.
	 */
	public function updateReverseDNSRecords(array $rdns_records)
	{
		$resource = $this->getResource();

		return $resource->rdns_records->updateAll($this, $rdns_records);
	}

	/**
	 * Computes the Total Number of Ip Addesses in the Subnet Accoring to the CIDR.
	 * @return int The Total Number of Ip Addresses
	 */
	public function getNumberOfIpAddresses()
	{
		$cidr = intval($this->getCidr());
		return pow(2, 32 - $cidr);
	}

	/**
	 * Retrieves a list of all Ip Addresses in the Subnet
	 * @return array<string> The list of Ip Addresses
	 */
	public function getIpAddresses()
	{
		$start_ip = $this->getIpAddress();
		$ip_parts = split('\.', $start_ip);
		$last_ip_part = intval(array_pop($ip_parts));
		$ip_prefix = implode('.', $ip_parts);

		$num_ips = $this->getNumberOfIpAddresses();
		$ips = array();

		for ($i = 0; $i < $num_ips; $i++)
		{
			$ip_suffix = $last_ip_part + $i;
			$ips[] = $ip_prefix . '.' . $ip_suffix;
		}

		return $ips;
	}

	/**
	 * Computes the Total Number of Usable Ip Addesses in the Subnet Accoring to the CIDR.
	 * @return int The Total Number of Usable Ip Addresses
	 */
	public function getNumberOfUsableIpAddresses()
	{
		$cidr = intval($this->getCidr());

		switch ($cidr) {
			case '32':
				return 1;
			case '31':
				return 0;
		}

		return $this->getNumberOfIpAddresses() - 3;
	}

	/**
	 * Gets the First Usable IP Address of this Subnet
	 * @return string The First Usable IP Address
	 */
	public function getFirstUsableIpAddress()
	{
		return long2ip(ip2long($this->getIpAddress()) + 2);
	}

	/**
	 * Gets the First Usable IP Address of this Subnet
	 * @return string The First Usable IP Address
	 */
	public function getLastUsableIpAddress()
	{
		return long2ip(ip2long($this->getIpAddress()) + $this->getNumberOfIpAddresses() - 2);
	}

	/**
	 * Gets the Mask for this Subnet
	 * @return string The Mask
	 */
	public function getMask() {
		$cidr = intval($this->getCidr());
    	$mask = array_map(function($part) {
    		return bindec($part);
    	}, str_split(str_pad(str_pad('', $cidr, '1'), 32, '0'), 8));
    	return join('.', $mask);
	}

	/**
	 * Gets the Gateway for this Subnet
	 * @return string The Gateway
	 */
	public function getGateway()
	{
		return long2ip(ip2long($this->getIpAddress()) + 1);
	}

	/**
	 * Get the Broadcast address for this Subnet
	 * @return string The Broadcast
	 */
	public function getBroadcast()
	{
		return long2ip(ip2long($this->getIpAddress()) + $this->getNumberOfIpAddresses() - 1);
	}

	/**
	 * Determines if the provided Ip Address is part of the Subnet
	 * @param  string  $ip_address The Ip Address
	 * @return boolean             True if in Subnet, false otherwise
	 */
	public function isIpAddressInSubnet($ip_address)
	{
        $start_ip = ip2long($this->getIpAddress());
        $end_ip = $start_ip + $this->getNumberOfIpAddresses() - 1;

        $ip_address = ip2long($ip_address);

        return $start_ip <= $ip_address && $end_ip >= $ip_address;
	}

	/**
	 * Retrieves the Ip Address in CIDR Notation
	 * @return string The Ip Address in CIDR Notation
	 */
	public function getCIDRIpAddress()
	{
		return $this->getIpAddress() . '/' . $this->getCidr();
	}

}
