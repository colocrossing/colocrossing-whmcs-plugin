<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Announcements Controller for WHMCS Admin Module.
 * Handles Responding to all Requests Related to Announcements
 */
class ColoCrossing_Admins_AnnouncementsController extends ColoCrossing_Admins_Controller {

  private static $TICKET_STATUSES = array('Low', 'Medium', 'High');

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
    $this->ticket_priorities = self::$TICKET_STATUSES;
  }

  public function send(array $params) {
    $announcement = $this->api->announcements->find($params['id']);

    if(empty($announcement)) {
      $this->setFlashMessage('The announcement was not found.', 'error');
      $this->redirectTo('announcements', 'index');
    }

    $user = $this->module->getSystemUsername();

    $department = ColoCrossing_Model_SupportDepartment::find($params['department_id']);
    $status = ColoCrossing_Model_SupportStatus::find($params['status_id']);
    $priority = self::$TICKET_STATUSES[$params['priority_id']];

    if(empty($user) || empty($department) || empty($status) || empty($priority)) {
      $this->setFlashMessage('An error occured while sending announcement.', 'error');
      $this->redirectTo('announcements', 'view', array(
        'id' => $announcement->getId()
      ));
    }

    $success = true;
    $clients = $this->getClients($announcement);

    foreach ($clients as $client_id => $entries) {
      $message = $this->formatMessage($params['message'], $entries);

      $ticket = localAPI('openticket', array(
        'subject' => $params['subject'],
        'message' => $message,
        'clientid' => $client_id,
        'deptid' => $department->getId(),
        'admin' => true,
        'priority' => $priority
      ), $user);

      if($ticket['result'] == 'success') {
        localAPI('updateticket', array(
          'ticketid' => intval($ticket['id']),
          'status' => $status->getName()
        ), $user);
      } else {
        $success = false;
      }
    }

    if($success) {
      $this->setFlashMessage('Announcement has successfully been sent to affected customers.', 'success');
      $this->log('Announcement #' . $announcement->getId() . ' was sent to affected customers.');
    } else {
      $this->setFlashMessage('An error occured while sending announcement.', 'error');
    }

    $this->redirectTo('announcements', 'view', array(
      'id' => $announcement->getId()
    ));
  }

  private function getClients($announcement) {
    $devices = $announcement->getDevices(array(
      'compact' => true
    ));
    $clients = array();

    foreach ($devices as $device) {
      $service = ColoCrossing_Model_Service::findByDevice($device);

      if(empty($service)) {
        continue;
      }

      $client_id = $service->getClientId();

      if(empty($client_id)) {
        continue;
      }

      if(empty($clients[$client_id])) {
        $clients[$client_id] = array();
      }

      $clients[$client_id][] = array(
        'device' => $device,
        'service' => $service
      );
    }

    return $clients;
  }

  private function formatMessage($message, $entries) {
    $affected_devices = array_map(function($entry) {
      return $entry['device']->getName() . '(' . $entry['service']->getHostname() . ')';
    }, $entries);

    return 'Affected Devices: ' .  implode(', ', $affected_devices) . "\n\n" . $message;
  }
}
