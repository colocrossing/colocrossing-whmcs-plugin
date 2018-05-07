<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Devices Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Devices
 */
class ColoCrossing_Admins_DevicesController extends ColoCrossing_Admins_Controller {

	public function index(array $params) {
		$this->filters = array(
			'query' => isset($params['query']) ? $params['query'] : '',
			'compact' => true
		);

		$this->sort = isset($params['sort']) ? $params['sort'] : 'name';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'desc' ? 'desc' : 'asc';

		$this->page_number = isset($params['page_number']) && is_numeric($params['page_number']) ? max(intval($params['page_number']), 1) : 1;
		$this->page_size = isset($params['page_size']) && is_numeric($params['page_size']) ? max(intval($params['page_size']), 1) : 25;

		$collection = $this->api->devices->findAll(array(
			'filters' => $this->filters,
			'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort
		));

		$this->num_records = $collection->size();
		$this->num_pages = ceil($this->num_records / $this->page_size);

		$start_index = min(($this->page_number - 1) * $this->page_size, $this->num_records);
		$end_index = min($start_index + $this->page_size, $this->num_records);

		$this->devices = array();
		$this->services = array();

		for ($i = $start_index; $i < $end_index; $i++) {
			$device = $collection->get($i);

			$this->devices[] = $device;

			$service = ColoCrossing_Model_Service::findByDevice($device);

			if(isset($service)) {
				$this->services[$device->getId()] = $service;
			}
		}
	}

	public function view(array $params) {
		$this->device = $this->api->devices->find($params['id']);

		if(empty($this->device)) {
			$this->setFlashMessage('The device was not found.', 'error');
			$this->redirectTo('devices', 'index');
		}

		$this->user = $this->api->getUser();

		$this->service = ColoCrossing_Model_Service::findByDevice($this->device->getId());

		if(isset($this->service)) {
			$this->client = $this->service->getClient();
			$this->product = $this->service->getProduct();
		}

		$this->assets = $this->device->getAssets();
		$this->type = $this->device->getType();
		$this->subnets = $this->type->isNetworkEndpoint() || $this->type->isVirtual() ? $this->device->getSubnets() : array();

		if($this->type->isRacked()) {
			$rack_name = $this->device->getRackName();
			$this->rack = isset($rack_name) ? $rack_name . ' (U Slot: ' . $this->device->getUSpace() . ')' : 'Unassigned';
		} else {
			$this->rack = 'Self';
		}

		if($this->type->isNetworkEndpoint()) {
			$this->switches = $this->device->getSwitches();
			$this->bandwidth_graphs = array();

			foreach ($this->switches as $i => $switch) {
				foreach ($switch->getPorts() as $j => $port) {
					if($port->isBandwidthGraphAvailable()) {
						$this->bandwidth_graphs[] = array(
							'device_id' => $this->device->getId(),
							'switch_id' => $switch->getId(),
							'port_id' => $port->getId()
						);
					}
				}
			}
		}

		if($this->type->isPowerEndpoint()) {
			$this->power_distribution_units = $this->device->getPowerDistributionUnits();
		}
	}

	public function update(array $params) {
		$device = $this->api->devices->find($params['id']);

		if(empty($device)) {
			$this->setFlashMessage('The device was not found.', 'error');
			$this->redirectTo('devices', 'index');
		}

		if($device->update($params['nickname'], $params['hostname'])) {
			$service = ColoCrossing_Model_Service::findByDevice($device);

			if(isset($service)) {
				$service->setHostname($params['hostname']);

				$service->update();
			}

			$this->setFlashMessage('The device has successfully been updated.', 'success');
		} else {
			$this->setFlashMessage('An error occurred while updating the device.', 'error');
		}

		$this->redirectTo('devices', 'view', array(
			'id' => $params['id']
		));
	}

	public function unassignedDevicesIndex(array $params) {
		//Get all Services Assign toDevice
		$services = ColoCrossing_Model_Service::findAllAssignedToDevices();

		//Build a Set of Assigned Device ids
		$assigned_device_ids = array();
		foreach ($services as $index => $service) {
			$device_id = $service->getDeviceId();

			if(!empty($device_id)) {
				$assigned_device_ids[$device_id] = true;
			}
		}

		//Get All Devices that Match Query
		$query_devices = $this->api->devices->findAll(array(
			'filters' => array(
				'query' => $params['query'],
				'compact' => true
			)
		));

		//Restrict to First 50 Devices That are not Assigned.
		$devices = array();
		$devices_length = 0;
		foreach ($query_devices as $index => $device) {
			$device_id = $device->getId();

			if(empty($assigned_device_ids[$device_id])) {
				$devices[] = array(
					'id' => $device_id,
					'name' => $device->getName(),
					'nickname' => $device->getNickname(),
					'hostname' => $device->getHostname(),
				);
				$devices_length++;
			}

			if($devices_length >= 50) {
				break;
			}
		}

		$this->renderJSON($devices);
	}

