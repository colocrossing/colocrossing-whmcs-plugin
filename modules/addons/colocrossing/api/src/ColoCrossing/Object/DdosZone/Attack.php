<?php

/**
 * Represents an instance of a DDoS Zone Attack resource
 * from the API. Holds data for a DDoS Zone Attack.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_DdosZone
 */
class ColoCrossing_Object_DdosZone_Attack extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Type object.
	 * @return ColoCrossing_Object The Type
	 */
	public function getType()
	{
		return $this->getObject('type');
	}

}
