<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Controller for the Client WHMCS Module.
 */
class ColoCrossing_Clients_Controller extends ColoCrossing_Controller {

    /**
     * The Current User
     * @var ColoCrossing_Model_User
     */
    protected $current_user;

    /**
     * Initialize References to Module and API Client
     */
    public function __construct() {
        parent::__construct();

        $this->current_user = ColoCrossing_Model_User::getCurrentUser('client');
    }

    /**
     * Logs Message to Events
     * @param  string $description
     */
    protected function log($description = ''){
        ColoCrossing_Model_Client::log($description);
    }

    /**
     * Dispatches Request to Action and Renders The Actions View
     * Throws Exaception if Action is not Found.
     *
     * @param  string $action
     * @param  array  $params
     * @return array<string> 0 => The Return Value, 1 => The Rendered Ouput
     */
    public function dispatch($action, array $params) {
        ob_start();

        $result = parent::dispatch($action, $params);
        $output = ob_get_clean();

        return array($result, $output);
    }

    /**
     * @return string The Base Url
     */
    protected function getBaseUrl() {
        return $this->module->getBaseClientUrl();
    }

    /**
     * Retrieves the View Directory Path
     *
     * @return string The Path
     */
    protected function getViewDirectoryPath() {
        return implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), 'views'));
    }

}
