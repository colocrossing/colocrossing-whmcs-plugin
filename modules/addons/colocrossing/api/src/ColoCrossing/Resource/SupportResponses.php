<?php

/**
 * Handles retrieving data from the API's Support Response resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_SupportResponses extends ColoCrossing_Resource_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client, 'support_response', '/support/responses', 'response');
	}

	/**
	 * Retrieve a Collection of Responses assigned to the provided ticket id.
	 * @param  integer 	$ticket_id 	The ticket id to filter by
	 * @param  array 	$options    	The Options for the page and sort.
	 * @return array|ColoCrossing_Collection<ColoCrossing_Object_SupportResponse> The Responses
	 */
	public function findByTicket($ticket_id, array $options = null)
	{
		$options = isset($options) && is_array($options) ? $options : array();
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['ticket'] = $ticket_id;

		return $this->findAll($options);
	}

}
