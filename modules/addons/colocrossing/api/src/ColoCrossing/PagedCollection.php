<?php

/**
 * A Paged Collection used for iterating through all Paginated Resources
 * in the API page by page.
 * @category   ColoCrossing
 */
class ColoCrossing_PagedCollection extends ColoCrossing_AbstractCollection
{

	/**
	 * The Resource this Pulls From
	 * @var ColoCrossing_Resource
	 */
	private $resource;

	/**
	 * The Url this Pulls From
	 * @var string
	 */
	private $url;

	/**
	 * The Total Number of Records in the Resource that match the Filters
	 * @var int
	 */
	private $total_record_count;

	/**
	 * The Max Number of Records in a Page
	 * @var int
	 */
	private $page_size;

	/**
	 * The Sort Fields
	 * @var array<string>
	 */
	private $sort;

	/**
	 * The Values to Filter By
	 * @var array<string, string|int>
	 */
	private $filters;

	/**
	 * Constructs a Paged Collection for the Provided Resource
	 * @param ColoCrossing_Resource 	$resource    The Resource To Pull From
	 * @param string                	$url         The Resource URL To Pull From
	 * @param integer               	$page_size   The Page Size, Defaults to 30
	 * @param array<string>             $sort        The Sort Fields
	 * @param array<string, string|int> $filters     The Values to Filter By
	 */
	public function __construct(ColoCrossing_Resource $resource, $url, $page_number = 1, $page_size = 30, array $sort = array(), array $filters = array())
	{
		$this->resource = $resource;
		$this->url = $url;
		$this->total_record_count = null;
		$this->page_size = max(min($page_size, 100), 1);
		$this->sort = $sort;
		$this->filters = $filters;

		$this->get($page_number);
	}

	public function getTotalRecordCount()
	{
		return $this->total_record_count;
	}

	/**
	 * Returns the number of Pages available in this collection
	 * @return integer The Number of Pages
	 */
	public function size()
	{
		return ceil($this->total_record_count / $this->page_size);
	}

	/**
	 * Retrieves the  specified page from the Collection
	 */
	public function get($index)
	{
		return parent::get($index - 1);
	}

	/**
	 * Returns the Current Page Number
	 * @return integer The Current Page Number
	 */
	public function key()
	{
		return parent::key() + 1;
	}

	/**
	 * Resets the Collection to the start
	 */
	public function reset()
	{
		$this->get(1);
	}

	/**
	 * Fetches the Current Element from the Remote Source
	 * @return mixed The Current Element
	 */
	protected function fetch()
	{
		if(isset($this->total_record_count) && !$this->valid())
		{
			return array();
		}

		list($page, $total_record_count) = $this->resource->fetchAll($this->url, array(
			'page_number' => $this->key(),
			'page_size' => $this->page_size,
			'sort' => $this->sort,
			'filters' => $this->filters
		));

		if(is_null($this->total_record_count))
		{
			$this->total_record_count = $total_record_count;
		}

		if($this->total_record_count != $total_record_count)
		{
			throw new Exception('Concurrent Modification Occurred! The number of remote records changed.');
		}

		return $page;
	}

}