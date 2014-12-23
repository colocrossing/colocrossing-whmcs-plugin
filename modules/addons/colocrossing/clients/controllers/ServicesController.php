<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Services Controller for WHMCS Client Module.
 * Handles Responding to all Requests Related to Services
 */
class ColoCrossing_Clients_ServicesController extends ColoCrossing_Clients_Controller {

	public function viewDevice(array $params) {
		$this->disableRendering();

		$service = ColoCrossing_Model_Service::find($params['id']);

		if(empty($service)) {
			return 'Service not found!';
		}

		$this->redirectTo('devices', 'view', array(
			'id' => $service->getDeviceId()
		));
	}

}
