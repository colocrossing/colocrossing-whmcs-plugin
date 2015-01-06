<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Subnets Controller for WHMCS Client Module.
 * Handles Responding to all Requests Related to Subnets
 */
class ColoCrossing_Clients_SubnetsController extends ColoCrossing_Clients_Controller {

	public function view(array $params) {
		$this->subnet = $this->api->subnets->find($params['id']);
		$this->device = isset($this->subnet) ? $this->subnet->getDevice() : null;

		if(empty($this->subnet) || empty($this->device) || empty($this->current_user)
			|| !$this->current_user->hasPermissionForDevice($this->device)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$this->ip_addresses = $this->subnet->getIpAddresses();

		$this->rdns_records = $this->subnet->isReverseDnsEnabled() ? $this->subnet->getReverseDNSRecords() : null;
		$this->null_routes = $this->subnet->isNullRoutesEnabled() ? ColoCrossing_Utility::getMapCollection($this->subnet->getNullRoutes(), 'ip_address') : null;

		return $this->subnet->getCIDRIpAddress();
	}

	public function update(array $params) {
		$this->subnet = $this->api->subnets->find($params['id']);
		$this->device = isset($this->subnet) ? $this->subnet->getDevice() : null;

		if(empty($this->subnet) || empty($this->device) || empty($this->current_user)
			|| !$this->current_user->hasPermissionForDevice($this->device)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('error', 'missing');
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
		), 'rdns-records');
	}

}
