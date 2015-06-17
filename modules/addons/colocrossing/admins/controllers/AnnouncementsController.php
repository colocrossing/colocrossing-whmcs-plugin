<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Announcements Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Announcements
 */
class ColoCrossing_Admins_AnnouncementsController extends ColoCrossing_Admins_Controller {

  public function index(array $params) {
    $this->filters = array(
      'query' => isset($params['query']) ? $params['query'] : '',
    );

    $this->sort = isset($params['sort']) ? $params['sort'] : 'created_at';
    $this->order = isset($params['order']) && strtolower($params['order']) == 'asc' ? 'asc' : 'desc';
    $this->page = isset($params['page']) && is_numeric($params['page']) ? intval($params['page']) : 1;

    $announcements = $this->api->announcements->findAll(array(
      'filters' => $this->filters,
      'sort' => ($this->order == 'asc' ? '+' : '-') . $this->sort,
      'page_number' => $this->page,
      'page_size' => 30,
      'format' => 'paged'
    ));

    $this->num_records = $announcements->getTotalRecordCount();
    $this->num_pages = $announcements->size();

    $this->announcements = $announcements->current();
  }

  public function view(array $params) {
    $this->announcement = $this->api->announcements->find($params['id']);

    if(empty($this->announcement)) {
      $this->setFlashMessage('The announcement was not found.', 'error');
      $this->redirectTo('announcements', 'index');
    }

    $this->type = $this->announcement->getType();
    $this->severity = $this->announcement->getSeverity();

    $this->devices = $this->announcement->getDevices(array(
      'compact' => true
    ));
    $this->services = array();

    foreach ($this->devices as $device) {
      $device_id = $device->getId();
      $service = ColoCrossing_Model_Service::findByDevice($device_id);

      if(isset($service)) {
        $this->services[$device_id] = $service;
      }
    }

    $this->ticket_departments = ColoCrossing_Model_SupportDepartment::findAll();
    $this->ticket_statuses = ColoCrossing_Model_SupportStatus::findAll();
    $this->ticket_priorities = array('Low', 'Medium', 'High');
  }
}
