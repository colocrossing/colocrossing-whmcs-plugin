<?php

/**
 * An Error For when Authorizing in the API Fails
 * @category   ColoCrossing
 * @package    ColoCrossing_Error
 */
class ColoCrossing_Error_Authorization extends ColoCrossing_Error_Api
{

	/**
	 * @param int 		$status  The HTTP Status
	 * @param array   	$content The Response's Content
	 */
	public function __construct($status = 403, array $content = array())
	{
		parent::__construct($status, $content, 'ColoCrossing API Authorization Error');
	}

}
