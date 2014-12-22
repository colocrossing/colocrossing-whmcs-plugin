<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Router for WHMCS Client Module.
 * Handles Dispatching Requests to Correct Controllers.
 */
class ColoCrossing_Clients_Router extends ColoCrossing_Router {

	/**
	 * The Route To Send Requests to if no route is specified.
	 * @var array
	 */
	protected static $DEFAULT_ROUTE = array(
		'controller' => 'devices',
		'action' => 'index'
	);

	/**
	 * The List of All Available Controllers and their actions
	 * @var array
	 */
	protected static $ROUTES = array(
		'error' => array('index'),
	);

	/**
	 * Retrieves the Default Route if none provided
	 *
	 * @return  array<string, string> The Route
	 */
	public function getDefaultRoute() {
		return self::$DEFAULT_ROUTE;
	}

	/**
	 * Retrieves the Available Routes to this
	 *
	 * @return  array<string,array<string>> The Routes
	 */
	public function getRoutes() {
		return self::$ROUTES;
	}

	/**
	 * Creates Controller
	 *
	 * @param  string $type
	 * @return ColoCrossing_Controller The Controller
	 */
    public function create($type) {
        switch ($type) {
        	case 'error':
        		require_once('controllers/ErrorController.php');
        		return new ColoCrossing_Clients_ErrorController();
        }

        throw new Exception('Unknown controller type specified.');
    }

}
