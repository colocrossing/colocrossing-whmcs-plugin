<?php

/**
 * Represents an instance of a Bandwidth Usage resource from the API.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_BandwidthUsage extends ColoCrossing_Resource_Object
{

	/**
	 * @return int The Device Id
	 */
	public function getDeviceId()
	{
		$device = $this->getValue('device');

		return intval($device['id']);
	}

	/**
	 * @return string The Device Name
	 */
	public function getDeviceName()
	{
		$device = $this->getValue('device');

		return $device['name'];
	}

	/**
	 * Start Date of the Metering Period as
	 * a Unix Timestamp.
	 * @return int The Start Date of the Metering Period
	 */
	public function getStartDate()
	{
		$start_date = $this->getValue('start_date');

		return $start_date && isset($start_date) ? strtotime($start_date) : null;
	}

	/**
	 * End Date of the Metering Period as
	 * a Unix Timestamp.
	 * @return int The End Date of the Metering Period
	 */
	public function getEndDate()
	{
		$end_date = $this->getValue('end_date');

		return $end_date && isset($end_date) ? strtotime($end_date) : null;
	}

}
