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

		$department_id = intval($department->getId());

		$message = $this->getMessage();

		if(empty($message)) {
			return false;
		}

		$subject = $this->ticket->getSubject();
		$priority = $this->getPriority();

		$clients = $this->getClients();

		foreach ($clients as $client) {
			$client_id = intval($client->getId());

			$this->executeWHMCSCommand('openticket', array(
				'clientid' => $client_id,
				'deptid' => $department_id,
				'admin' => true,
	 			'subject' => $subject,
				'message' => $message,
	 			'priority' => $priority
	 		));
		}

 		return count($clients) > 0;
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

		if(isset($configuration['abuse_department'])) {
			$options['filters'] = array(
				'name' => $configuration['abuse_department']
			);
		}

		$departments = ColoCrossing_Model_SupportDepartment::findAll($options);

	    return count($departments) ? $departments[0] : null;
	}

	/**
	 * Get the Clients with Devices Assigned to Ticket
	 *
	 * @return array<ColoCrossing_Model_Client> The Clients
	 */
	public function getClients() {
		$devices = $this->ticket->getDevices();
		$clients = array();

		foreach ($devices as $device) {
			$service = ColoCrossing_Model_Service::findByDevice($device);

			if(isset($service)) {
				$client = $service->getClient();

				if(isset($client)) {
					$clients[] = $client;
				}
			}
		}

		return array_unique($clients);
	}

}