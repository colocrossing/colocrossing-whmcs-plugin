<?php

/**
 * Factory Class that handles creating all Objects that inherit from
 * ColoCrossing_Resource by using the provided type.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_Factory
{

	/**
	 * The Available Resources and the Files they are Available in
	 * @var array<string, string>
	 * @static
	 */
	private static $AVAILABLE_RESOURCES = array(
		'bandwidth_usages' => '/BandwidthUsages.php',
		'devices' => '/Devices.php',
		'networks' => '/Networks.php',
		'null_routes' => '/NullRoutes.php',
		'subnets' => '/Subnets.php'
	);

	/**
	 * Creates a Resource according to the provided Type.
	 * @param  string              $type   	The Type of Resources
	 * @param  ColoCrossing_Client $client 	The API Client
	 * @return ColoCrossing_Resource      	The Resource Created
	 * @throws ColoCrossing_Error 			If Type is not Found
	 */
	public static function createResource($type, ColoCrossing_Client $client)
	{
		$available_resources = self::getAvailableResources();

		if (isset($available_resources[$type]))
		{
			require_once(dirname(__FILE__) . $available_resources[$type]);
		}

		switch ($type)
		{
			case 'bandwidth_usages':
				return new ColoCrossing_Resource_BandwidthUsages($client);
			case 'devices':
				return new ColoCrossing_Resource_Devices($client);
			case 'networks':
				return new ColoCrossing_Resource_Networks($client);
			case 'null_routes':
				return new ColoCrossing_Resource_NullRoutes($client);
			case 'subnets':
				return new ColoCrossing_Resource_Subnets($client);
		}

		throw new ColoCrossing_Error('ColoCrossing API Resource not found.');
	}

	/**
	 * Retrieves the Available Resources
	 * @return array<string, string>	The Available Resources and the Class Files.
	 */
	public static function getAvailableResources()
	{
		return self::$AVAILABLE_RESOURCES;
	}

}
