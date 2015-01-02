<?php

/**
 * Represents an instance of a User. Holds data for a User.
 * User can either be a client or a subuser. If this is a client,
 * then it is the client associated with the API token.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_User extends ColoCrossing_Object
{

	/**
	 * @return string The type of user. Either client or subuser.
	 */
	public function getType()
	{
		$type = $this->getValue('type');

		return isset($type) && $type == 'client' ? $type : 'subuser';
	}

}
