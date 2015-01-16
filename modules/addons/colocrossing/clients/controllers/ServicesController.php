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

		$device_id = $service->getDeviceId();

		if(empty($device_id)) {
			return 'Service is not assigned to a device.';
		}

		$this->redirectTo('devices', 'view', array(
			'id' => $device_id
		));
	}

	function view(array $params) {
	    $this->service = ColoCrossing_Model_Service::find($params['id']);

	    try {
	        $this->device = isset($this->service) ? $this->service->getDevice() : null;
	    } catch(ColoCrossing_Error $e) {
	        $this->device = null;
	    }

	    if(empty($this->service) || empty($this->device)) {
	    	$this->disableRendering();
	        return false;
	    }

	    $this->type = $this->device->getType();
	    $this->subnets = $this->type->isNetworkEndpoint() || $this->type->isVirtual() ? $this->device->getSubnets() : null;

	    if($this->type->isNetworkEndpoint()) {
	        $switches = $this->device->getSwitches();

	        $this->is_network_controllable = ColoCrossing_Utilities::isDeviceControllable($switches);
	        $this->network_status = ColoCrossing_Utilities::getDeviceOverallStatus($switches);
	        $this->network_status_color = ColoCrossing_Utilities::getPortStatusColor($this->network_status);

	        $this->bandwidth_graphs = array();
	        $this->bandwidth_graph_durations = $this->getBandwidthGraphDurations($this->service);

	        foreach ($switches as $i => $switch) {
	            foreach ($switch->getPorts() as $j => $port) {
	                if($port->isBandwidthGraphAvailable()) {
	                    $this->bandwidth_graphs[] = array(
	                        'switch_id' => $switch->getId(),
	                        'port_id' => $port->getId()
	                    );
	                }
	            }
	        }
	    }

	    if($this->type->isPowerEndpoint()) {
	        $power_distribution_units = $this->device->getPowerDistributionUnits();

	        $this->is_power_controllable = ColoCrossing_Utilities::isDeviceControllable($power_distribution_units);
	        $this->power_status = ColoCrossing_Utilities::getDeviceOverallStatus($power_distribution_units);
	        $this->power_status_color = ColoCrossing_Utilities::getPortStatusColor($this->power_status);
	    }
	}

	private function getBandwidthGraphDurations($service) {
		$bandwidth_graph_durations = array(
            'current' => 'Current Billing Period',
            'previous' => 'Previous Billing Period',
            '12 hours' => 'Last 12 Hours',
            '1 day' => 'Last Day',
            '1 week' => 'Last Week',
            '2 weeks' => 'Last 2 Weeks',
            '1 month' => 'Last Month',
            '3 months' => 'Last 3 Months'
        );

        $start_date = $service->getRegistrationDate();
        $due_date = $service->getNextDueDate();
        $length = $service->getBillingCycleLength();

        if($start_date > strtotime('-' . $length, $due_date)) {
            unset($bandwidth_graph_durations['previous']);
        }

        return $bandwidth_graph_durations;
	}

	public function controlDevicePower(array $params) {
		$service = ColoCrossing_Model_Service::find($params['id']);
		$device = isset($service) ? $service->getDevice() : null;

		if(empty($device) || empty($service) || !$service->isAssignedToUser($this->current_user)
			|| !$service->isActive()) {
			$this->setFlashMessage('The device/service was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$status = $params['status'];
		$status_description = ColoCrossing_Utilities::getPortStatusDescription($status);

		$power_distribution_units = $device->getPowerDistributionUnits();

		$success = true;
		foreach ($power_distribution_units as $pdu) {
			$ports = $pdu->getPorts();

			foreach ($ports as $port) {
				$result = $port->setStatus($status);

				if($result) {
					$description = 'Power port ' . $port->getId() . ' of ' . $pdu->getName() . ' assigned to ' . $device->getName() . ' was '  . $status_description . '.';
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

		$this->redirectToUrl(ColoCrossing_Utilities::buildUrl('/clientarea.php', array(
			'action' => 'productdetails',
			'id' => $service->getId()
		)));
	}

	public function controlDeviceNetwork(array $params) {
		$service = ColoCrossing_Model_Service::find($params['id']);
		$device = isset($service) ? $service->getDevice() : null;

		if(empty($device) || empty($service) || !$service->isAssignedToUser($this->current_user)
			|| !$service->isActive()) {
			$this->setFlashMessage('The device/service was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$status = $params['status'];
		$status_description = ColoCrossing_Utilities::getPortStatusDescription($status);

		$switches = $device->getSwitches();

		$success = true;
		foreach ($switches as $switch) {
			$ports = $switch->getPorts();

			foreach ($ports as $port) {
				$result = $port->setStatus($status);

				if($result) {
					$description = 'Network port ' . $port->getId() . ' of ' . $switch->getName() . ' assigned to ' . $device->getName() . ' was '  . $status_description . '.';
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

		$this->redirectToUrl(ColoCrossing_Utilities::buildUrl('/clientarea.php', array(
			'action' => 'productdetails',
			'id' => $service->getId()
		)));
	}

}
