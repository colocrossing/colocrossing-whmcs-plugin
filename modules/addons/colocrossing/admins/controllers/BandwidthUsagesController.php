<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Bandwidth Usage Controller for WHMCS Admin Module.
 * Handles Displaying Bandwidth Usages for Devices
 */
class ColoCrossing_Admins_BandwidthUsagesController extends ColoCrossing_Admins_Controller {

	public function index(array $params) {
		$this->sort = isset($params['sort']) ? $params['sort'] : 'total_usage';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'asc' ? 'asc' : 'desc';
		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;

		$bandwidth_usages = $this->api->bandwidth_usages->findAll(array(
			'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
			'page_number' => $this->page,
			'page_size' => 30,
			'format' => 'paged'
		));

		$this->num_records = $bandwidth_usages->getTotalRecordCount();
		$this->num_pages = $bandwidth_usages->size();

		$this->bandwidth_usages = $bandwidth_usages->current();

		/*$devices = $this->api->devices->findAll(array(
			'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
			'page_number' => $this->page,
			'page_size' => 30,
			'format' => 'paged'
		));

		$this->num_records = $devices->getTotalRecordCount();
		$this->num_pages = $devices->size();

		$this->devices = $devices->current();
		$this->devices_services = array();

		foreach ($this->devices as $index => $device) {
			$device_id = $device->getId();
			$service = ColoCrossing_Model_Service::findByDevice($device_id);

			if(isset($service)) {
				$this->devices_services[$device_id] = $service;
			}
		}*/
	}

}
