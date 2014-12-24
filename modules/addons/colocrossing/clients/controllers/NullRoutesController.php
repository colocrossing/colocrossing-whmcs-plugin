<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Null Routes Controller for WHMCS Client Module.
 * Handles Responding to all Requests Related to Null Routes
 */
class ColoCrossing_Clients_NullRoutesController extends ColoCrossing_Clients_Controller {

	public function create(array $params) {
		$this->subnet = $this->api->subnets->find($params['id']);
		$this->device = isset($this->subnet) ? $this->subnet->getDevice() : null;

		if(empty($this->subnet) || empty($this->device) || empty($this->current_user)
			|| !$this->current_user->hasPermissionForDevice($this->device)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$expire_date = max(min(strtotime('+' . $params['duration']), strtotime('+30 days')), time());

		$this->null_route = $this->subnet->addNullRoute($params['ip_address'], $params['comment'], $expire_date);

		if($this->null_route === false) {
			$this->setFlashMessage('An error occurred while adding null route.', 'error');
		} else {
			ColoCrossing_Model_Event::log('Null route for ' . $params['ip_address'] . ' was added.');
			$this->setFlashMessage('The null route for ' . $params['ip_address'] . ' was added successfully!', 'success');
		}

		$this->redirectTo('subnets', 'view', array(
			'id' => $params['subnet_id']
		));
	}

	public function destroy(array $params) {
		$this->null_route = $this->api->null_routes->find($params['id']);

		if(empty($this->null_route)) {
			$this->setFlashMessage('The null route was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$this->subnet = $this->null_route->getSubnet();
		$this->device = isset($this->subnet) ? $this->subnet->getDevice() : null;

		if(empty($this->subnet) || empty($this->device) || empty($this->current_user)
			|| !$this->current_user->hasPermissionForDevice($this->device)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('error', 'missing');
		}

		$this->ip_address = $this->null_route->getIpAddress();

		if(!$this->null_route->isRemovable()) {
			$this->setFlashMessage('The null route on ' . $this->ip_address . ' is not removable.', 'error');
		} else if($this->null_route->remove()) {
			ColoCrossing_Model_Event::log('Null route on ' . $this->ip_address . ' was removed.');
			$this->setFlashMessage('The null route on ' . $this->ip_address . ' was removed successfully!', 'success');
		} else {
			$this->setFlashMessage('An error occurred while removing null route on ' . $this->ip_address . '.', 'error');
		}

		$this->redirectTo('subnets', 'view', array(
			'id' => $this->subnet->getId()
		));
	}

}
