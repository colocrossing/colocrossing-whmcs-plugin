<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Services Controller for WHMCS Admin Module.
 * Handles Responding to all Requests from the WHMCS Service Provisioning Module
 */
class ColoCrossing_Admins_ServicesController extends ColoCrossing_Admins_Controller {

	/**
	 * Override Default Constructor to Disable Rendering for this Controller By Default
	 */
	public function __construct() {
        parent::__construct();

        $this->disableRendering();
    }

	public function edit(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		try {
			$device = empty($service) ? null : $service->getDevice();
		} catch (ColoCrossing_Error $e) {
			$device = null;
		}

		//Device Not Found for Service, Render Device Select
		if(empty($device)) {
			$path = '/services/device_select.phtml';
			$data = array(
	    		'base_url' => $this->base_url
			);
		} else {
			$device_url = ColoCrossing_Utilities::buildUrl($this->base_url, array(
				'controller' => 'devices',
				'action' => 'view',
				'id' => $device->getId()
			));

			$path = '/services/device_display.phtml';
			$data = array(
				'device' => $device,
				'device_url' => $device_url
			);
		}

		return array(
		    'ColoCrossing Device' => ColoCrossing_Utilities::parseTemplate($this->getViewDirectoryPath() . $path, $data)
		);
	}

	public function assignDevice(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		if(empty($service)) {
			return 'Service not found!';
		}

		$device_id = intval($params['device_id']);
		$device = $device_id > 0 ? $this->api->devices->find($device_id) : null;

		if(empty($device)) {
			return 'Device not found!';
		}

		$service->assignToDevice($device_id);
	}

	public function unassignDevice(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		$service->unassignFromDevice();
	}

	public function suspend(array $params) {
		//Turn Network Ports Off, Log Event
	}

	public function unsuspend(array $params) {
		//Turn Network Ports On, Log Event
	}

	public function terminate(array $params) {
		//Unassign Device from Service, Submit Cancellation Request, Log Event
	}

}
