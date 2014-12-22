<?php
/**
 * ColoCrossing Portal
 *
 * This is an addon module that can be used to interact with the ColoCrossing Portal.
 */

if (!defined('WHMCS')){
    die('This file cannot be accessed directly');
}

require implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), '..', '..', 'addons', 'colocrossing', 'Module.php'));

function colocrossing_AdminServicesTabFields($params) {
	$service_id = intval($params['serviceid']);
	$service = ColoCrossing_Model_Service::find($service_id);

	try {
		$device = empty($service) ? null : $service->getDevice();
	} catch (ColoCrossing_Error $e) {
		$device = null;
	}

	$module = ColoCrossing_Module::getInstance();
	$admin_module_url = $module->getBaseAdminUrl();

	//Device Not Found for Service, Render Device Select
	if(empty($device)) {
		$template = ColoCrossing_Utilities::parseTemplate(dirname(__FILE__) . '/templates/services/device_select.phtml', array(
    		'admin_module_url' => $admin_module_url
		));

	    return array(
	    	'ColoCrossing Device' => $template
	    );
	}

	$device_url = ColoCrossing_Utilities::buildUrl($admin_module_url, array(
		'controller' => 'devices',
		'action' => 'view',
		'id' => $device->getId()
	));

	$template = ColoCrossing_Utilities::parseTemplate(dirname(__FILE__) . '/templates/services/device_display.phtml', array(
		'device' => $device,
		'device_url' => $device_url
	));

	return array(
        'ColoCrossing Device' => $template
    );
}

function colocrossing_AdminServicesTabFieldsSave($params) {
	$service_id = intval($params['serviceid']);
	$service = ColoCrossing_Model_Service::find($service_id);

	$device_id = intval($_POST['colocrossing_device_id']);

	if($device_id > 0) {
		$service->assignToDevice($device_id);
	} else {
		$service->unassignFromDevice();
	}
}
