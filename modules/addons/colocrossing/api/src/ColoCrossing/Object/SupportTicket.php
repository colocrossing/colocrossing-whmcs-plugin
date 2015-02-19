<?php

/**
 * Represents an instance of a Support Ticket resource from the API.
 * Holds data for a Support Ticket and provides methods to retrive
 * objects related to the Support Ticket such as its Department, Responses, and Devices.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_SupportTicket extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Creation Date of the Support Ticket as
	 * a Unix Timestamp.
	 * @return int The Date The Support Ticket was Created.
	 */
	public function getDateCreated()
	{
		$created_at = $this->getValue('created_at');

		return $created_at && isset($created_at) ? strtotime($created_at) : null;
	}

	/**
	 * Retrieves the Last Updated Date of the Support Ticket as
	 * a Unix Timestamp.
	 * @return int The Date The Support Ticket was Updated.
	 */
	public function getDateUpdated()
	{
		$updated_at = $this->getValue('updated_at');

		return $updated_at && isset($updated_at) ? strtotime($updated_at) : null;
	}

	/**
	 * Retrieves the Status object.
	 * @return ColoCrossing_Object The Status
	 */
	public function getStatus()
	{
		return $this->getObject('status');
	}

	/**
	 * Retrieves the Priority object.
	 * @return ColoCrossing_Object The Priority
	 */
	public function getPriority()
	{
		return $this->getObject('priority');
	}

	/**
	 * Retrieves the Department object.
	 * @return ColoCrossing_Object The Department
	 */
	public function getDepartment()
	{
		$client = $this->getClient();

		return $this->getObject('department', $client->support_departments, null, null, false);
	}

	/**
	 * Retrieves the Assigned User object.
	 * @return ColoCrossing_Object_User The Assigned User
	 */
	public function getAssignee()
	{
		return $this->getObject('assignee', null, 'user');
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
	 * Retrieves the Updater User object.
	 * @return ColoCrossing_Object_User The Updater
	 */
	public function getUpdatedBy()
	{
		return $this->getObject('updated_by', null, 'user');
	}

	/**
	 * Retrieves the Devices that are assigned to this Ticket.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device> 	The Ticket's Devices
	 */
	public function getDevices(array $options = null)
	{
		$client = $this->getClient();

		return $client->devices->findByTicket($this->getId(), $options);
	}

	/**
	 * Retrieves the Responses that this Ticket has.
	 * @return ColoCrossing_Collection<ColoCrossing_Object_SupportResponse> 	The Ticket's Responses
	 */
	public function getResponses(array $options = null)
	{
		$client = $this->getClient();

		return $client->support_responses->findByTicket($this->getId(), $options);
	}

}
