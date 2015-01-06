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
		$devices_services = ColoCrossing_Model_Service::getAssignedDevices();

		$this->devices_by_hostname = array();

		foreach ($devices as $index => $device) {
			$device_id = $device->getId();
			$hostname = $device->getHostname();

			//Ignore Devices Without Hostname or that are already assigned
			if(empty($hostname) || isset($devices_services[$device_id])) {
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
			$path = $this->getViewDirectoryPath() . '/services/device_select.phtml';
			$template = ColoCrossing_Utilities::parseTemplate($path, array(
	    		'base_url' => $this->base_url
			));

			return array(
			    'ColoCrossing Device' => $template
			);
		}

		$fields = array();

		$device_id = $device->getId();
		$device_url = ColoCrossing_Utilities::buildUrl($this->base_url, array(
			'controller' => 'devices',
			'action' => 'view',
			'id' => $device_id
		));

		$path = $this->getViewDirectoryPath() . '/services/device_display.phtml';
		$fields['ColoCrossing Device'] = ColoCrossing_Utilities::parseTemplate($path, array(
			'device' => $device,
			'device_url' => $device_url
		));

		$bandwidth_graphs = $this->getDeviceBandwidthGraphs($device);
		$bandwidth_graph_url = ColoCrossing_Utilities::buildUrl($this->base_url, array(
			'controller' => 'services',
			'action' => 'bandwidth-graph',
			'id' => $service_id
		));
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

		if(count($bandwidth_graphs)) {
			$path = $this->getViewDirectoryPath() . '/services/device_bandwidth_graphs.phtml';
			$fields['Bandwidth Usage'] = ColoCrossing_Utilities::parseTemplate($path, array(
				'bandwidth_graphs' => $bandwidth_graphs,
				'bandwidth_graph_url' => $bandwidth_graph_url,
				'bandwidth_graph_durations' => $bandwidth_graph_durations
			));
		}

		return $fields;
	}

	public function bandwidthGraph(array $params) {
		$this->disableRendering();

		$device = $this->api->devices->find($params['id']);
		$service = ColoCrossing_Model_Service::find($params['id']);
		$device = isset($service) ? $service->getDevice() : null;

		if(empty($device)) {
			$this->setResponseCode(404);
			return null;
		}

		switch ($params['range']) {
			case 'current':
				$end = $service->getNextDueDate();
				$length = $service->getBillingCycleLength();
				$start = strtotime('-' . $length, $end);
				break;
			case 'previous':
				$length = $service->getBillingCycleLength();
				$end = strtotime('-' . $length, $service->getNextDueDate());
				$start = strtotime('-' . $length, $end);
				break;
			default:
				$start = strtotime('-' . $params['range']);
				$end = time();
				break;
		}

		$graph = $this->api->devices->switches->getBandwidthGraph($params['switch_id'], $params['port_id'], $device, $start, $end);

		if (empty($graph)) {
			$this->setResponseCode(404);
			return null;
		}

		$this->renderImage($graph);
	}

	private function getDeviceBandwidthGraphs($device) {
		$device_id = $device->getId();
		$device_type = $device->getType();

		if(!$device_type->isNetworkEndpoint()) {
			return array();
		}

		$switches = $device->getSwitches();
		$bandwidth_graphs = array();

		foreach ($switches as $i => $switch) {
			$switch_id = $switch->getId();
			$switch_ports = $switch->getPorts();

			foreach ($switch_ports as $j => $port) {
				if($port->isBandwidthGraphAvailable()) {
					$bandwidth_graphs[] = array(
						'device_id' => $device_id,
						'switch_id' => $switch_id,
						'port_id' => $port->getId()
					);
				}
			}
		}

		return $bandwidth_graphs;
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
