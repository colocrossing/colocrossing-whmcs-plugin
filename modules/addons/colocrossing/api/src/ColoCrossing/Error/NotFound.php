<?php

/**
 * Error For when an API Resource is not found.
 * @category   ColoCrossing
 * @package    ColoCrossing_Error
 */
class ColoCrossing_Error_NotFound extends ColoCrossing_Error_Api
{

	/**
	 * @param array $content The Response's Content
	 */
	public function __construct(array $content = array())
	{
		parent::__construct(404, $content, 'ColoCrossing API Resource Not Found Error');
	}

}
