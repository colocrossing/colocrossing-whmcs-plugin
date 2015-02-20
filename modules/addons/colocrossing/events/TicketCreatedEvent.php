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
 		return $this->executeWHMCSCommand('openticket', array(
			'clientid' => '1',
			'deptid' => '1',
			'admin' => '1',
 			'subject' => $this->ticket->getSubject(),
			'message' => 'This is a sample ticket opened by the API as an admin user',
 			'priority' => 'Low'
 		));
	}

}