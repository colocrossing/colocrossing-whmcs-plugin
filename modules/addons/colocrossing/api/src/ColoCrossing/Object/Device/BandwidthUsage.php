<?php

/**
 * Holds data corresponding to the bandwidth usage for a switch port.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_Device_BandwidthUsage extends ColoCrossing_Object
{

	/**
	 * Retrieves the Start Date of the current billing period as
	 * a Unix Timestamp.
	 * @return int 	The Start Date of the current billing period
	 */
	public function getStartDate()
	{
		$start_date = $this->getValue('start_date');

		return $start_date && isset($start_date) ? strtotime($start_date) : null;
	}

	/**
	 * Retrieves the End Date of the current billing period as
	 * a Unix Timestamp.
	 * @return int 	The End Date of the current billing period
	 */
	public function getEndDate()
	{
		$end_date = $this->getValue('end_date');

		return $end_date && isset($end_date) ? strtotime($end_date) : null;
	}

	/**
	 * Retrieves the datetime the bandwidth usage was last updated as
	 * a Unix Timestamp.
	 * @return int 	The Datetime the data was last updated.
	 */
	public function getDateUpdatedAt()
	{
		$updated_at = $this->getValue('updated_at');

		return $updated_at && isset($updated_at) ? strtotime($updated_at) : null;
	}

}
