<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Sale Devices Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Sales Devices
 */
class ColoCrossing_Admins_SalesDevicesController extends ColoCrossing_Admins_Controller {

    public function index(array $params) {
        $this->sort = isset($params['sort']) ? $params['sort'] : 'name';
        $this->order = isset($params['order']) && strtolower($params['order']) == 'desc' ? 'desc' : 'asc';

        $this->page_number = isset($params['page_number']) && is_numeric($params['page_number']) ? intval($params['page_number']) : 1;
        $this->page_size = isset($params['page_size']) && is_numeric($params['page_size']) ? intval($params['page_size']) : 25;

        $this->devices = $this->api->sales_devices->findAll(array(
            'filters' => $this->filters,
            'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
            'page_number' => $this->page_number,
            'page_size' => $this->page_size,
            'format' => 'paged'
        ));

        $this->num_records = $this->devices->getTotalRecordCount();
		$this->num_pages = $this->devices->size();
    }

    public function view(array $params) {
        $this->device = $this->api->sales_devices->find($params['id']);

        if(empty($this->device)) {
            $this->setFlashMessage('The device was not found.', 'error');
            $this->redirectTo('sales-devices', 'index');
        }

        $this->assets = $this->device->getAssets();
		$this->type = $this->device->getType();
    }

}
