<?php

require_once 'api/ColoCrossing.php';
require_once 'models/Event.php';

/**
 * ColoCrossing Module for WHMCS Module
 * Handles Module Configuration, Activation, and Deactivation
 */
class ColoCrossing_Module {

	/**
	 * The Singleton Module Instance
	 * @var ColoCrossing_Module
	 */
	private static $instance;

	/**
	 * The Router
	 * @var ColoCrossing_Router
	 */
	private $router;

	/**
	 * The API Client
	 * @var ColoCrossing_Client
	 */
	private $api_client;

	/**
	 * Private Constructor to Prevent Instantiation Outside of this Class
	 */
	private function __construct() {}

	/**
	 * Activates the ColoCrossing Module.
	 * Makes a required database changes for this module.
	 *
	 * @return array The result
	 */
	public function activate() {
		// Create Devices Table
	    $query = "CREATE TABLE `mod_colocrossing_devices` (" .
	                "`service_id` int(10) NOT NULL," .
	                "`device_id` int(10) NOT NULL," .
	                "PRIMARY KEY (`service_id`, `device_id`)" .
	             ");";
	    full_query($query);

	    // Create Events Table
	    $query = "CREATE TABLE `mod_colocrossing_events` (" .
	                "`id` int(10) NOT NULL AUTO_INCREMENT," .
	                "`user_type` tinyint(1) NOT NULL," .
	                "`user_id` int(10) NOT NULL," .
	                "`service_id` int(10) NOT NULL," .
	                "`description` varchar(255) NOT NULL," .
	                "`time` int(10) NOT NULL," .
	                "PRIMARY KEY (`id`)" .
	             ");";
	    full_query($query);

	    // Return Result
	    return array(
	        'status' => 'success',
	        'description' => 'Specify your API Key in the module configuration to get started.'
	    );
	}

	/**
	 * Deactivates the ColoCrossing Module.
	 * Removes database changes made by this module.
	 *
	 * @return array The result
	 */
	public function deactivate() {
	    // Remove Devices Table
	    $query = "DROP TABLE `mod_colocrossing_devices`";
	    $result = full_query($query);

	    // Remove Events Table
	    $query = "DROP TABLE `mod_colocrossing_events`";
	    $result = full_query($query);

	    // Return Result
	    return array(
	        'status' => 'success'
	    );
	}

	/**
	 * Gets the Default Configuration for this Module
	 *
	 * @return array The Default Config
	 */
	public function getDefaultConfiguration() {
	    return array(
	    	'name' => 'ColoCrossing Portal',
	    	'description' => 'This is an addon module that can be used to interact with the ColoCrossing Portal.',
	    	'version' => '1.0',
	    	'author' => 'ColoCrossing',
	    	'language' => 'english',
	    	'fields' => $this->getConfigurationFields()
	    );
	}

	/**
	 * Get the configuration fields available
	 *
	 * @return array The Fields
	 */
	public function getConfigurationFields() {
	    return array(
        	'api_key' => array(
        		'FriendlyName' => 'API Key',
        		'Type' => 'text',
        		'Size' => '50'
        	)
   	    );
	}

	/**
	 * Get configuration of the module
	 *
	 * @return array The Config
	 */
	public function getConfiguration() {
		$configuration = $this->getDefaultConfiguration();

	    $results = select_query('tbladdonmodules', 'setting,value', array('module' => 'colocrossing'));

	    while ($data = mysql_fetch_array($results)) {
	    	$configuration[$data['setting']] = $data['value'];
		}

		return $configuration;
	}

	/**
	 * Dispatch a Request to the Controller
	 *
	 * @param  array  $params
	 */
	public function dispatchRequest(array $params = array()) {
		$router = $this->getRouter();
		$params = array_merge($params, $_POST, $_GET);

		$router->dispatch($params);
	}

	/**
	 * Get the router for this module
	 *
	 * @return ColoCrossing_Router
	 */
	public function getRouter() {
		if(empty($this->router)) {
			require_once 'Router.php';

			$routes = array(
				'devices' => array('index', 'view', 'bandwidth-graph', 'update-power-ports', 'update-network-ports'),
				'subnets' => array('index', 'view', 'update'),
				'null-routes' => array('index', 'create', 'destroy'),
				'events' => array('index')
			);
			$default_route = array(
				'controller' => 'devices',
				'action' => 'index'
			);

			$this->router = new ColoCrossing_Router($routes, $default_route);
		}

		return $this->router;
	}

	/**
	 * Get the API Key
	 *
	 * @return string The API Key
	 */
	public function getAPIKey() {
		$configuration = $this->getConfiguration();

	    return isset($configuration['api_key']) ? $configuration['api_key'] : null;
	}

	/**
	 * Get the API Client for this module
	 *
	 * @return ColoCrossing_Client
	 */
	public function getAPIClient() {
		if(empty($this->api_client)) {
			$api_key = $this->getAPIKey();
			$options = array(
				'application_name' => 'ColoCrossing WHMCS Module',
				'api_url' => 'http://portal.matt/api/',
				'ssl_verify' => false,
			);

			$this->api_client = new ColoCrossing_Client($api_key, $options);
		}

		return $this->api_client;
	}

	/**
	 * Returns the Singleton Instance of this class.
	 *
	 * @return ColoCrossing_Module The Instance
	 */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ColoCrossing_Module();
        }

        return self::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * singleton instance.
     */
    private function __clone() {}

    /**
     * Private unserialize method to prevent unserializing of the singleton
     * instance.
     */
    private function __wakeup() {}

}
