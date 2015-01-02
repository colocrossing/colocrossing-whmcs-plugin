<?php

/**
 * An extension to the generic ColoCrossing_Object that adds support for
 * linking the Object to the resource it originated from.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_Object extends ColoCrossing_Object
{

	/**
	 * The Resource This Object is Created From
	 * @var ColoCrossing_Resource
	 */
	private $resource;

	/**
	 * @param ColoCrossing_Client   $client   The API Client
	 * @param ColoCrossing_Resource $resource The Resource
	 * @param array                 $values   The Values
	 */
	public function __construct(ColoCrossing_Client $client, ColoCrossing_Resource $resource, array $values = array())
	{
		parent::__construct($client, $values);

		$this->resource = $resource;
	}

	/**
	 * @return ColoCrossing_Resource The Resource
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * Retrieves a Collection of objects from a Child Resource
	 * @param  string 	child_type 	The Child Type
	 * @param  array 	$options    The Options of the page and sorting.
	 * @return ColoCrossing_Collection<ColoCrossing_Object> The Collection of objects
	 */
	protected function getResourceChildCollection($child_type, array $options = null)
	{
		$parent_id = $this->getId();
		$child_resource = $this->resource->$child_type;

		return $child_resource->findAll($options, $parent_id);
	}

	/**
	 * Retrieves a Object from a Child Resource
	 * @param  string 	$child_type 	The Child Type
	 * @param  int 		$child_id   	The Child Id
	 * @return ColoCrossing_Object 		The Object
	 */
	protected function getResourceChildObject($child_type, $child_id)
	{
		$parent_id = $this->getId();
		$child_resource = $this->resource->$child_type;

		return $child_resource->find($child_id, $parent_id);
	}

}
