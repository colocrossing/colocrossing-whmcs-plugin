<?php

/**
 * Represents an instance of a Announcement resource from the API.
 * Holds data for a Announcement and provides methods to retrive
 * objects related to the Announcement such as its Devices.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_Announcement extends ColoCrossing_Resource_Object
{

	/**
	 * Retrieves the Creation Date of the Announcement as
	 * a Unix Timestamp.
	 * @return int The Date The Announcement was Created.
	 */
	public function getCreateDate()
	{
		$created_at = $this->getValue('created_at');

		return $created_at && isset($created_at) ? strtotime($created_at) : null;
	}

	/**
	 * Retrieves the Start Date of the Announcement as
	 * a Unix Timestamp.
	 * @return int The Date The Announcement Period Starts.
	 */
	public function getStartDate()
	{
		$start_at = $this->getValue('start_at');

		return $start_at && isset($start_at) ? strtotime($start_at) : null;
	}

	/**
	 * Retrieves the End Date of the Announcement as
	 * a Unix Timestamp.
	 * @return int The Date The Announcement Period Ends.
	 */
	public function getEndDate()
	{
		$end_at = $this->getValue('end_at');

		return $end_at && isset($end_at) ? strtotime($end_at) : null;
	}

	/**
	 * Retrieves the Type object.
	 * @return ColoCrossing_Object The Type
	 */
	public function getType()
	{
		return $this->getObject('type');
	}

	/**
	 * Retrieves the Severity object.
	 * @return ColoCrossing_Object The Severity
	 */
	public function getSeverity()
	{
		return $this->getObject('severity');
	}

	/**
	 * Retrieves the Devices that are associated with the Announcement
	 * @return ColoCrossing_Collection<ColoCrossing_Object_Device>  The Announcement's Devices
	 */
	public function getDevices(array $options = null)
	{
		$client = $this->getClient();

		return $client->devices->findByAnnouncement($this->getId(), $options);
	}

}
