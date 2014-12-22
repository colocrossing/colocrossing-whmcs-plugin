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

}
