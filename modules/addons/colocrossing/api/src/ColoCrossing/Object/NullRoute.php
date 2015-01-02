<?php

/**
 * Represents an instance of a Null Route resource from the API.
 * Holds data for a Null Route and provides methods to retrive
 * objects related to the null route such as its Subnet.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_NullRoute extends ColoCrossing_Resource_Object
{

	/**
	 * Flag for if this Null Route has been removed.
	 * @var boolean
	 */
	private $is_removed = false;

	/**
	 * Retrieves the Creation Date of the Null Route as
	 * a Unix Timestamp.
	 * @return int The Date The Null Route was Added.
	 */
	public function getDateAdded()
	{
		$date_added = $this->getValue('date_added');

		return $date_added && isset($date_added) ? strtotime($date_added) : null;
	}

	/**
	 * Retrieves the Expiration Date of the Null Route as
	 * a Unix Timestamp.
	 * @return int The Date The Null Route Expires.
	 */
	public function getDateExpire()
	{
		$date_expire = $this->getValue('date_expire');

		return $date_expire && isset($date_expire) ? strtotime($date_expire) : null;
	}

	/**
	 * Retrieves the Subnet object that this Null Route is on.
	 * @return ColoCrossing_Object_Subnet|null	The Subnet
	 */
	public function getSubnet()
	{
		$client = $this->getClient();

		return $this->getObject('subnet', $client->subnets);
	}

	/**
	 * Retrieves the Subnet id that this Null Route is on.
	 * @return int|null	The Subnet Id
	 */
	public function getSubnetId()
	{
		$subnet = $this->getValue('subnet');

		return isset($subnet) && is_array($subnet) ? intval($subnet['id']) : null;
	}


	/**
	 * Removes this Null Route.
	 * @return boolean	True if the removal suceeds, false otherwise.
	 */
	public function remove()
	{
		if($this->is_removed)
		{
			return false;
		}

		$client = $this->getClient();

		if($client->null_routes->remove($this))
		{
			return $this->is_removed = true;
		}

		return false;
	}

}
