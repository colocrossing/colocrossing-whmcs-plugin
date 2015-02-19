<?php

/**
 * Represents an instance of a DDoS Zone resource from the API.
 * Holds data for a DDoS Zone and provides methods to retrive
 * objects related to the DDoS Zone such as its Owner and Traffic
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_DdosZone extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Traffic object.
	 * @return ColoCrossing_Object The Traffic
	 */
	public function getTraffic()
	{
		return $this->getObject('traffic');
	}

	/**
	 * Retrieves the Owner User object.
	 * @return ColoCrossing_Object_User The Owner
	 */
	public function getOwner()
	{
		return $this->getObject('owner', null, 'user');
	}

	/**
	 * Retrieves the attacks
	 * @param  array 	$options 		The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_DdosZone_Attack>	The Attacks
	 */
	public function getAttacks(array $options = null)
	{
		return $this->getResourceChildCollection('attacks', $options);
	}

}
