<?php

/**
 * ColoCrossing Subnets Controller for WHMCS Module.
 * Handles Responding to all Requests Related to Subnets
 */
class ColoCrossing_SubnetsController extends ColoCrossing_Controller {

	public function index(array $params) {
		$this->sort = isset($params['sort']) ? $params['sort'] : 'ip_address';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'desc' ? 'desc' : 'asc';

		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;

		$this->subnets = $this->api->subnets->findAll(array(
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
			$this->redirectToModule($params['modulelink'], array(
				'controller' => 'subnets',
				'action' => 'index'
			));
		}

		$this->device = $this->subnet->getDevice();
		$this->rdns_records = $this->subnet->getReverseDNSRecords();
		$this->null_routes = ColoCrossing_Utility::getMapCollection($this->subnet->getNullRoutes(), 'ip_address');
	}

	public function update(array $params) {
		$this->subnet = $this->api->subnets->find($params['id']);

		if(empty($this->subnet)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectToModule($params['modulelink'], array(
				'controller' => 'subnets',
				'action' => 'index'
			));
		}

		if(!$this->subnet->isReverseDnsEnabled()) {
			$this->setFlashMessage('Reverse DNS records are not enabled for this subnet.', 'error');
		} else if($this->subnet->isPendingServiceRequest()) {
			$this->setFlashMessage('Reverse DNS records can\'t be edited while a service request is pending.', 'error');
		} else {
			$request_result = $this->subnet->updateReverseDNSRecords($params['rdns_records']);

			if($request_result === true) {
				$this->setFlashMessage('Reverse DNS records have successfully been edited.', 'success');
			} else if($request_result === false) {
				$this->setFlashMessage('An error occurred while saving reverse DNS records.', 'error');
			} else {
				$this->setFlashMessage('Reverse DNS record service request has successfully been submitted.', 'success');
			}
		}

		$this->redirectToModule($params['modulelink'], array(
			'controller' => 'subnets',
			'action' => 'view',
			'id' => $this->subnet->getId()
		));
	}

}
