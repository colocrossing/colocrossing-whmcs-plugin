<?php

/**
 * Generic Exception For All Errors in The API
 * @category   ColoCrossing
 * @package    ColoCrossing_Error
 */
class ColoCrossing_Error extends Exception
{

	/**
	 * The Error Type
	 * @var string
	 */
	private $type;

	/**
	 * @param string $message The Message.
	 * @param string $type    The Type of Error.
	 */
	public function __construct($message, $type = 'server_error')
	{
		parent::__construct($message);

		$this->type = $type;
	}


	/**
	 * @return string The Type of Error.
	 */
	public function getType()
	{
		return $this->type;
	}

}
