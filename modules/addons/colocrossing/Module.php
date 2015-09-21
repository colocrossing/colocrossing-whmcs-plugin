<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require 'Router.php';
require 'Controller.php';
require 'Utilities.php';
require 'Model.php';
require 'API.php';
require 'Event.php';

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
	 * The Client and Admin Routers for this
	 * @var array<ColoCrossing_Router>
	 */
	private $routers;

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
	    $query = "CREATE TABLE `mod_colocrossing_devices_services` (" .
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
	    $query = "DROP TABLE `mod_colocrossing_devices_services`";
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
	    	'version' => '1.2.0',
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
		$support_department_names = ColoCrossing_Model_SupportDepartment::getNames();
		$support_status_names = ColoCrossing_Model_SupportStatus::getNames();

	    return array(
        	'api_key' => array(
        		'FriendlyName' => 'API Key',
        		'Description' => 'The token provided to you when creating the key in the ColoCrossing Portal.',
        		'Type' => 'text',
        		'Size' => '50'
        	),
        	'api_hook_secret' => array(
        		'FriendlyName' => 'API Webhook Secret',
        		'Description' => 'The secret used when creating the webhook in the ColoCrossing Portal.',
        		'Type' => 'text',
        		'Size' => '50'
        	),
        	'system_username' => array(
        		'FriendlyName' => 'System Username',
        		'Description' => 'The user that will be used to perform automated tasks such as creating tickets.',
        		'Type' => 'text',
        		'Size' => '50'
        	),
        	'abuse_ticket_department' => array (
        		'FriendlyName' => 'Abuse Ticket Department',
        		'Type' => 'dropdown',
        		'Options' => implode(',', $support_department_names),
        		'Description' => 'The department that abuse tickets will be assigned to.'
        	),
        	'abuse_ticket_status' => array (
        		'FriendlyName' => 'Abuse Ticket Status',
        		'Type' => 'dropdown',
        		'Options' => implode(',', $support_status_names),
        		'Description' => 'The status that abuse tickets will be set to upon opening.'
        	)
   	    );
	}

	/**
	 * Get configuration of the module
	 *
	 * @return array The Config
	 */
	public function getConfiguration() {
		$configuration = array();

	    $results = select_query('tbladdonmodules', 'setting,value', array('module' => 'colocrossing'));

	    while ($data = mysql_fetch_array($results)) {
	    	$configuration[$data['setting']] = $data['value'];
		}

		return $configuration;
	}

	/**
	 * @return string The Base Admin URL
	 */
	public function getBaseAdminUrl() {
		return BASE_ADMIN_URL;
	}

	/**
	 * @return string The Base Client URL
	 */
	public function getBaseClientUrl() {
		return BASE_CLIENT_URL;
	}

	/**
	 * Get the router of the specified type
	 *
	 * @return ColoCrossing_Admins_Router
	 */
	public function getRouter($type = 'admin') {
		if(isset($this->routers[$type])) {
			return $this->routers[$type];
		}

		switch ($type) {
			case 'admin':
				return $this->routers[$type] = new ColoCrossing_Admins_Router();
			case 'client':
				return $this->routers[$type] = new ColoCrossing_Clients_Router();
		}

		throw new Exception('Unkown router type specified.');
	}

	/**
	 * Dispatch a Request to the Controller
	 *
	 * @param string  $type 	The type of router to send request to.
	 * @param  array  $params 	The Parameters to pass to router
	 * @return mixed  			The Result of the Action
	 */
	public function dispatchRequest($type, array $params = array()) {
		$router = $this->getRouter($type);
		$params = array_merge($_POST, $_GET, $params);

		return $router->dispatch($params);
	}

	/**
	 * Dispatch a Request to the Controller and Action Specified
	 *
	 * @param string  $type 		The type of router to send request to.
	 * @param string  $controller 	The contoller
	 * @param string  $action 		The action
	 * @param  array  $params 		The Parameters to pass to router
	 * @return mixed  				The Result of the Action
	 */
	public function dispatchRequestTo($type, $controller, $action, array $params = array()) {
		$params['controller'] = $controller;
		$params['action'] = $action;

		return $this->dispatchRequest($type, $params);
	}

	/**
	 * Get the API Key
	 *
	 * @return string The API Key
	 */
	public function getAPIKey() {
		$configuration = $this->getConfiguration();

	    return empty($configuration['api_key']) ? null : $configuration['api_key'];
	}

	/**
	 * Get the API Hook Secret
	 *
	 * @return string The API Hook Secret
	 */
	public function getAPIHookSecret() {
		$configuration = $this->getConfiguration();

	    return empty($configuration['api_hook_secret']) ? null : $configuration['api_hook_secret'];
	}

	/**
	 * Get the System Username
	 *
	 * @return string The System Username
	 */
	public function getSystemUsername() {
		$configuration = $this->getConfiguration();

	    return empty($configuration['system_username']) ? null : $configuration['system_username'];
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
