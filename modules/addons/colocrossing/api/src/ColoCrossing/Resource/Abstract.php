<?php

/**
 * The base implementation for accessing a specific Resource of
 * the API. Handles Creating the URL of the resource and creating
 * the Http Request Object and sending it to be executed and retrieving
 * the content.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @abstract
 */
abstract class ColoCrossing_Resource_Abstract implements ColoCrossing_Resource
{

	/**
	 * The API Client
	 * @var ColoCrossing_Client
	 */
	private $client;

	/**
	 * The Resource Name singular form
	 * @var string
	 */
	private $name;

	/**
	 * The Field Name singular form
	 * @var string
	 */
	private $field_name;

	/**
	 * The Suffix to The Resource/Field Name in its Plural form.
	 * Default value is `s`
	 * @var string
	 */
	private $plural_name_suffix;

	/**
	 * The Url of the Resource Relative to the root of parent.
	 * @var string
	 */
	private $url;

	/**
	 * The Child Resources
	 * @var ColoCrossing_Resource_Child_Abstract
	 */
	private $child_resources;

	/**
	 * @param ColoCrossing_Client 	$client The API Client
	 * @param string  $name   The Resource Name. If string, it is assumed the
	 *                                      	singular form is provided and the plural form
	 *                                      	is created by appending an 's'.
	 * @param string              	$url    The Url of the Resource Relative to the root of parent.
	 */
	public function __construct(ColoCrossing_Client $client, $name, $url, $field_name = null, $plural_name_suffix = null)
	{
		$this->client = $client;
		$this->url = $url;

		$this->child_resources = array();

		$this->name = $name;
		$this->field_name = isset($field_name) ? $field_name : $name;
		$this->plural_name_suffix = isset($plural_name_suffix) ? $plural_name_suffix : 's';
	}

	/**
	 * @return ColoCrossing_Client The API Client of this Resource
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @param  boolean $plural 	Specifies if the plural form of the name is wanted.
	 * @return string         	The resource name. If $plural is true the name is in plural form,
	 *                            otherwise it is in singular form.
	 */
	public function getName($plural = false)
	{
		return $this->name . ($plural ? $this->plural_name_suffix : '');
	}

		/**
	 * @param  boolean $plural 	Specifies if the plural form of the name is wanted.
	 * @return string         	The field name. If $plural is true the name is in plural form,
	 *                            otherwise it is in singular form.
	 */
	public function getFieldName($plural = false)
	{
		return $this->field_name . ($plural ? $this->plural_name_suffix : '');
	}

	/**
	 * @return string The Base Url of this Resource relative the root of API.
	 */
	public function getURL()
	{
		return $this->url;
	}

	/**
	 * Retrieves a Child Resource if it is available. If the resource is available, but has not
	 * been created yet, then the resource is created and returned. Otherwise the already
	 * existing resource is returned.
	 * @param  string 		$child_name 			The name of the child resource requested.
	 * @return ColoCrossing_Resource_Child_Abstract The child resource
	 */
	public function getChildResource($child_name)
	{
		$parent_name = $this->getName(true);
		$available_child_resources = ColoCrossing_Resource_Child_Factory::getAvailableChildResources($parent_name);

		if (empty($available_child_resources) || empty($available_child_resources[$child_name]))
		{
			return null;
		}

		if (empty($this->child_resources[$child_name]))
		{
			$this->child_resources[$child_name] = ColoCrossing_Resource_Child_Factory::createChildResource($parent_name, $child_name, $this->client);
		}

		return $this->child_resources[$child_name];
	}

	/**
	 * Handles Magic Loading of Child Resources
	 * @param  string $name 			The function called.
	 * @return ColoCrossing_Resource    The resource.
	 */
	public function __get($name)
	{
		$parent_name = $this->getName(true);
		$available_child_resources = ColoCrossing_Resource_Child_Factory::getAvailableChildResources($parent_name);

		if (isset($available_child_resources) && isset($available_child_resources[$name]))
		{
			return $this->getChildResource($name);
		}
	}

	/**
	 * Retrieves a List of ColoCrossing_Object from this Resource
	 * @param  array 			$options 	An Array of Options to Adjust the Result. Includes filters,
	 *											sort, page_number, and page_size.
	 * @param  int 				$parent_id 	The Parent Id, 	Only Used For Child Resources
	 * @return ColoCrossing_Collection<ColoCrossing_Object>	A List of ColoCrossing_Object
	 */
	public function findAll(array $options = null, $parent_id = null)
	{
		$options = $this->createCollectionOptions($options);
		$url = $this->createCollectionUrl($parent_id);

		if (isset($options['format']))
		{
			switch ($options['format'])
			{
				case 'paged':
					return new ColoCrossing_PagedCollection($this, $url, $options['page_number'], $options['page_size'], $options['sort'], $options['filters']);
				case 'map':
					$collection = new ColoCrossing_Collection($this, $url, $options['sort'], $options['filters']);
					return ColoCrossing_Utility::getMapCollection($collection);
			}
		}

		return new ColoCrossing_Collection($this, $url, $options['sort'], $options['filters']);
	}

	/**
	 * Retrieves a ColoCrossing_Object from this Resource
	 * @param  int 			$id     	The Id
	 * @param  int 			$parent_id 	The Parent Id, 	Only Used For Child Resources
	 * @return ColoCrossing_Object		The ColoCrossing_Object
	 */
	public function find($id, $parent_id = null)
	{
		$url = $this->createObjectUrl($id, $parent_id);

		return $this->fetch($url);
	}

