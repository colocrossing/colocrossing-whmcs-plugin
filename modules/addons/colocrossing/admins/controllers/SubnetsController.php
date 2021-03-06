<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Subnets Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Subnets
 */
class ColoCrossing_Admins_SubnetsController extends ColoCrossing_Admins_Controller {

	public function index(array $params) {
		$this->filters = array(
			'query' => isset($params['query']) ? $params['query'] : '',
		);

		$this->sort = isset($params['sort']) ? $params['sort'] : 'ip_address';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'desc' ? 'desc' : 'asc';
		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;

		$this->subnets = $this->api->subnets->findAll(array(
			'filters' => $this->filters,
			'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
			'page_number' => $this->page,
			'page_size' => 30,
			'format' => 'paged'
		));
	}

	public function view(array $params) {
		$this->subnet = $this->api->subnets->find($params['id']);

		if(empty($this->subnet)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('subnets', 'index');
		}

		$this->device = $this->subnet->getDevice();
		$this->rdns_records = $this->subnet->isReverseDnsEnabled() ? $this->subnet->getReverseDNSRecords() : null;
		$this->null_routes = $this->subnet->isNullRoutesEnabled() ? ColoCrossing_Utility::getMapCollection($this->subnet->getNullRoutes(), 'ip_address') : null;
	}

	public function update(array $params) {
		$this->subnet = $this->api->subnets->find($params['id']);

		if(empty($this->subnet)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('subnets', 'index');
		}

		if(!$this->subnet->isReverseDnsEnabled()) {
			$this->setFlashMessage('Reverse DNS records are not enabled for this subnet.', 'error');
		} else if($this->subnet->isPendingServiceRequest()) {
			$this->setFlashMessage('Reverse DNS records can\'t be edited while a service request is pending.', 'error');
		} else {
			$request_result = $this->subnet->updateReverseDNSRecords($params['rdns_records']);

			if($request_result === true) {
				$this->log('Reverse DNS records for ' . $this->subnet->getCIDRIpAddress() . ' were updated.');
				$this->setFlashMessage('Reverse DNS records have successfully been edited.', 'success');
			} else if($request_result === false) {
				$this->setFlashMessage('An error occurred while saving reverse DNS records.', 'error');
			} else {
				$this->log('Reverse DNS record service request for ' . $this->subnet->getCIDRIpAddress() . ' was submitted.');
				$this->setFlashMessage('Reverse DNS record service request has successfully been submitted.', 'success');
			}
		}

		$this->redirectTo('subnets', 'view', array(
			'id' => $this->subnet->getId()
		));
	}

}
