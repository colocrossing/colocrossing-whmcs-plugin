<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Devices Controller for WHMCS Client Module.
 * Handles Responding to all Requests Related to Devices
 */
class ColoCrossing_Clients_DevicesController extends ColoCrossing_Clients_Controller {

	public function view(array $params) {
		$this->device = $this->api->devices->find($params['id']);
		$this->service = ColoCrossing_Model_Service::findByDevice($params['id']);

		if(empty($this->device) || empty($this->service) || !$this->service->isAssignedToUser($this->current_user)
			|| !$this->service->isActive()) {
			$this->setFlashMessage('The device/service was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$this->product = $this->service->getProduct();
		$this->product_group = isset($this->product) ? $this->product->getGroup() : null;

		$this->type = $this->device->getType();

		$this->assets = $this->device->getAssets();
		$this->subnets = $this->type->isNetworkEndpoint() || $this->type->isVirtual() ? $this->device->getSubnets() : array();

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

			if(count($this->bandwidth_graphs)) {
				$this->bandwidth_graph_durations = array(
					"current" => 'Current Billing Period',
					"previous" => 'Previous Billing Period',
					"12 hours" => "Last 12 Hours",
					"1 day" => "Last Day",
					"1 week" => "Last Week",
					"2 weeks" => "Last 2 Weeks",
					"1 month" => "Last Month",
					"3 months" => "Last 3 Months"
				);

				$start_date = $this->service->getRegistrationDate();
				$due_date = $this->service->getNextDueDate();
				$length = $this->service->getBillingCycleLength();

				if($start_date > strtotime('-' . $length, $due_date)) {
					unset($this->bandwidth_graph_durations["previous"]);
				}
			}
		}

		if($this->type->isPowerEndpoint()) {
			$this->power_distribution_units = $this->device->getPowerDistributionUnits();
		}

		if($this->type->isRack()) {
			$this->devices = $this->device->getDevices();
		}

		return $this->device->getName();
	}

	public function bandwidthGraph(array $params) {
		$this->disableRendering();

		$device = $this->api->devices->find($params['id']);
		$service = ColoCrossing_Model_Service::findByDevice($params['id']);

		if(empty($device) || empty($service) || !$service->isAssignedToUser($this->current_user) || !$service->isActive()) {
			$this->setResponseCode(404);
			return null;
		}

		switch ($params['duration']) {
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
				$start = strtotime('-' . $params['duration']);
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

	public function updatePowerPorts(array $params) {
		$status = $params['status'];
		$status_description = ColoCrossing_Utilities::getPortStatusDescription($status);

		$success = true;

		foreach ($params['power_distribution_units'] as $pdu_id => $ports) {
			foreach ($ports as $port_id => $device_id) {
				$device = $this->api->devices->find($device_id);

				if(empty($device) || !$device->getType()->isPowerEndpoint() || !$this->isCurrentUserAssignedToDevice($device)) {
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

				$result = $this->api->devices->pdus->setPortStatus($pdu, $port, $device, $status);

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
			'id' => $params['origin_device_id']
		), 'power-ports');
	}

	public function updateNetworkPorts(array $params) {
		$status = $params['status'];
		$status_description = ColoCrossing_Utilities::getPortStatusDescription($status);
		$comment = empty($params['comment']) ? null : $params['comment'];

		$success = true;

		foreach ($params['switches'] as $switch_id => $ports) {
			foreach ($ports as $port_id => $device_id) {
				$device = $this->api->devices->find($device_id);

				if(empty($device) || !$device->getType()->isNetworkEndpoint() || !$this->isCurrentUserAssignedToDevice($device)) {
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

				$result = $this->api->devices->switches->setPortStatus($switch, $port, $device, $status);

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
		), 'network-ports');
	}

	/**
	 * Determines if Current User is Assigned to Device
	 * @param  ColoCrossing_Object_Device  $device The Device
	 * @return boolean       True if the current user has permissions to the device
	 */
	private function isCurrentUserAssignedToDevice($device) {
		return isset($this->current_user) && $this->current_user->hasPermissionForDevice($device);
	}

	public function updateIpmi(array $params)
	{
		$device = $this->api->devices->find($params['device_id']);
		// if(!isCurrentUserAssignedToDevice($device))
		// {
		// 	$this->setFlashMessage('You do not have permissions to accesss this device.', 'error');

		// 	$this->redirectTo('devices', 'view', array(
		// 		'id' => $params['device_id']
		// 	));
		// }

		$config = $device->getIpmiConfiguration();
		$status = $config->getNullRouteStatus()['id'];
		switch($params['ipmi_action'])
		{
			case 'lift':
				if(in_array($status, array(2, 4)))
				{
					$this->setFlashMessage('Unable to lift a null route that is not active.', 'error');
					break;
				}

				$result =  $this->api->devices->ipmi_null_route->setNullRouteStatus($config, $params['device_id'], 'lift');

				if($result) {
					$this->setFlashMessage('Null Route was successfully lifted for 4 hours.');
				} else {
					$this->setFlashMessage('An error occurred while attempting to lift the null route.', 'error');
				}
				break;

			case 'replace':
				if($status != 2)
				{
					$this->setFlashMessage('Unable to replace a null route that is not lifted.', 'error');
					break;
				}

				$result =  $this->api->devices->ipmi_null_route->setNullRouteStatus($config, $params['device_id'], 'replace');

				if($result) {
					$this->setFlashMessage('Null Route was successfully replaced.');
				} else {
					$this->setFlashMessage('An error occurred while attempting to replace the null route.', 'error');
				}
				break;

			case 'renew':
				if($status != 2)
				{
					$this->setFlashMessage('Unable to renew a null route lift that is not lifted.', 'error');
					break;
				}

				$result =  $this->api->devices->ipmi_null_route->setNullRouteStatus($config, $params['device_id'], 'renew');

				if($result) {
					$this->setFlashMessage('Null Route was successfully lifted for 4 more hours.');
				} else {
					$this->setFlashMessage('An error occurred while attempting to renew the null route lift.', 'error');
				}
				break;
			default:
				$this->setFlashMessage('Unrecognized ipmi action: '.$params['ipmi_action'], 'error');
				break;
		}

		$this->redirectTo('devices', 'view', array(
			'id' => $params['device_id']
		));
	}

}
