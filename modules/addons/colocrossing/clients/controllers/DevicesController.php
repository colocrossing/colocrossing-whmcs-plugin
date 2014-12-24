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

		if(empty($this->device) || empty($this->service) || $this->service->isAssignedToUser($this->current_user)
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
		}

		if($this->type->isPowerEndpoint()) {
			$this->power_distribution_units = $this->device->getPowerDistributionUnits();
		}

		return $this->device->getName();
	}

}