	public function bandwidthGraph(array $params) {
		$start = is_numeric($params['start']) ? intval($params['start']) : strtotime($params['start']);
		$end = is_numeric($params['end']) ? intval($params['end']) : strtotime($params['end']);

		$graph = $this->api->devices->switches->getBandwidthGraph($params['switch_id'], $params['port_id'], $params['id'], $start, $end);

		if (empty($graph))
		{
			$this->setResponseCode(404);
			$this->disableRendering();
			return null;
		}

		$this->renderImage($graph);
	}


	public function updatePowerPorts(array $params) {
		$status = $params['status'];
		$status_description = $this->getPortStatusDescription($status);
		$comment = empty($params['comment']) ? null : $params['comment'];

		$success = true;

		foreach ($params['power_distribution_units'] as $pdu_id => $ports) {
			foreach ($ports as $port_id => $device_id) {
				$device = $this->api->devices->find($device_id);

				if(empty($device) || !$device->getType()->isPowerEndpoint()) {
					$success = false;
					continue;
				}

				$pdu = $device->getPowerDistributionUnit($pdu_id);

				if(empty($pdu)) {
					$success = false;
					continue;
				}

				$port = $pdu->getPort($port_id);

				if(empty($port) || !$port->isControllable()) {
					$success = false;
					continue;
				}

				$result = $this->api->devices->pdus->setPortStatus($pdu, $port, $device, $status, $comment);

				if($result) {
					$description = 'Power port ' . $port_id . ' of ' . $pdu->getName() . ' assigned to ' . $device->getName() . ' was '  . $status_description . '.';
					$this->log($description);
				} else {
					$success = false;
				}
			}
		}

		if($success) {
			$this->setFlashMessage('The power ports have successfully been ' . $status_description . '.', 'success');
		} else {
			$this->setFlashMessage('An error occurred while controlling the power ports.', 'error');
		}

		$this->redirectTo('devices', 'view', array(
			'id' => isset($params['origin_device_id']) ? $params['origin_device_id'] : $params['device_id']
		));
	}

	public function updateNetworkPorts(array $params) {
		$status = $params['status'];
		$status_description = $this->getPortStatusDescription($status);
		$comment = empty($params['comment']) ? null : $params['comment'];

		$success = true;

		foreach ($params['switches'] as $switch_id => $ports) {
			foreach ($ports as $port_id => $device_id) {
				$device = $this->api->devices->find($device_id);

				if(empty($device) || !$device->getType()->isNetworkEndpoint()) {
					$success = false;
					continue;
				}

				$switch = $device->getSwitch($switch_id);

				if(empty($switch)) {
					$success = false;
					continue;
				}

				$port = $switch->getPort($port_id);

				if(empty($port) || !$port->isControllable()) {
					$success = false;
					continue;
				}

				$result = $this->api->devices->switches->setPortStatus($switch, $port, $device, $status, $comment);

				if($result) {
					$description = 'Network port ' . $port_id . ' of ' . $switch->getName() . ' assigned to ' . $device->getName() . ' was '  . $status_description . '.';
					$this->log($description);
				} else {
					$success = false;
				}
			}
		}

		if($success) {
			$this->setFlashMessage('The network ports have successfully been ' . $status_description . '.', 'success');
		} else {
			$this->setFlashMessage('An error occurred while controlling the network ports.', 'error');
		}

		$this->redirectTo('devices', 'view', array(
			'id' => isset($params['origin_device_id']) ? $params['origin_device_id'] : $params['device_id']
		));
	}

	private function getPortStatusDescription($status) {
		switch ($status) {
			case 'on':
				return 'turned on';
			case 'off':
				return 'turned off';
			case 'restart':
				return 'restarted';
		}

		return 'controlled';
	}

	public function updateIpmi(array $params)
	{
		$device = $this->api->devices->find($params['device_id']);
		switch($params['ipmi_action'])
		{
			case 'lift':
				$config = $device->getIpmiConfiguration();

				if(in_array($config->getNullRouteStatus(), array(2, 4)))
				{
					return false;
				}

				$result =  $this->api->devices->ipmi_config->liftNullRoute();

				if($result) {
					$this->setFlashMessage('Null Route was successfully lifted for 4 hours.');
				} else {
					$this->setFlashMessage('An error occurred while attempting to lift the null route.', 'error');
				}
				break;
			case 'replace':
				$result = $device->getIpmiConfiguration()->replaceNullRoute();

				if($result) {
					$this->setFlashMessage('Null Route was successfully replaced.');
				} else {
					$this->setFlashMessage('An error occurred while attempting to replace the null route.', 'error');
				}
				break;
			case 'renew':
				$result = $device->getIpmiConfiguration()->renewNullRouteLift();

				if($result) {
					$this->setFlashMessage('Null Route was successfully lifted for 4 more hours.');
				} else {
					$this->setFlashMessage('An error occurred while attempting to renew the null route lift.', 'error');
				}
				break;
			default:
				$this->setFlashMessage('Unrecognized ipmi action: '.$params['ipmi_action'], 'error');
		}

		$this->redirectTo('devices', 'view', array(
			'id' => $params['device_id']
		));
	}

}
