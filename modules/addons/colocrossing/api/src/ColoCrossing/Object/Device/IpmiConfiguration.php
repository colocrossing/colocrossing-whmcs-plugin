<?php

/**
 * Represents the Ipmi Configuration of a Device. Provides methods for 
 * retrieving the hostname and, if one exists, information about the 
 * null route, specifically its status and the time it will be replaced
 * if it is lifted. Also prvides methods for lifting, renewing a lift, 
 * and replacing a null route. 
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 * @subpackage ColoCrossing_Object_Device
 */
class ColoCrossing_Object_Device_IpmiConfiguration extends ColoCrossing_Object
{

	public function getHostname()
	{
		$hostname = $this->getValue('hostname');

		return empty($hostname) ? null : $hostname;
	}

	public function getNullRoute()
	{
		$null_route = $this->getValue('null_route');

		return empty($null_route) ? null : $null_route;
	}

	public function getNullRouteStatus()
	{
		$null_route = $this->getNullRoute();

		return isset($null_route) ? $null_route['status'] : null;
	}

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

	public function liftNullRoute()
	{
		if(in_array($this->getNullRouteStatus(), array(2, 4)))
		{
			return false;
		}

		file_put_contents('/tmp/url.txt', 'hi');

		$client = $this->getClient();
		return $client->devices->ipmi_null_route->setNullRouteStatus($this, $this->getDeviceId(), 'lift');
	}

	public function replaceNullRoute()
	{
		if($this->getNullRouteStatus() != 2)
		{
			return false;
		}

		$client = $this->getClient();
		return $client->devices->ipmi_null_route->setNullRouteStatus($this, $this->getDeviceId(), 'replace');
	}

	public function renewNullRouteLift()
	{
		if($this->getNullRouteStatus() != 2)
		{
			return false;
		}

		$client = $this->getClient();
		return $client->devices->ipmi_null_route->setNullRouteStatus($this, $this->getDeviceId(), 'renew');
	}
}
