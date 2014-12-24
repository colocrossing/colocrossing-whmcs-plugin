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

		return $this->subnet->getCIDRIpAddress();
	}

}
