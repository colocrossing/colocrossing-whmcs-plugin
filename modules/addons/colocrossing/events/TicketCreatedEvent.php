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

		if(empty($this->data['ticket']) || empty($this->data['ticket']['id']) || intval($this->data['ticket']['id']) < 0) {
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
		$ticket_department = $this->ticket->getDepartment();

		if(empty($ticket_department) || !in_array($ticket_department->getId(), array(2, 11))) {
			return false;
		}

		$department = $this->getDepartment();

		if(empty($department)) {
			return false;
		}

		$subject = $this->getSubject();
		$messages = $this->getMessages();

		if(empty($subject) || empty($messages)) {
			return false;
		}

		$priority = $this->getPriority();
		$status = $this->getStatus();

		$ticket_devices = $this->ticket->getDevices();
		$clients = array();

		foreach ($ticket_devices as $device) {
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

		foreach ($clients as $client_id => $records) {
			$ticket = $this->executeWHMCSCommand('openticket', array(
	 			'subject' => $this->formatSubject($subject, $records),
				'message' => $this->formatMessage(array_shift($messages), $records),
				'clientid' => $client_id,
				'deptid' => $department->getId(),
				'admin' => true,
	 			'priority' => $priority
	 		));

	 		$this->executeWHMCSCommand('updateticket', array(
	 			'ticketid' => intval($ticket['id']),
	 			'status' => $status
	 		));

            foreach ($messages as $message) {
                $this->executeWHMCSCommand('addticketreply', array(
                    'ticketid' => intval($ticket['id']),
                    'adminusername' => 'System',
                    'message' => $message
                ));
            }
		}

 		return true;
	}

	/**
	 * Retrieves the Admin/System Messages from the Ticket
	 * @return array<string> The Messages
	 */
	private function getMessages() {
		$responses = $this->ticket->getResponses();

		if(empty($responses)) {
			return array();
		}

        $messages = array();

        foreach ($responses as $response)
        {
            $user = $response->getUser();

            if(empty($user) || !in_array($user->getType(), array('system', 'admin'))) {
                continue;
            }

            $message = $response->getMessage();

            $lines = preg_split("/\r\n|\n|\r/", $message);

            if(count($lines) > 2 && substr($lines[0], -1) == ',' && empty($lines[1])) {
                $lines = array_slice($lines, 2);

                $message = implode("\n", $lines);
            }

            list($body, $signature) = explode($user->getName(), $message);

            if(!empty($body)) {
                $message = $body . "\n" . $user->getName();
            }

            $messages[] = $message;
        }

		return $messages;
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
	 * @param  string 	$subject The Subject
	 * @param  array 		$records  The Devices/Services
	 * @return string   The Formatted Subject
	 */
	public function formatSubject($subject, $records) {
		if(count($records) != 1) {
			return $subject;
		}

		$device = $records[0]['device'];
		$service = $records[0]['service'];

		$hostname = $service->getHostname();

		if(empty($hostname)) {
			return $subject;
		}

		$name = $device->getName();

		return str_replace($name, $hostname, $subject);
	}

	/**
	 * Formats the Message for the new Ticket
	 * @param  string 	$message The Message
	 * @param  array 		$records  The Devices/Services
	 * @return string   The Formatted Message
	 */
	public function formatMessage($message, $records) {
		if(count($records) == 1) {
			$device = $records[0]['device'];
			$service = $records[0]['service'];

			$name = $device->getName();

			if(strpos($message, $name) !== false) {
				$hostname = $service->getHostname();

				if(empty($hostname)) {
					return $message;
				}

				return str_replace($name, $name . ' (' . $hostname . ')', $message);
			}
		}

		$affected_devices = array();

		foreach ($records as $record)
		{
			$device = $record['device'];
			$service = $record['service'];

			$name = $device->getName();
			$hostname = $service->getHostname();

			$affected_devices[] = $name . (empty($hostname) ? '' : ' (' . $hostname . ')');
		}

		return $message . "\n\nThe following devices are associated with this ticket:\n" . implode("\n", $affected_devices);
	}

}
