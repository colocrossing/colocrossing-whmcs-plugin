<?php

/**
 * The Base Object for Any Data that is Returned from the API.
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object
{

	/**
	 * The API Client
	 * @var ColoCrossing_Client
	 */
	private $client;

	/**
	 * The Values this Object Holds
	 * @var array
	 */
	private $values;

	/**
	 * The ColoCrossing_Object's this has
	 * @var array<ColoCrossing_Object>
	 */
	private $objects;

	/**
	 * The Collections of ColoCrossing_Objects this has.
	 * @var array<Iterable<ColoCrossing_Object>>
	 */
	private $object_arrays;

	/**
	 * @param ColoCrossing_Client $client The API Client
	 * @param array               $values The Values of the Object
	 */
	public function __construct(ColoCrossing_Client $client, array $values = array())
	{
		$this->client = $client;
		$this->values = $values;

		$this->objects = array();
		$this->object_arrays = array();
	}

	/**
	 * @return ColoCrossing_Client The API Client of this Resource
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @return array The Array of Values this Object Holds.
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @param  string $key 	The key to one the this object's values
	 * @return mixed      	The value corresponding to the key if present, false otherwise.
	 */
	public function getValue($key)
	{
		return isset($this->values[$key]) ? $this->values[$key] : false;
	}

	/**
	 * Sets the value of the local object. Does Not Update anything in the API.
	 * @param string 	$key   The key to one the this object's values.
	 * @param mixed 	$value The new value.
	 */
	public function setValue($key, $value)
	{
		return $this->values[$key] = $value;
	}

	/**
	 * @return int The Id of this object.
	 */
	public function getId()
	{
		$id = $this->getValue('id');
		return $id && isset($id) ? intval($id) : null;
	}

	/**
	 * @return string The Object in JSON form.
	 */
	public function __toJSON()
	{
		if (defined('JSON_PRETTY_PRINT'))
		{
      		return json_encode($this->__toArray(), JSON_PRETTY_PRINT);
    	}

    	return json_encode($this->__toArray());
	}

	/**
	 * @return string The Object in string form.
	 */
	public function __toString()
	{
    	$class = get_class($this);
    	return $class . ' JSON: ' . $this->__toJSON();
	}

	/**
	 * @return array The Object's Values.
	 */
	public function __toArray()
	{
		return $this->values;
	}

	/**
	 * Handles the Magic get__ and is__ functions to access Object values.
	 * @param  string 	$name      	The function name called
	 * @param  array 	$arguments  The arguments passed.
	 * @return mixed             	The value.
	 */
	public function __call($name, $arguments)
    {
    	$name = ColoCrossing_Utility::convertCamelCaseToSnakeCase($name);
    	$name_parts = explode('_', $name);

    	if (count($name_parts) <= 1)
    	{
    		return null;
    	}

    	$type = array_shift($name_parts);
    	$name = implode('_', $name_parts);

    	if (!isset($this->values[$name]) && !array_key_exists($name, $this->values))
        {
        	return null;
        }

    	switch ($type) {
    		case 'get':
    			return $this->values[$name];
    		case 'is':
    			return !!$this->values[$name];
    	}

        return null;
    }

	/**
	 * @return array<ColoCrossing_Object> The ColoCrossing_Object's this has
	 */
	protected function getObjects()
	{
		return $this->objects;
	}

	/**
	 * Retrieves a ColoCrossing_Object from the values of this object.
	 * @param  string 					$key      The key of the value that holds the data for the object.
	 * @param  ColoCrossing_Resource 	$resource The API Resource to load this from. If null it uses the key's
	 *                                         	  	value for data instead of loading from resource
	 * @param  string 					$type     The type of Object to create. If $resource is present this is ignored.
	 *                          	    			If null it creates generic Object.
	 * @param  mixed 					$default  The value to return if the key's value is not found.
	 * @return ColoCrossing_Object 				  The Object.
	 */
	protected function getObject($key, ColoCrossing_Resource $resource = null, $type = null, $default = null, $load = true)
	{
		if (isset($this->objects[$key]))
		{
			return $this->objects[$key];
		}

		$value = $this->getValue($key);

		if ($value && is_array($value))
		{
			if (isset($resource) && isset($value['id']) && $load)
			{
				return $this->objects[$key] = $resource->find($value['id']);
			}
			return $this->objects[$key] = ColoCrossing_Object_Factory::createObject($this->client, $resource, $value, $type);
		}

		return $default;
	}

	/**
	 * Retreive an Object from a Resource with the provided Id
	 * @param  int                	 $id       The Id of the Object.
	 * @param  string                $key      The key to Store the Object at.
	 * @param  ColoCrossing_Resource $resource The Resource to Retrieve from.
	 * @return ColoCrossing_Object             The Object.
	 */
	protected function getObjectById($id, $key, ColoCrossing_Resource $resource)
	{
		if (isset($this->objects[$key]))
		{
			return $this->objects[$key];
		}

		return $this->objects[$key] = $resource->find($id);
	}

	/**
	 * @return array<Iterable<ColoCrossing_Object>> The Collections of ColoCrossing_Objects this has.
	 */
	protected function getObjectArrays()
	{
		return $this->object_arrays;
	}

	/**
	 * Retrieves a List of ColoCrossing_Object from the values of this object.
	 * @param  string 					$key      The key of the value that holds the data for the object.
	 * @param  ColoCrossing_Resource 	$resource The API Resource to load this from. If null it uses the key's
	 *                                         	  	value for data instead of loading from resource
	 * @param  string 					$type     The type of Object to create. If $resource is present this is ignored.
	 *                          	    			If null it creates generic Object.
	 * @param  mixed 					$default  The value to return if the key's value is not found.
	 * @param  array 					$additional_data  Extra Data to add to each object when not loading from a resource.
	 * @return Iterable<ColoCrossing_Object>	  The List of ColoCrossing_Object's.
	 */
	protected function getObjectArray($key, ColoCrossing_Resource $resource = null, $type = null, $default = null, array $additional_data = null, $ignore_unauthorized = false)
	{
		if (isset($this->object_arrays[$key]))
		{
			return $this->object_arrays[$key];
		}

		$value = $this->getValue($key);

		if ($value && is_array($value))
		{
			if (empty($resource))
			{
				return $this->object_arrays[$key] = ColoCrossing_Object_Factory::createObjectArray($this->client, $resource, $value, $type, $additional_data);
			}

			$this->object_arrays[$key] = array();
			foreach ($value as $index => $content)
			{
				try {
					$object = $resource->find($content['id']);
					$this->object_arrays[$key][] = $object;
				} catch (ColoCrossing_Error_Authorization $e) {
					if(!$ignore_unauthorized) {
						throw $e;
					}
				}
			}
			return $this->object_arrays[$key];
		}

		return $default;
	}

}
