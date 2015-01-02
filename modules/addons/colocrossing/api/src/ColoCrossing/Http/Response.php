<?php

/**
 * Holds All the Results of Http Request Being Executed.
 * @category   ColoCrossing
 * @package    ColoCrossing_Http
 */
class ColoCrossing_Http_Response
{

	/**
	 * The Response Body
	 * @var string
	 */
	private $body;

	/**
	 * The Parsed Content of the Body
	 * @var mixed
	 */
	private $content;

	/**
	 * The Content Type
	 * @var string
	 */
	private $content_type;

	/**
	 * The HTTP Code
	 * @var int
	 */
	private $code;

	/**
	 * @param string  $body         The Response Body
	 * @param integer $code         The HTTP Code
	 * @param string  $content_type The Content Type
	 */
	public function __construct($body, $code = 200, $content_type = null)
	{
		$this->body = $body;
		$this->code = $code;
		$this->content_type = $content_type;

		$this->setContent();
	}

	/**
	 * @return string The Response Body
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @return int The HTTP Code
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return mixed The Parsed Content of the Body
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return string The Content Type
	 */
	public function getContentType()
	{
		return $this->content_type;
	}

	/**
	 * Parses the Content of the Body According to the Content Type
	 */
	private function setContent()
	{
		switch ($this->getContentType()) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				$this->content = imagecreatefromstring($this->body);

				if (is_bool($this->content) && !$this->content)
				{
					throw new ColoCrossing_Error('ColoCrossing API Error - Image is corrupt or in an unsupported format.');
				}
				break;
			case 'application/json':
				$this->content = json_decode($this->body, true);

				if (isset($this->content) && isset($this->content['status']) && $this->content['status'] == 'error')
				{
					$this->throwAPIError();
				}
				break;
			default:
				$this->content = null;
				break;
		}
	}

	/**
	 * Throws API Exceptions Base on the Content Returned
	 */
	private function throwAPIError()
	{
		switch ($this->content['type']) {
			case 'api_token_missing':
			case 'unauthorized':
			case 'inactive':
				throw new ColoCrossing_Error_Authorization($this->code, $this->content);
				break;
			case 'missing_resource':
				throw new ColoCrossing_Error_NotFound($this->content);
				break;
			default:
				throw new ColoCrossing_Error_Api($this->code, $this->content);
				break;
		}
	}

}
