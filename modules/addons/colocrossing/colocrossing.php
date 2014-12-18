<?php
/**
 * ColoCrossing Portal
 *
 * This is an addon module that can be used to interact with the ColoCrossing Portal.
 */

if (!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

require 'Module.php';
require 'Router.php';
require 'Controller.php';
require 'Model.php';
require 'models/Event.php';
require 'models/User.php';
require 'models/Admin.php';
require 'models/Client.php';
require 'api/ColoCrossing.php';

function colocrossing_config() {
    $module = ColoCrossing_Module::getInstance();

    return $module->getDefaultConfiguration();
}

function colocrossing_activate() {
    $module = ColoCrossing_Module::getInstance();

    return $module->activate();
}

function colocrossing_deactivate() {
    $module = ColoCrossing_Module::getInstance();

    return $module->deactivate();
}

function colocrossing_output($params) {
    $module = ColoCrossing_Module::getInstance();

    $module->dispatchRequest($params);
}

function colocrossing_sidebar($params) {
    $module_link = $params['modulelink'];

    $sidebar  = '<span class="header">ColoCrossing Portal</span>';
    $sidebar .= '<ul class="menu">';
    $sidebar .=     '<li><a href="' . $module_link . '&controller=devices&action=index">Devices</a></li>';
    $sidebar .=     '<li><a href="' . $module_link . '&controller=subnets&action=index">Subnets</a></li>';
    $sidebar .=     '<li><a href="' . $module_link . '&controller=null-routes&action=index">Null Routes</a></li>';
    $sidebar .=     '<li><a href="' . $module_link . '&controller=events&action=index">Events</a></li>';
    $sidebar .= '</ul>';

    return $sidebar;

}