<?php
/**
 * ColoCrossing Portal
 *
 * This is a provisioning module that delegates actions to the ColoCrossing WHMCS Addon
 */

if (!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

require implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'addons', 'colocrossing', 'Module.php'));

function colocrossing_AdminServicesTabFields($params) {
	$module = ColoCrossing_Module::getInstance();

    return $module->dispatchRequestTo('admin', 'services', 'edit', array(
    	'id' => $params['serviceid']
    ));
}

function colocrossing_AdminServicesTabFieldsSave($params) {
	$module = ColoCrossing_Module::getInstance();

    return $module->dispatchRequestTo('admin', 'services', 'assign-device', array(
    	'id' => $params['serviceid'],
    	'device_id' => $_POST['colocrossing_device_id']
    ));
}

function colocrossing_SuspendAccount($params) {
	$module = ColoCrossing_Module::getInstance();

    return $module->dispatchRequestTo('admin', 'services', 'suspend', array(
        'id' => $params['serviceid']
    ));
}

function colocrossing_UnsuspendAccount($params) {
	$module = ColoCrossing_Module::getInstance();

    return $module->dispatchRequestTo('admin', 'services', 'unsuspend', array(
        'id' => $params['serviceid']
    ));
}

function colocrossing_TerminateAccount($params) {
	$module = ColoCrossing_Module::getInstance();

    return $module->dispatchRequestTo('admin', 'services', 'terminate', array(
        'id' => $params['serviceid']
    ));
}

function colocrossing_UnassignDevice($params) {
	$module = ColoCrossing_Module::getInstance();

    return $module->dispatchRequestTo('admin', 'services', 'unassign-device', array(
        'id' => $params['serviceid']
    ));
}

function colocrossing_AdminCustomButtonArray() {
    return array(
        'Unassign Device' => 'UnassignDevice'
    );
}