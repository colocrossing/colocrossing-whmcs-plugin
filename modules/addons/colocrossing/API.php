<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require 'api/ColoCrossing.php';

/**
 * Creates a Singleton Implmentation of the ColoCrossing API Client.
 * Uses the API Key from
 */
class ColoCrossing_API extends ColoCrossing_Client {

	/**
	 * The Singleton API Instance
	 * @var ColoCrossing_API
	 */
	private static $instance;

    /**
     * The Module
     * @var ColoCrossing_Module
     */
    protected $module;

    /**
     * Constructs the instance of the API Client with the API Key from the Module.
     * Call getInstance instead of this.
     */
	public function __construct($api_key = null, array $options = array()) {
		$this->module = ColoCrossing_Module::getInstance();

		$api_key = isset($api_key) ? $api_key : $this->module->getAPIKey();

		parent::__construct($api_key, array_merge(array(
			'application_name' => 'ColoCrossing WHMCS Module'
		), $options));
	}

	/**
	 * Returns the Singleton Instance of this class.
	 *
	 * @return ColoCrossing_API The Instance
	 */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ColoCrossing_API();
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
