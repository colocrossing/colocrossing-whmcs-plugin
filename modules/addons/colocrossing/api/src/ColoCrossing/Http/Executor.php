<?php

/**
 * This Handles Taking a ColoCrossing_Http_Request, executing it and creating a
 * ColoCrossing_Http_Response with the result.
 * @category   ColoCrossing
 * @package    ColoCrossing_Http
 */
class ColoCrossing_Http_Executor
{

	/**
	 * The cURL Handle
	 * @var resource
	 */
	private $curl;

	/**
	 * The API Client
	 * @var ColoCrossing_Client
	 */
	private $client;

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Executes a Http Request
	 * @param  ColoCrossing_Http_Request $request 	The Request to be Executed.
	 * @return ColoCrossing_Http_Response     		The Response to the Request.
	 */
	public function executeRequest(ColoCrossing_Http_Request $request)
	{
		$this->createCurl();
		$this->setCurlRequestOptions($request);

		return $this->executeCurl();
	}

	/**
	 * Creates a cURL Handle and sets some Default Options.
	 * @return resource The cURL Handle.
	 */
	private function createCurl()
	{
		if (isset($this->curl))
		{
			$this->destroyCurl();
		}

		$this->curl = curl_init();

		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, $this->client->getOption('ssl_verify') ? 2 : 0);
    	curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, $this->client->getOption('ssl_verify'));
    	curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($this->curl, CURLOPT_HEADER, false);

		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $this->client->getOption('follow_redirects'));
    	curl_setopt($this->curl, CURLOPT_USERAGENT, $this->client->getOption('application_name') . '/' . $this->client->getVersion());
    	curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->client->getOption('connection_timeout'));
    	curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->client->getOption('request_timeout'));

    	return $this->curl;
	}

	/**
	 * Retrieves the Current cURL Handle. Creates it if not exists.
	 * @return resource The cURL Handle.
	 */
	private function getCurl()
	{
		if (empty($this->curl))
		{
			return $this->createCurl();
		}

		return $this->curl;
	}

	/**
	 * Sets the current cURL Handle's Options that Correspond to the current Request.
	 * @param ColoCrossing_Http_Request $request The Request
	 */
	private function setCurlRequestOptions(ColoCrossing_Http_Request $request)
	{
		$this->setCurlHeaders($request->getHeaders());

		$curl = $this->getCurl();
		curl_setopt($curl, CURLOPT_URL, $this->client->getBaseUrl() . $request->getUrl());
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());

        $request_data = $request->getData();
        if (count($request_data) && $request->getMethod() != 'GET')
        {
        	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request_data));
        }
	}

	/**
	 * Sets the current cURL Handle's Headers
	 * @param array $headers The Headers to be set.
	 */
	private function setCurlHeaders(array $headers = array())
	{
		$headers['X-API-Token'] = $this->client->getAPIToken();

		$curl = $this->getCurl();
	    $curl_headers = array();

	    foreach ($headers as $key => $value) {
	        $curl_headers[] = "$key: $value";
	    }

	    curl_setopt($curl, CURLOPT_HTTPHEADER, $curl_headers);
	}

	/**
	 * Executes the Current cURL Handle and Creates a Response to hold the Result
	 * @return ColoCrossing_Http_Response The Response.
	 */
	private function executeCurl()
	{
		$curl = $this->getCurl();

		$body = curl_exec($curl);

		if (is_bool($body) && !$body)
		{
			throw new ColoCrossing_Error('Unable to make connection to ColoCrossing API.');
		}

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

		$this->destroyCurl();

		return new ColoCrossing_Http_Response($body, $code, $content_type);
	}

	/**
	 * Destroys the current cURL handle if it exists.
	 * @return boolean	True if action occured.
	 */
	private function destroyCurl()
	{
		if (empty($this->curl))
		{
			return false;
		}

		curl_close($this->curl);
		$this->curl = null;

		return true;
	}

}
