<?php

/**
 * An Abstract Collection for iterating through Paginated Resources.
 * @category   ColoCrossing
 */
abstract class ColoCrossing_AbstractCollection implements Iterator, Countable
{

	/**
	 * The Current Index
	 * @var integer
	 */
	private $index;

	/**
	 * The Element at the Current Index
	 * @var mixed
	 */
	private $current;

	/**
	 * Returns the number of elements available in this collection
	 * @return integer The Number of Elements
	 */
	public abstract function size();

	/**
	 * Fetches the Current Element from the Remote Source
	 * @return mixed The Current Element
	 */
	protected abstract function fetch();

	/**
	 * Retrieves the specified element from the Collection
	 * @param integer $index The Position
	 */
	public function get($index)
	{
		if(!isset($this->index))
		{
			$this->index = max($index, -1);
			$this->current = $this->fetch();

			$this->index = min($this->index, $this->size());
			return $this->current;
		}

		if($index == $this->index)
		{
			return $this->current;
		}

		$this->index = min(max($index, -1), $this->size());

		return $this->current = $this->fetch();
	}

	/**
	 * Returns the Current Index
	 * @return integer The Current Index
	 */
	public function key()
	{
		return $this->index;
	}

	/**
	 * Determines if the Current Page With the Specifed Offset Exists
	 * @param  integer $offset 	Defaults to 0
	 * @return boolean  		True if the page is valid
	 */
	public function valid($offset = 0)
	{
		$index = $this->index + $offset;

		return $index >= 0 && $index < $this->size();
	}

	/**
	 * Moves the Collection to the Next Position
	 */
	public function next()
	{
		$this->get($this->index + 1);
	}

	/**
	 * Moves the Collection to the Previous Position
	 */
	public function previous()
	{
		$this->get($this->index - 1);
	}

	/**
	 * Resets the Collection to the start
	 */
	public function reset()
	{
		$this->get(0);
	}

	/**
	 * Retrieves the element current position
	 * @return mixed The element
	 */
    public function current()
    {
        return $this->get($this->key());
    }

	/**
	 * Determines if there are any pages
	 * @return boolean True if >= 1 pages exist
	 */
	public function isEmpty()
	{
		return $this->size() == 0;
	}

	/**
	 * Determines if the next is valid
	 * @return boolean True if the next exists
	 */
	public function hasNext()
	{
		return $this->valid(1);
	}

	/**
	 * Determines if the previous is valid
	 * @return boolean True if the previous exists
	 */
	public function hasPrevious()
	{
		return $this->valid(-1);
	}

	/**
	 * Alias Reset Method for Iterable Interface
	 */
	public function rewind()
	{
        $this->reset();
    }

    /**
	 * Alias size Method for Countable Interface
	 */
    public function count()
    {
        return $this->size();
    }

}