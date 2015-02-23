<?php

/**
 * The base implementation for accessing a specific  Sub-Resource of
 * the API. Handles Creating the URL of the sub-resource by using the
 * parent resource.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child
 * @abstract
 */
abstract class ColoCrossing_Resource_Child_Abstract extends ColoCrossing_Resource_Abstract
{

	/**
	 * The Parent Resource
	 * @var ColoCrossing_Resource
	 */
	private $parent_resource;

	/**
	 * @param ColoCrossing_Resource $parent_resource	The Parent Resource
	 * @param ColoCrossing_Client 	$client 			The API Client
	 * @param string|array<string>  $name   			The Resource Name. If string, it is assumed the
	 *                                      	  			singular form is provided and the plural form
	 *                                      	     		is created by appending an 's'.
	 * @param string              	$url    			The Url of the Resource Relative to the root of parent.
	 */
	public function __construct(ColoCrossing_Resource $parent_resource, ColoCrossing_Client $client, $name, $url, $field_name = null, $plural_name_suffix = null)
	{
		parent::__construct($client, $name, $url, $field_name, $plural_name_suffix);

		$this->parent_resource = $parent_resource;
	}

	/**
	 * @return ColoCrossing_Resource The Parent Resource
	 */
	public function getParentResource()
	{
		return $this->parent_resource;
	}

	/**
	 * Creates the Url that refers to the Collection/Index of this Child Resource
	 * @param  int 	$parent_id 	The Parent Id
	 * @return string 			The Url
	 */
	protected function createCollectionUrl($parent_id)
	{
		return $this->parent_resource->createObjectUrl($parent_id) . $this->getUrl();
	}

	/**
	 * Creates the Url that refers to a Object in this Resource.
	 * @param  int $id 			The Object Id
	 * @param  int $parent_id 	The Parent Id
	 * @return string   		The Url
	 */
	protected function createObjectUrl($id, $parent_id)
	{
		return  $this->parent_resource->createObjectUrl($parent_id) . $this->getUrl() . '/' . urlencode($id);
	}

}
