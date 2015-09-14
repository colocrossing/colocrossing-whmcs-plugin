<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Router for WHMCS Admin Module.
 * Handles Dispatching Requests to Correct Controllers.
 */
class ColoCrossing_Admins_Router extends ColoCrossing_Router {

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
        'bandwidth-usages' => array('index'),
        'devices' => array('index', 'view', 'update', 'unassigned-devices-index', 'bandwidth-graph', 'update-power-ports', 'update-network-ports'),
        'services' => array('unassigned', 'overdue', 'edit', 'bandwidth-graph', 'assign-devices', 'assign-device', 'unassign-device', 'suspend', 'unsuspend', 'terminate'),
        'subnets' => array('index', 'view', 'update'),
        'null-routes' => array('index', 'create', 'destroy'),
        'announcements' => array('index', 'view', 'send'),
        'events' => array('index'),
        'error' => array('general', 'missing', 'unauthorized')
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
            case 'bandwidth-usages':
                require_once('controllers/BandwidthUsagesController.php');
                return new ColoCrossing_Admins_BandwidthUsagesController();
            case 'devices':
                require_once('controllers/DevicesController.php');
                return new ColoCrossing_Admins_DevicesController();
            case 'services':
                require_once('controllers/ServicesController.php');
                return new ColoCrossing_Admins_ServicesController();
            case 'subnets':
                require_once('controllers/SubnetsController.php');
                return new ColoCrossing_Admins_SubnetsController();
            case 'null-routes':
                require_once('controllers/NullRoutesController.php');
                return new ColoCrossing_Admins_NullRoutesController();
            case 'announcements':
                require_once('controllers/AnnouncementsController.php');
                return new ColoCrossing_Admins_AnnouncementsController();
            case 'events':
                require_once('controllers/EventsController.php');
                return new ColoCrossing_Admins_EventsController();
            case 'error':
                require_once('controllers/ErrorController.php');
                return new ColoCrossing_Admins_ErrorController();
        }

        throw new Exception('Unknown controller type specified.');
    }

}
