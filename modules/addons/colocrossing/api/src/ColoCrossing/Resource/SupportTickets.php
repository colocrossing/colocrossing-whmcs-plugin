<?php

/**
 * Handles retrieving data from the API's Support Ticket resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_SupportTickets extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'support_ticket', '/support/tickets', 'ticket');
	}

	/**
	 * Retrieve a Collection of Tickets assigned to the provided department id.
	 * @param  integer 	$department_id 	The department id to filter by
	 * @param  array 	$options    	The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_SupportTicket> The Tickets
	 */
	public function findByDepartment($department_id, array $options = null)
	{
		$options = isset($options) && is_array($options) ? $options : array();
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['department'] = $department_id;

		return $this->findAll($options);
	}


}
