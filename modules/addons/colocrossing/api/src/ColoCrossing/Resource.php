<?php

/**
 * Inteface for a Resource In the API
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
interface ColoCrossing_Resource
{

	/**
	 * Retrieves a List of ColoCrossing_Object from this Resource
	 * @param  string 			$url     	The Url of the Resource relative the root of the API.
	 * @param  array 			$options 	An Array of Options to Adjust the Result. Includes filters,
	 *											sort, page_number, and page_size.
	 * @return array(array<ColoCrossing_Object>, int)	A List of ColoCrossing_Object from the Url and the Total Record Count
	 */
	public function fetchAll($url, array $options = null);

	/**
	 * Retrieves a ColoCrossing_Object from this Resource
	 * @param  string 		$url    The Url of the Resource relative the root of the API.
	 * @return ColoCrossing_Object	The ColoCrossing_Object from the Url
	 */
	public function fetch($url);

	/**
	 * @return ColoCrossing_Client The API Client of this Resource
	 */
	public function getClient();

	/**
	 * @param  boolean $plural 	Specifies if the plural form of the name is wanted.
	 * @return string         	The name of the resource. If $plural is true the name is in plural form,
	 *                            otherwise it is in singular form.
	 */
	public function getName($plural);

	/**
	 * @return string The Base Url of this Resource relative the root of API.
	 */
	public function getURL();

}
