<?php

/**
 * A General Utility Class For Misc Helper Methods
 */
class ColoCrossing_Utilities {

	/**
	 * Returns the parsed contents of a template.
	 * @param  string $file The Absolute Path to the Template
	 * @param  array  $data The Data to Extract into the Template
	 * @return string 		The Parsed Template
	 */
	public static function parseTemplate($file, array $data = array()) {
	    extract($data);
	    ob_start();
	    require $file;
	    return ob_get_clean();
	}

	/**
	 * Creates a URL with the provided Query String Params
	 * @param  string $url    	The URL
	 * @param  array  $params 	The Query String Params
	 * @return string 			The URL with The Query Params
	 */
	public static function buildUrl($url, array $params = array()) {
		$parts = parse_url($url);
		parse_str($parts['query'], $query);

		$params = isset($query) && is_array($query) ?  array_merge($query, $params) : $params;

		return explode('?', $url)[0] . '?' . http_build_query($params);
	}

	/**
	 * Combines the statuses from a collection of Switches/PDUs into one if they all match.
	 * Returns 'Mixed' if statuses differ.
	 * @param  ColoCrossing_Collection $devices A collection of Switches or PDUs
	 * @return string|null  	Possible Values include 'On', 'Off', 'Down', 'Rebooting', Error', 'Mixed', or null
	 */
	public static function getDeviceOverallStatus($devices) {
		$statuses = array();

		foreach ($devices as $i => $device) {
			$ports = $device->getPorts();

            foreach ($device->getPorts() as $j => $port) {
            	$status = $port->getStatus();

            	if(!empty($status)) {
            		$statuses[] = $status;
            	}
            }
        }

        $statuses = array_unique($statuses);

        if(empty($statuses)) {
        	return null;
        }

        return count($statuses) > 1 ? 'Mixed' : $statuses[0];
	}

	/**
	 * Converts Port Status to Description for use in messages
	 * @param  string $status
	 * @return string The Status Description
	 */
	public static function getPortStatusDescription($status) {
		switch ($status) {
			case 'on':
				return 'turned on';
			case 'off':
				return 'turned off';
			case 'restart':
				return 'restarted';
		}

		return 'controlled';
	}

	public static function getPortStatusColor($status) {
		$status = strtolower($status);

		switch ($status) {
			case 'on':
				return 'green';
			case 'off':
				return 'grey';
			case 'down':
				return 'purple';
			case 'error':
				return 'red';
			case 'restart':
			case 'rebooting':
				return 'orange';
			case 'mixed':
				return 'black';
		}

        return null;
	}

	public static function isDeviceControllable($devices) {
		$controllable = false;

		foreach ($devices as $i => $device) {
			$ports = $device->getPorts();

            foreach ($device->getPorts() as $j => $port) {
            	$controllable |= $port->isControllable();
            }
        }

        return $controllable;
	}

}
