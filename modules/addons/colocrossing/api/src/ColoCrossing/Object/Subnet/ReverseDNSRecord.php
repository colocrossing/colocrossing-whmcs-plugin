<?php

/**
 * Represents an instance of a Subnet's Reverse DNS Record resource
 * from the API. Holds data for a Subnet's Reverse DNS Record and
 * provides methods to retrive objects related to the rDNS record
 * such as its Subnet.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Subnet
 */
class ColoCrossing_Object_Subnet_ReverseDNSRecord extends ColoCrossing_Resource_Object
{

	/**
	 * Updates this Reverse DNS Record's value with the one provided.
	 * @param  string $value 	The new record value.
	 * @return boolean|int 		True if successful, false otherwise. If a ticket to review the request
	 *                            	must be created, then the ticket id is returned.
	 */
	public function update($value)
	{
		$subnet = $this->getSubnet();

		if (empty($subnet))
		{
			return false;
		}

		$resource = $this->getResource();
		$result = $resource->update($subnet, $this, $value);

		if ($result)
		{
			$this->setValue('record', $value);
		}

		return $result;
	}

	/**
	 * Retrieves the Subnet object that this Reverse DNS Record is on.
	 * @return ColoCrossing_Object_Subnet|null	The Subnet
	 */
	public function getSubnet()
	{
		$client = $this->getClient();

		return $this->getObject('subnet', $client->subnets);
	}

}
