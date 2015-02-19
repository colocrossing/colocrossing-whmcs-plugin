<?php

/**
 * Represents an instance of a Support Department resource from the API.
 * Holds data for a Support Department and provides methods to retrive
 * objects related to the Support Department such as its Tickets.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_SupportDepartment extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Tickets that are assigned to this Department.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_SupportTicket> 	The Tickets
	 */
	public function getTickets(array $options = null)
	{
		$client = $this->getClient();

		return $client->support_tickets->findByDepartment($this->getId(), $options);
	}

}
