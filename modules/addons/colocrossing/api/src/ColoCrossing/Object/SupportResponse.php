<?php

/**
 * Represents an instance of a Support Response resource from the API.
 * Holds data for a Support Response and provides methods to retrive
 * objects related to the Support Response such as its Ticket and Creator
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_SupportResponse extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Creation Date of the Support Response as
	 * a Unix Timestamp.
	 * @return int The Date the Support Response was Created.
	 */
	public function getDateCreated()
	{
		$created_at = $this->getValue('created_at');

		return $created_at && isset($created_at) ? strtotime($created_at) : null;
	}

	/**
	 * Retrieves the User object.
	 * @return ColoCrossing_Object_User The Creator of the Response.
	 */
	public function getUser()
	{
		return $this->getObject('user', null, 'user');
	}

	/**
	 * Retreives the Support Ticket object that the Response belongs to.
	 * @return ColoCrossing_Object_SupportTicket 	The Responses's Ticket
	 */
	public function getTicket()
	{
		$client = $this->getClient();

		return $this->getObject('ticket', $client->support_tickets);
	}

	/**
	 * Retrieves the ticket Id that this response belongs to
	 * @return int|null The Ticket Id
	 */
	public function getTicketId()
	{
		$ticket = $this->getValue('ticket');

		if(empty($ticket) || !is_array($ticket))
		{
			return null;
		}

		return $ticket['id'];
	}

	/**
	 * Retrieves the ticket subject that this response belongs to
	 * @return int|null The Ticket subject
	 */
	public function getTicketSubject()
	{
		$ticket = $this->getValue('ticket');

		if(empty($ticket) || !is_array($ticket))
		{
			return null;
		}

		return $ticket['subject'];
	}


}
