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
    $module_url = $module->getBaseAdminUrl();

    $sidebar  = '<span class="header">ColoCrossing Portal</span>';
    $sidebar .= '<ul class="menu">';
    $sidebar .=     '<li><a href="' . $module_url . '&controller=devices&action=index">Devices</a></li>';
    $sidebar .=     '<li><a href="' . $module_url . '&controller=bandwidth-usages&action=index">Bandwidth Usages</a></li>';
    $sidebar .=     '<li><a href="' . $module_url . '&controller=services&action=index">Unassigned Services</a></li>';
    $sidebar .=     '<li><a href="' . $module_url . '&controller=subnets&action=index">Subnets</a></li>';
    $sidebar .=     '<li><a href="' . $module_url . '&controller=null-routes&action=index">Null Routes</a></li>';
    $sidebar .=     '<li><a href="' . $module_url . '&controller=events&action=index">Events</a></li>';
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
