<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Null Routes Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Null Routes
 */
class ColoCrossing_Admins_NullRoutesController extends ColoCrossing_Admins_Controller {

	public function index(array $params) {
		$this->filters = array(
			'query' => isset($params['query']) ? $params['query'] : '',
		);

		$this->sort = isset($params['sort']) ? $params['sort'] : 'ip_address';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'desc' ? 'desc' : 'asc';
		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;

		$this->null_routes = $this->api->null_routes->findAll(array(
			'filters' => $this->filters,
			'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
			'page_number' => $this->page,
			'page_size' => 30,
			'format' => 'paged'
		));
	}

	public function create(array $params) {
		$this->subnet = $this->api->subnets->find($params['subnet_id']);

		if(empty($this->subnet)) {
			$this->setFlashMessage('The subnet was not found.', 'error');
			$this->redirectTo('subnets', 'index');
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
			$this->redirectTo('null-routes', 'index');
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

		if(isset($params['origin']) && $params['origin'] == 'subnets#view') {
			$this->subnet = $this->null_route->getSubnet();

			$this->redirectTo('subnets', 'view', array(
				'id' => $this->subnet->getId()
			));
		} else {
			$this->redirectTo('null-routes', 'index');
		}
	}

}
