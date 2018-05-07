<?php

/**
 * Represents the Ipmi Configuration of a Device. Provides methods for 
 * retrieving the hostname and, if one exists, information about the 
 * null route, specifically its status and the time it will be replaced
 * if it is lifted. 
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_IpmiConfiguration extends ColoCrossing_Object
{
	/**
	 * Retrieve the hostname ipmi is configured to.
	 * 
	 * @return string	The Hostname
	 */
	public function getHostname()
	{
		$hostname = $this->getValue('hostname');

		return empty($hostname) ? null : $hostname;
	}

	/**
	 * Retrieve an array with information about the null route, including its status
	 * and the time it will be replaced if it is currently lifted.
	 * 
	 * @return array	An associative array describing the null route.
	 */
	public function getNullRoute()
	{
		$null_route = $this->getValue('null_route');

		return empty($null_route) ? null : $null_route;
	}

	/**
	 * Retrieve an array with information about the null route's status.
	 * Contains an 'id' and a 'name' as follows:
	 *  1 => 'active',
	 *  2 => 'lifted',
	 *  3 => 'awaiting routing', (temporarily removed and awaiting a routing change.)
	 *  4 => 'archived'
	 * 
	 * @return array	An associative array describing the null route.
	 */
	public function getNullRouteStatus()
	{
		$null_route = $this->getNullRoute();

		return isset($null_route) ? $null_route['status'] : null;
	}

	/**
	 * Retrieve the time the null route will be replaced if it is currently lifted.
	 * 
	 * @return int	A timestamp for when the null will be replaced.
	 */
	public function getNullRouteLiftExpiration()
	{
		$status = $this->getNullRouteStatus();

		if(isset($status))
		{
			if($status['id'] == 2)
			{
				$null_route = $this->getNullRoute();
				return $null_route['time'];
			}
		}

		return null;
	}

}
