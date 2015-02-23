<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A Handler for a Ticket Created Webhook Event
 */
class ColoCrossing_TicketCreatedEvent extends ColoCrossing_Event {

    /**
     * The ColoCrossing Support Ticket
     * @var ColoCrossing_Object_SupportTicket
     */
    protected $ticket;

	/**
	 * Constructs an event
	 * @param integer  	$id        The Id of The Type
	 * @param string   	$name      The Name of The Type
	 * @param array   	$data      The Data
	 */
	protected function __construct($id, $name, array $data) {
		parent::__construct($id, $name, $data);

		if(empty($this->data['ticket']) || empty($this->data['ticket']['id']) || $this->data['ticket']['id'] <= 0) {
			throw new Exception('Ticket not provided');
		}

		$this->ticket = $this->api->support_tickets->find($this->data['ticket']['id']);

		if(empty($this->ticket)) {
			throw new Exception('Ticket not found');
		}
	}

	/**
	 * Executes this event
	 * @return boolean True if executes succesfully
	 */
	public function execute() {
		$cc_department = $this->ticket->getDepartment();

		if(empty($cc_department) && $cc_department->getId() != 11 && $cc_department->getName() != 'Abuse') {
			return false;
		}

		$department = $this->getDepartment();

		if(empty($department)) {
			return false;
		}

		$subject = $this->getSubject();
		$message = $this->getMessage();

		if(empty($subject) || empty($message)) {
			return false;
		}

		$priority = $this->getPriority();
		$status = $this->getStatus();

		$devices = $this->ticket->getDevices();

		foreach ($devices as $device) {
			$service = ColoCrossing_Model_Service::findByDevice($device);

			if(empty($service)) {
				continue;
			}

			$client = $service->getClient();

			if(empty($service)) {
				continue;
			}

			$subject = $this->getSubject($device);

			$ticket = $this->executeWHMCSCommand('openticket', array(
	 			'subject' => $this->formatSubject($subject, $device, $service),
				'message' => $this->formatMessage($message, $device, $service),
				'clientid' => $client->getId(),
				'deptid' => $department->getId(),
				'admin' => true,
	 			'priority' => $priority
	 		));

	 		$this->executeWHMCSCommand('updateticket', array(
	 			'ticketid' => intval($ticket['id']),
	 			'status' => $status
	 		));
		}

 		return true;
	}

	/**
	 * Retrieves the Message from the First Response. Must be from Admin or System.
	 * @return string|null The Message
	 */
	private function getMessage() {
		$responses = $this->ticket->getResponses();

		if(empty($responses)) {
			return null;
		}

		$response = $responses->get(0);
		$user = $response->getUser();

		if(empty($user) || $user->getType() != 'system') {
			return null;
		}

		$message = $response->getMessage();

		$lines = preg_split("/\r\n|\n|\r/", $message);

		if(count($lines) > 2 && substr($lines[0], -1) == ',' && empty($lines[1])) {
			$lines = array_slice($lines, 2);

			return implode("\n", $lines);
		}

		return $message;
	}

	/**
	 * Retrieves the Subject of the Ticket
	 * @return string The Subject
	 */
	private function getSubject() {
		return $this->ticket->getSubject();
	}

	/**
	 * Retrieves the Priority of the Ticket
	 * @return string|null The Priority
	 */
	private function getPriority() {
		$priority = $this->ticket->getPriority();

		if(empty($priority)) {
			return 'Low';
		}

		switch ($priority->getId()) {
			case 1:
				return 'Low';
			case 2:
				return 'Medium';
			case 3:
			case 4:
				return 'High';
		}

		return 'Low';
	}

	/**
	 * Get the Department
	 *
	 * @return ColoCrossing_Model_SupportDepartment The Department
	 */
	public function getDepartment() {
		$configuration = $this->module->getConfiguration();
		$options = array();

		if(isset($configuration['abuse_ticket_department'])) {
			$options['filters'] = array(
				'name' => $configuration['abuse_ticket_department']
			);
		}

		$departments = ColoCrossing_Model_SupportDepartment::findAll($options);

	    return count($departments) ? $departments[0] : null;
	}

	/**
	 * Get the Status for the new Ticket
	 *
	 * @return string The Status
	 */
	public function getStatus() {
		$configuration = $this->module->getConfiguration();

		return isset($configuration['abuse_ticket_status']) ? $configuration['abuse_ticket_status'] : 'Open';
	}

	/**
	 * Formats the Subject for the new Ticket
	 * @param  string 						$subject The Subject
	 * @param  ColoCrossing_Object_Device 	$device  The Device
	 * @param  ColoCrogging_Model_Service 	$service The Service
	 * @return string         The Formatted Subject
	 */
	public function formatSubject($subject, $device, $service) {
		$service_hostname = $service->getHostname();

		if(empty($service_hostname)) {
			return $subject;
		}

		$device_name = $device->getName();

		return str_replace($device_name, $service_hostname, $subject);
	}

	/**
	 * Formats the Message for the new Ticket
	 * @param  string 						$message The Message
	 * @param  ColoCrossing_Object_Device 	$device  The Device
	 * @param  ColoCrogging_Model_Service 	$service The Service
	 * @return string         The Formatted Message
	 */
	public function formatMessage($message, $device, $service) {
		$service_hostname = $service->getHostname();

		if(empty($service_hostname)) {
			return $message;
		}

		$device_name = $device->getName();

		return str_replace($device_name, $device_name . ' (' . $service_hostname . ')', $message);
	}

}