	/**
	 * Retrieves a List of ColoCrossing_Object from this Resource
	 * @param  string 			$url     	The Url of the Resource relative the root of the API.
	 * @param  array 			$options 	An Array of Options to Adjust the Result. Includes filters,
	 *											sort, page_number, and page_size.
	 * @return array(array<ColoCrossing_Object>, int)	A List of ColoCrossing_Object from the Url and the Total Record Count
	 */
	public function fetchAll($url, array $options = null)
	{
		$options = $this->createCollectionOptions($options);

		$request = $this->createRequest($url);

		$query_params = array(
			'page' => $options['page_number'],
			'limit' => $options['page_size'],
			'sort' => implode(',', $options['sort'])
		);
		foreach ($options['filters'] as $filter => $value)
		{
			$query_params[$filter] = $value;
		}
		$request->setQueryParams($query_params);

		$response = $this->executeRequest($request);
		list($content, $total_record_count) = $this->getResponseContent($response, true);

		if (empty($content) || empty($total_record_count))
		{
			return array(array(), 0);
		}

		$records = ColoCrossing_Object_Factory::createObjectArray($this->client, $this, $content);

		return array($records, $total_record_count);
	}

	/**
	 * Retrieves a ColoCrossing_Object from this Resource
	 * @param  string 		$url    The Url of the Resource relative the root of the API.
	 * @return ColoCrossing_Object	The ColoCrossing_Object from the Url
	 */
	public function fetch($url)
	{
		$response = $this->sendRequest($url);
		$content = $this->getResponseContent($response, false);

		if (empty($content))
		{
			return null;
		}

		return ColoCrossing_Object_Factory::createObject($this->client, $this, $content);
	}

	/**
	 * Creates a valid Options array for a Collection
	 * @param  array|null $options 	The Options Provided for the Collection
	 * @return array          		The Verified Collection Options
	 */
	protected function createCollectionOptions(array $options = null)
	{
		$options = isset($options) && is_array($options) ? $options : array();

		$options['format'] = isset($options['format']) ? $options['format'] : 'collection';
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['sort'] = isset($options['sort']) ? (is_array($options['sort']) ? $options['sort'] : array($options['sort']) ) : array();
		$options['page_number'] = isset($options['page_number']) ? max($options['page_number'], 1) : 1;
		$options['page_size'] = isset($options['page_size']) ? $options['page_size'] : $this->client->getOption('page_size');
		$options['page_size'] = max(min($options['page_size'], 100), 1);

		return $options;
	}

	/**
	 * Creates the Url that refers to the Collection/Index of this Resource
	 * @param  int $parent_id 	The Parent Id, Only Used For Child Resources
	 * @return string 			The Url
	 */
	protected function createCollectionUrl($parent_id = null)
	{
		return $this->url;
	}

	/**
	 * Creates the Url that refers to a Object in this Resource.
	 * @param  int $id 			The Object Id
	 * @param  int $parent_id 	The Parent Id, Only Used For Child Resources
	 * @return string   		The Url
	 */
	protected function createObjectUrl($id, $parent_id = null)
	{
		return $this->url . '/' . urlencode($id);
	}

	/**
	 * Creates a Http Request to the API, Executes it, and returns the Http Response
	 * @param  string 		$url    			The Url
	 * @param  string 		$method 			The Http Method. GET, POST, PUT, or DELETE
	 * @param  array  		$data   			The Data to be sent as the Body. Sent in the
	 *                            					query string for a GET request.
	 * @return ColoCrossing_Http_Response|null	The Http Response
	 */
	protected function sendRequest($url, $method = 'GET', $data = array())
	{
		$request = $this->createRequest($url, $method, $data);
		return $this->executeRequest($request);
	}

	/**
	 * Creates a Http Request to the API
	 * @param  string 		$url    		The Url
	 * @param  string 		$method 		The Http Method. GET, POST, PUT, or DELETE
	 * @param  array  		$data   		The Data to be sent as the Body. Sent in the
	 *                            				query string for a GET request.
	 * @return ColoCrossing_Http_Request	The Http Request
	 */
	protected function createRequest($url, $method = 'GET', $data = array())
	{
		return new ColoCrossing_Http_Request($url, $method, $data);
	}

	/**
	 * Executes the Http Request and Returns the Http Repsonse
	 * @param  ColoCrossing_Http_Request  $request 	The Http Request
	 * @return ColoCrossing_Http_Response|null		The Http Response
	 */
	protected function executeRequest(ColoCrossing_Http_Request $request)
	{
		$executor = $this->client->getHttpExecutor();

		try
		{
			return $executor->executeRequest($request);
		}
		catch(ColoCrossing_Error_NotFound $e)
		{
			return null;
		}
	}

	/**
	 * Retrieves the Content from the Http Response
	 * @param  ColoCrossing_Http_Response	$response      	The Http Response
	 * @param  boolean						$is_collection 	Specifies if the content is expected
	 *                                     						as a collection
	 * @return array|null                 					The Content
	 */
	protected function getResponseContent(ColoCrossing_Http_Response $response = null, $is_collection = false)
	{
		if (empty($response))
		{
			return null;
		}

		$data = $response->getContent();
		$name = $this->getFieldName($is_collection);

		$content = isset($data) && isset($data[$name]) && is_array($data[$name]) ? $data[$name] : null;

		if($is_collection)
		{
			return array($content, $data['total_record_count']);
		}

		return $content;
	}

}
