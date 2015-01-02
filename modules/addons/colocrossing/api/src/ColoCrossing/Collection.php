<?php

/**
 * A Collection used for iterating through all Paginated Resources
 * in the API.
 * @category   ColoCrossing
 */
class ColoCrossing_Collection extends ColoCrossing_AbstractCollection
{

	/**
	 * The Size of Page to Load
	 */
	const PAGE_SIZE = 100;

	/**
	 * The Collection of Pages
	 * @var ColoCrossing_PagedCollection
	 */
	private $pages;

	/**
	 * Constructs a Collection According to the Provided Sort and Filter Parameters
	 * for the provided Resource
	 * @param ColoCrossing_Resource 	$resource    The Resource To Pull From
	 * @param string                	$url         The Resource URL To Pull From
	 * @param array<string>             $sort        The Sort Fields
	 * @param array<string, string|int> $filters     The Values to Filter By
	 */
	public function __construct(ColoCrossing_Resource $resource, $url, array $sort = array(), array $filters = array())
	{
		$this->pages = new ColoCrossing_PagedCollection($resource, $url, 1, self::PAGE_SIZE, $sort, $filters);
	}

	/**
	 * Returns the number of records available in this collection
	 * @return integer The Number of Records
	 */
	public function size()
	{
		return $this->pages->getTotalRecordCount();
	}

	/**
	 * Fetches the Current Element from the Remote Source
	 * @return mixed The Current Element
	 */
	protected function fetch()
	{
		$page = $this->pages->get(floor($this->key() / self::PAGE_SIZE) + 1);
		return $page[$this->key() - ($this->pages->key() - 1) * self::PAGE_SIZE];
	}

}
