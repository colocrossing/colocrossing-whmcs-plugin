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

    public function index(array $params) {
        $this->enableRendering();

		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;
		$this->page_size = isset($params['page_size']) && is_numeric($params['page_size']) ? intval($params['page_size']) : 50;

		$this->services = ColoCrossing_Model_Service::findAllUnassigned(array(
			'pagination' => array(
				'number' => $this->page,
				'size' =>$this->page_size
			)
		));

		$this->total_record_count = ColoCrossing_Model_Service::getTotalUnassigned();
		$this->page_count = ceil($this->total_record_count / $this->page_size);

		//Group Devices by Hostname
		$devices = $this->api->devices->findAll(array(
			'filters' => array('compact' => true)
		));

		$this->devices_by_hostname = array();

		foreach ($devices as $index => $device) {
			$hostname = $device->getHostname();

			//Ignore Devices Without Hostname
			if(empty($hostname)) {
				continue;
			}

			if(empty($devices_by_hostname[$hostname])) {
				$devices_by_hostname[$hostname] = array();
			}

			$this->devices_by_hostname[$hostname][] = $device;
		}
	}

	public function assignDevices(array $params) {
		$success = true;

		foreach ($params['services'] as $index => $service_id) {
			$service = ColoCrossing_Model_Service::find($service_id);

			$client = isset($service) ? $service->getClient() : null;

			$device_id = intval($params['devices'][$service_id]);

			try {
				$device = $device_id > 0 ? $this->api->devices->find($device_id) : null;
			} catch (ColoCrossing_Error $e) {
				$device = null;
			}

			if(empty($service) || empty($device) || empty($client)) {
				$success = false;
				continue;
			}

			$service->assignToDevice($device_id);

			ColoCrossing_Model_Event::log($device->getName() . ' assigned to ' . $client->getFullName() . ' for service #' . $service_id . '.');
		}

		if($success) {
			$this->setFlashMessage('The devices have successfully been assigned to the services.', 'success');
		} else {
			$this->setFlashMessage('An error occurred while assigning devices to services.', 'error');
		}

		$this->redirectTo('services', 'index');
	}

	public function edit(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		try {
			$device = empty($service) ? null : $service->getDevice();
		} catch (ColoCrossing_Error $e) {
			$path = $this->getViewDirectoryPath() . '/services/api_unavailable.phtml';

			return array(
			    'ColoCrossing Device' => ColoCrossing_Utilities::parseTemplate($path)
			);
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

		$client = $service->getClient();

		if(empty($client)) {
			return 'Client not found!';
		}

		$device_id = intval($params['device_id']);

		try {
			$device = $device_id > 0 ? $this->api->devices->find($device_id) : null;
		} catch (ColoCrossing_Error $e) {
			return 'Unable to access the API at this time. Please try again later.';
		}

		if(empty($device)) {
			return 'Device not found!';
		}

		$service->assignToDevice($device_id);

		ColoCrossing_Model_Event::log($device->getName() . ' assigned to ' . $client->getFullName() . ' for service #' . $service_id . '.');
		return 'success';
	}

	public function unassignDevice(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		if(empty($service)) {
			return 'Service not found!';
		}

		$client = $service->getClient();

		if(empty($client)) {
			return 'Client not found!';
		}

		try {
			$device = $service->getDevice();
		} catch (ColoCrossing_Error $e) {
			$device = null;
		}

		$device_name = isset($device) ? $device->getName() : 'Device';

		$service->unassignFromDevice();

		ColoCrossing_Model_Event::log($device_name . ' unassigned from ' . $client->getFullName() . ' for service #' . $service_id . '.');
		return 'success';
	}

	public function suspend(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		if(empty($service)) {
			return 'Service not found!';
		}

		$client = $service->getClient();

		if(empty($client)) {
			return 'Client not found!';
		}

		try {
			$device = $service->getDevice();
		} catch (ColoCrossing_Error $e) {
			return 'Unable to access the API at this time. Please try again later.';
		}

		if(empty($device)) {
			return 'Device not found!';
		}

		$comment = empty($params['comment']) ? 'Service Suspended' : $params['comment'];

		if(!$this->controlDeviceNetworkPorts($device, 'off', $comment)) {
			$message = 'Failed to suspend service #' . $service_id . ' for ' . $client->getFullName() . ' on ' . $device->getName() . '.';

			ColoCrossing_Model_Event::log($message);
			return $message;
		}

		ColoCrossing_Model_Event::log('Service #' . $service_id . ' for ' . $client->getFullName() . ' on ' . $device->getName() . ' was suspended.');
		return 'success';
	}

	public function unsuspend(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		if(empty($service)) {
			return 'Service not found!';
		}

		$client = $service->getClient();

		if(empty($client)) {
			return 'Client not found!';
		}

		try {
			$device = $service->getDevice();
		} catch (ColoCrossing_Error $e) {
			return 'Unable to access the API at this time. Please try again later.';
		}

		if(empty($device)) {
			return 'Device not found!';
		}

		if(!$this->controlDeviceNetworkPorts($device, 'on')) {
			$message = 'Failed to unsuspend service #' . $service_id . ' for ' . $client->getFullName() . ' on ' . $device->getName() . '.';

			ColoCrossing_Model_Event::log($message);
			return $message;
		}

		ColoCrossing_Model_Event::log('Service #' . $service_id . ' for ' . $client->getFullName() . ' on ' . $device->getName() . ' was unsuspended.');
		return 'success';
	}

	public function terminate(array $params) {
		$service_id = intval($params['id']);
		$service = ColoCrossing_Model_Service::find($service_id);

		if(empty($service)) {
			return 'Service not found!';
		}

		$client = $service->getClient();

		if(empty($client)) {
			return 'Client not found!';
		}

		try {
			$device = $service->getDevice();
		} catch (ColoCrossing_Error $e) {
			return 'Unable to access the API at this time. Please try again later.';
		}

		if(empty($device)) {
			return 'Device not found!';
		}

		$service->unassignFromDevice();
		ColoCrossing_Model_Event::log($device->getName() . ' unassigned from ' . $client->getFullName() . ' for service #' . $service_id . '.');

		if($this->api->hasPermission('device_cancellation')) {
			$success = $device->cancelService();
		} else {
			$success = $this->controlDeviceNetworkPorts($device, 'off');
		}

		if(!$success) {
			$message = 'Failed to terminate service #' . $service_id . ' for ' . $client->getFullName() . ' on ' . $device->getName() . '.';

			ColoCrossing_Model_Event::log($message);
			return $message;
		}

		ColoCrossing_Model_Event::log('Service #' . $service_id . ' for ' . $client->getFullName() . ' on ' . $device->getName() . ' was terminated.');
		return 'success';
	}

	/**
	 * Sets the status of all the ports of a network endpoint device to the status specified
	 * @param  ColoCrossing_Device 	$device  The Device
	 * @param  string 				$status  The Status, on or off
	 * @param  string|null 			$comment The Reason for controlling
	 * @return boolean		True if Successful
	 */
	private function controlDeviceNetworkPorts($device, $status, $comment = null) {
		$type = $device->getType();

		if(!$type->isNetworkEndpoint()) {
			return true;
		}

		$switches = $device->getSwitches();

		$success = true;
		foreach ($switches as $switch) {
			$ports = $switch->getPorts();

			foreach ($ports as $port) {
				$result = $port->setStatus($status, $comment);
				$success &= $result;
			}
		}
		return $success;
	}

}
