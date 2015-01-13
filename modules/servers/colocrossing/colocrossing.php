<?php
/**
 * ColoCrossing Portal
 *
 * This is a provisioning module that delegates actions to the ColoCrossing WHMCS Addon
 */

if (!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

define('ADDON_DIRECTORY', implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'addons', 'colocrossing', '')));

require ADDON_DIRECTORY . 'Configuration.php';
require ADDON_DIRECTORY . 'Module.php';

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

	if(strlen($params['suspendreason']) > 20) {
		return 'The reason for suspension must be 20 characters or less.';
	}

    return $module->dispatchRequestTo('admin', 'services', 'suspend', array(
        'id' => $params['serviceid'],
        'comment' => $params['suspendreason']
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

function colocrossing_ViewDevice($params) {
    $module = ColoCrossing_Module::getInstance();

    list($result, $output) = $module->dispatchRequestTo('client', 'services', 'view-device', array(
        'id' => $params['serviceid']
    ));

    return $result;
}

function colocrossing_ClientAreaCustomButtonArray() {
    return array(
        'View Device' => 'ViewDevice'
    );
}

function colocrossing_ClientArea($params) {
    $module = ColoCrossing_Module::getInstance();
    $service = ColoCrossing_Model_Service::find($params['serviceid']);

    if(empty($service)) {
        return null;
    }

    try {
        $device = $service->getDevice();
    } catch(ColoCrossing_Error $e) {
        $device = null;
    }

    if(empty($device)) {
        return null;
    }

    $device_id = $device->getId();
    $device_name = $device->getName();
    $device_url = ColoCrossing_Utilities::buildUrl($module->getBaseClientUrl(), array(
        'controller' => 'devices',
        'action' => 'view',
        'id' => $device_id
    ));

    $type = $device->getType();

    if($type->isNetworkEndpoint()) {
        $switches = $device->getSwitches();

        $bandwidth_graphs = array();

        foreach ($switches as $i => $switch) {
            foreach ($switch->getPorts() as $j => $port) {
                if($port->isBandwidthGraphAvailable()) {
                    $bandwidth_graphs[] = array(
                        'device_id' => $device_id,
                        'switch_id' => $switch->getId(),
                        'port_id' => $port->getId()
                    );
                }
            }
        }

        if(count($bandwidth_graphs)) {
            $bandwidth_graph_durations = array(
                "current" => 'Current Billing Period',
                "previous" => 'Previous Billing Period',
                "12 hours" => "Last 12 Hours",
                "1 day" => "Last Day",
                "1 week" => "Last Week",
                "2 weeks" => "Last 2 Weeks",
                "1 month" => "Last Month",
                "3 months" => "Last 3 Months"
            );

            $start_date = $service->getRegistrationDate();
            $due_date = $service->getNextDueDate();
            $length = $service->getBillingCycleLength();

            if($start_date > strtotime('-' . $length, $due_date)) {
                unset($bandwidth_graph_durations["previous"]);
            }
        }
    }

    if($type->isPowerEndpoint()) {
        $power_distribution_units = $device->getPowerDistributionUnits();
    }



    return array(
        'templatefile' => 'client-area',
        'vars' => array(
            'device_url' => $device_url,
            'device_name' => $device_name
        )
    );
}
