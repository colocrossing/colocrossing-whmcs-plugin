<?php

/**
 * Holds all the necessary data for making an HTTP Request
 * @category   ColoCrossing
 * @package    ColoCrossing_Http
 */
class ColoCrossing_Http_Request
{

	/**
	 * The Url relative to the API Root.
	 * @var string
	 */
	private $url;

	/**
	 * The Http Method
	 * @var string
	 */
	private $method = 'GET';

	/**
	 * The Http Headers
	 * @var array
	 */
	private $headers = array();

	/**
	 * The Query Parameters to be appended to the URL
	 * @var array
	 */
	private $queryParams = array();

	/**
	 * The Data to be sent as POST Parameters
	 * @var array
	 */
	private $data = array();

	/**
	 * @param string $url         The Url relative to the API Root.
	 * @param string $method      The Http Method
	 * @param array  $data        The Data to be sent as POST Parameters
	 * @param array  $queryParams The Query Parameters to be appended to the URL
	 * @param array  $headers     The Http Headers
	 */
	public function __construct($url, $method = 'GET', array $data = array(), array $queryParams = array(), array $headers = array())
	{
		$this->setUrl($url);

		$this->setMethod($method);
		$this->setHeaders($headers);

		$this->setQueryParams($queryParams);
		$this->setData($data);
	}

	/**
	 * @param string $url The Url relative to the API Root.
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string The Url relative to the API Root.
	 */
	public function getUrl()
	{
		return $this->url . $this->getQueryString();
	}

	/**
	 * @param string $method The Http Method
	 */
	public function setMethod($method)
	{
		$this->method = strtoupper($method);
	}

	/**
	 * @return string The Http Method
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param array $headers The Http Headers
	 */
	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
	}

	/**
	 * @param string $key   The Specific Header Name
	 * @param string $value The Value
	 */
	public function setHeader($key, $value)
	{
	    $this->headers[$key] = $value;
	}

	/**
	 * @return array The Http Headers
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param array $queryParams The Query Parameters to be appended to the URL
	 */
	public function setQueryParams(array $queryParams)
	{
		$this->queryParams = $queryParams;
	}

	/**
	 * @param string $key   The Specific Query Parameter Name
	 * @param string $value The Value
	 */
	public function setQueryParam($key, $value)
	{
	    $this->queryParams[$key] = $value;
	}

	/**
	 * @return array The Query Parameters to be appended to the URL
	 */
	public function getQueryParams()
	{
		return $this->queryParams;
	}

	/**
	 * Creates the Query String from the Query Parameters.
	 * If the Method is GET, The data values are included with the query params.
	 * @return string The Query String to be Appended to the URL
	 */
	public function getQueryString()
	{
		$params = $this->getQueryParams();

		if ($this->getMethod() == 'GET')
		{
			$params = array_merge($params, $this->getData());
		}

		return count($params) ? '?' . http_build_query($params) : '';
	}

	/**
	 * @param array $data The Data to be sent as POST Parameters
	 */
	public function setData(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @return array The Data to be sent as POST Parameters
	 */
	public function getData()
	{
		return $this->data;
	}

}
