<?php

/**
 * ColoCrossing Devices Controller for WHMCS Module.
 * Handles Responding to all Requests Related to Devices
 */
class ColoCrossing_DevicesController extends ColoCrossing_Controller {

	public function index(array $params) {
		$this->filters = array(
			'query' => isset($params['query']) ? $params['query'] : '',
		);

		$this->sort = isset($params['sort']) ? $params['sort'] : 'name';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'desc' ? 'desc' : 'asc';
		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;

		$this->devices = $this->api->devices->findAll(array(
			'filters' => $this->filters,
			'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
			'page_number' => $this->page,
			'page_size' => 30,
			'format' => 'paged'
		));
	}

	public function view(array $params) {
		$this->device = $this->api->devices->find($params['id']);

		if(empty($this->device)) {
			$this->setFlashMessage('The device was not found.', 'error');
			$this->redirectToModule($params['modulelink'], array(
				'controller' => 'devices',
				'action' => 'index'
			));
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


	public function bandwidthGraph(array $params) {
		$start = is_numeric($params['start']) ? intval($params['start']) : strtotime($params['start']);
		$end = is_numeric($params['end']) ? intval($params['end']) : strtotime($params['end']);

		$graph = $this->api->devices->switches->getBandwidthGraph($params['switch_id'], $params['port_id'], $params['id'], $start, $end);

		if (empty($graph))
		{
			http_response_code(404);
			return false;
		}

		ob_clean();
    	ob_start();

    	header('Content-Type: image/png');
    	http_response_code(imagepng($graph) ? 200 : 500);
    	imagedestroy($graph);

    	ob_end_flush();
    	exit;
	}


	public function updatePowerPorts(array $params) {
		$status = $params['status'];
		$comment = empty($params['comment']) ? null : $params['comment'];

		$success = true;

		foreach ($params['power_distribution_units'] as $pdu_id => $ports) {
			foreach ($ports as $port_id => $device_id) {
				$success &= $this->api->devices->pdus->setPortStatus($pdu_id, $port_id, $device_id, $status, $comment);
			}
		}

		if($success) {
			switch ($status) {
				case 'on':
					$status_description = 'turned on';
					break;
				case 'off':
					$status_description = 'turned off';
					break;
				case 'restart':
					$status_description = 'restarted';
					break;
				default:
					$status_description = 'controlled';
					break;
			}

			$this->setFlashMessage('The power ports have successfully been ' . $status_description . '.', 'success');
		} else {
			$this->setFlashMessage('An error occurred while controlling the power ports.', 'error');
		}

		$this->redirectToModule($params['modulelink'], array(
			'controller' => 'devices',
			'action' => 'view',
			'id' => isset($params['origin_device_id']) ? $params['origin_device_id'] : $params['device_id']
		));
	}

	public function updateNetworkPorts(array $params) {
		$status = $params['status'];
		$comment = empty($params['comment']) ? null : $params['comment'];

		$success = true;

		foreach ($params['switches'] as $pdu_id => $ports) {
			foreach ($ports as $port_id => $device_id) {
				$success &= $this->api->devices->switches->setPortStatus($pdu_id, $port_id, $device_id, $status, $comment);
			}
		}

		if($success) {
			switch ($status) {
				case 'on':
					$status_description = 'turned on';
					break;
				case 'off':
					$status_description = 'turned off';
					break;
				default:
					$status_description = 'controlled';
					break;
			}

			$this->setFlashMessage('The network ports have successfully been ' . $status_description . '.', 'success');
		} else {
			$this->setFlashMessage('An error occurred while controlling the network ports.', 'error');
		}

		$this->redirectToModule($params['modulelink'], array(
			'controller' => 'devices',
			'action' => 'view',
			'id' => isset($params['origin_device_id']) ? $params['origin_device_id'] : $params['device_id']
		));
	}

}
