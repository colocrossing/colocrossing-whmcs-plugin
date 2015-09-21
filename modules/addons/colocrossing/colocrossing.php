<?php
/**
 * ColoCrossing Portal
 *
 * This is an addon module that can be used to interact with the ColoCrossing Portal.
 */

if (!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

require 'Configuration.php';
require 'Module.php';

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

    $module->dispatchRequest('admin');
}

function colocrossing_sidebar($params) {
    $module = ColoCrossing_Module::getInstance();

    $base_url = $module->getBaseAdminUrl();

    $devices_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'devices',
        'action' => 'index'
    ));
    $bandwidth_usages_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'bandwidth-usages',
        'action' => 'index'
    ));
    $assigned_services_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'services',
        'action' => 'assigned'
    ));
    $unassigned_services_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'services',
        'action' => 'unassigned'
    ));
    $subnets_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'subnets',
        'action' => 'index'
    ));
    $null_routes_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'null-routes',
        'action' => 'index'
    ));
    $announcements_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'announcements',
        'action' => 'index'
    ));
    $events_url = ColoCrossing_Utilities::buildUrl($base_url, array(
        'controller' => 'events',
        'action' => 'index'
    ));

    $sidebar  = '<span class="header">ColoCrossing Portal</span>';
    $sidebar .= '<ul class="menu">';
    $sidebar .=     '<li><a href="' . $devices_url . '">Devices</a></li>';
    $sidebar .=     '<li><a href="' . $bandwidth_usages_url . '">Bandwidth Usages</a></li>';
    $sidebar .=     '<li><a href="' . $assigned_services_url . '">Assigned Services</a></li>';
    $sidebar .=     '<li><a href="' . $unassigned_services_url . '">Unassigned Services</a></li>';
    $sidebar .=     '<li><a href="' . $subnets_url . '">Subnets</a></li>';
    $sidebar .=     '<li><a href="' . $null_routes_url . '">Null Routes</a></li>';
    $sidebar .=     '<li><a href="' . $announcements_url . '">Announcements</a></li>';
    $sidebar .=     '<li><a href="' . $events_url . '">Events</a></li>';
    $sidebar .= '</ul>';

    return $sidebar;

}

function colocrossing_clientarea($params) {
    $module = ColoCrossing_Module::getInstance();

    list($title, $output) = $module->dispatchRequest('client');

    return array(
        'pagetitle' => $title,
        'breadcrumb' => array($_SERVER['REQUEST_URI'] => $title),
        'templatefile' => 'clients/template',
        'requirelogin' => true,
        'vars' => array(
            'output' => $output
        ),
    );
}
