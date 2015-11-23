<?php

if(!defined('WHMCS')) {
	die('This file cannot be accessed directly');
}

require 'admins/Router.php';
require 'clients/Router.php';

/**
 * ColoCrossing Router for WHMCS Module.
 * Handles Dispatching Requests to Correct Controllers.
 */
abstract class ColoCrossing_Router {

	/**
	 * Dispatch Request to Controller Action Specified
	 *
	 * @param  string $controller
	 * @param  string $action
	 * @return mixed The Result of the Action
	 */
	public function dispatch(array $params) {
		$route = $this->getRouteFromParams($params);
		try {
			return $this->dispatchRoute($route, $params);
		} catch (ColoCrossing_Error_Authorization $e) {
			return $this->dispatchRoute(array(
				'controller' => 'error',
				'action' => 'authentication'
			), array(
				'params' => $params,
				'message' => $e->getMessage()
			));
		} catch (ColoCrossing_Error_NotFound $e) {
			return $this->dispatchRoute(array(
				'controller' => 'error',
				'action' => 'missing'
			), array(
				'params' => $params,
				'message' => $e->getMessage()
			));
		} catch (Exception $e) {
			return $this->dispatchRoute(array(
				'controller' => 'error',
				'action' => 'generic'
			), array(
				'params' => $params,
				'message' => $e->getMessage()
			));
		}
	}

	/**
	 * Dispatch The Route to the Controller
	 *
	 * @param  array  $route
	 * @return mixed The Result of the Action
	 */
	public function dispatchRoute(array $route, array $params = array()) {
		$controller = $this->create($route['controller']);
		return $controller->dispatch($route['action'], $params);
	}

	/**
	 * Retrieves the Default Route if none provided
	 *
	 * @return  array<string, string> The Route
	 */
	public abstract function getDefaultRoute();

	/**
	 * Retrieves the Available Routes to this
	 *
	 * @return  array<string,array<string>> The Routes
	 */
	public abstract function getRoutes();

	/**
	 * Creates Controller
	 *
	 * @param  string $type
	 * @return ColoCrossing_Controller The Controller
	 */
	public abstract function create($type);

	/**
	 * Determines if the Route is available
	 *
	 * @param  string  $controller
	 * @param  string  $action
	 * @return boolean True if route is present
	 */
	private function isRouteAvailable($controller, $action) {
		$routes = $this->getRoutes();

		return isset($routes[$controller]) && is_array($routes[$controller]) && in_array($action, $routes[$controller]);
	}

	/**
	 * Retrieves the Route From the Params Specified. Returns the Default
	 * Route if none Specified. Returns the Error Route if invalid route
	 * is specifed
	 *
	 * @param  array  $params The Request Params
	 * @return array The Route
	 */
	private function getRouteFromParams(array $params = array()) {
		if(empty($params['controller']) || empty($params['action'])) {
			return $this->getDefaultRoute();
		}

		if(!$this->isRouteAvailable($params['controller'], $params['action'])) {
			return array(
				'controller' => 'error',
				'action' => 'missing'
			);
		}

		return array(
			'controller' => strtolower($params['controller']),
			'action' => strtolower($params['action'])
		);
	}

}
