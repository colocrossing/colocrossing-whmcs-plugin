<?php

/**
 * ColoCrossing Router for WHMCS Module.
 * Handles Dispatching Requests to Correct Controllers.
 */
class ColoCrossing_Router {

	/**
	 * Routes recognized by this Router
	 * @var array
	 */
	private $routes;

	/**
	 * The Default Route to Dispatch to
	 * @var array
	 */
	private $default_route;

	/**
	 * Constructs Router By Specifying Routes and Setting The Default Route
	 * @param array $routes
	 * @param array $default_route
	 */
	public function __construct(array $routes, array $default_route) {
    	$this->routes = array();


    	$this->addRoutes($routes);
    	$this->setDefaultRoute($default_route['controller'], $default_route['action']);
    }

	/**
	 * Set the Default Route
	 *
	 * @param string $controller
	 * @param string $action
	 */
	public function setDefaultRoute($controller, $action) {
		if(!$this->isRouteAvailable($controller, $action)) {
			throw new Exception('Specifed route is not found.');
		}

		$this->default_route = array(
			'controller' => $controller,
			'action' => $action
		);
	}

	/**
	 * Dispatch Request to Controller Action Specified
	 *
	 * @param  string $controller
	 * @param  string $action
	 */
	public function dispatch(array $params) {
		$route = $this->getRouteFromParams($params);

		try {
			$this->dispatchRoute($route, $params);
		} catch (ColoCrossing_Error_Authorization $e) {
			$this->dispatchRoute(array(
				'controller' => 'error',
    			'action' => 'authentication'
			), $params);
		} catch (ColoCrossing_Error_NotFound $e) {
			$this->dispatchRoute(array(
				'controller' => 'error',
    			'action' => 'missing'
			), $params);
		} catch (Exception $e) {
			$this->dispatchRoute(array(
				'controller' => 'error',
    			'action' => 'generic'
			), $params);
		}
	}

	/**
	 * Add Array of Routes to be Handled By Router
	 *
	 * @param array $routes
	 */
	public function addRoutes(array $routes) {
		foreach ($routes as $controller => $actions) {
			foreach ($actions as $index => $action) {
				$this->addRoute($controller, $action);
			}
		}
	}

	/**
	 * Add Single Route to be handled by this Router
	 *
	 * @param string $controller
	 * @param string $action
	 */
	public function addRoute($controller, $action) {
		if(empty($this->routes[$controller])) {
			$this->routes[$controller] = array();
		}

		$this->routes[$controller][$action] = true;
	}

    /**
     * Returns the singleton instance of this class.
     *
     * @return ColoCrossing_Router The Router instance.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            $instance = new ColoCrossing_Router();
        }

        return $instance;
    }

    /**
     * Dispatch The Route to the Controller
     *
     * @param  array  $route
     */
	public function dispatchRoute(array $route, array $params = array()) {
		$controller = self::create($route['controller']);
		$controller->dispatch($route['action'], $params);
	}
	/**
	 * Creates Controller
	 *
	 * @param  string $type
	 * @return ColoCrossing_Controller The Controller
	 */
    public static function create($type) {
        switch ($type) {
        	case 'devices':
        		require_once('controllers/DevicesController.php');
        		return new ColoCrossing_DevicesController();
            case 'subnets':
                require_once('controllers/SubnetsController.php');
                return new ColoCrossing_SubnetsController();
            case 'null-routes':
                require_once('controllers/NullRoutesController.php');
                return new ColoCrossing_NullRoutesController();
        	case 'events':
        		require_once('controllers/EventsController.php');
        		return new ColoCrossing_EventsController();
        	case 'error':
        		require_once('controllers/ErrorController.php');
        		return new ColoCrossing_ErrorController();
        }

        throw new Exception('Unknown controller type specified.');
    }


    /**
     * Determines if the Route is available
     *
     * @param  string  $controller
     * @param  string  $action
     * @return boolean True if route is present
     */
    private function isRouteAvailable($controller, $action) {
    	return isset($this->routes[$controller]) && isset($this->routes[$controller][$action]) && $this->routes[$controller][$action];
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
    		return $this->default_route;
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
