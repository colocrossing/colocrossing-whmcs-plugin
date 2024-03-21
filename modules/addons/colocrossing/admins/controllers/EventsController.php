<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require_once dirname(__DIR__, 2) . '/models/Event.php';

/**
 * ColoCrossing Events Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Events
 */
class ColoCrossing_Admins_EventsController extends ColoCrossing_Admins_Controller {

	public function index(array $params) {
		$this->sort = isset($params['sort']) ? $params['sort'] : 'time';
		$this->order = isset($params['order']) && strtolower($params['order']) == 'asc' ? 'asc' : 'desc';
		$this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;
		$this->page_size = isset($params['page_size']) && is_numeric($params['page_size']) ? intval($params['page_size']) : 30;

		$this->events = ColoCrossing_Model_Event::findAll(array(
			'sort' => $this->sort,
			'order' => $this->order,
			'pagination' => array(
				'number' => $this->page,
				'size' =>$this->page_size
			)
		));

		$this->total_record_count = ColoCrossing_Model_Event::getTotalRecordCount();
		$this->page_count = ceil($this->total_record_count / $this->page_size);
	}

}
