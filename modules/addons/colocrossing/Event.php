<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A Handler for a Webhook Event
 */
abstract class ColoCrossing_Event {

	/**
     * The Id of the Event Type
     * @var integer
     */
    protected $id;

	/**
     * The Name of the Event Type
     * @var string
     */
    protected $name;

    /**
     * The Data
     * @var array
     */
    protected $data;

	/**
     * The Module
     * @var ColoCrossing_Module
     */
    protected $module;

    /**
     * The API Client
     * @var ColoCrossing_API
     */
    protected $api;

	/**
	 * Constructs an event
	 * @param integer  	$id        The Id of The Type
	 * @param string   	$name      The Name of The Type
	 * @param array   	$data      The Data
	 */
	protected function __construct($id, $name, array $data) {
        $this->module = ColoCrossing_Module::getInstance();
        $this->api = ColoCrossing_API::getInstance();

		$this->id = $id;
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * Executes this event
	 */
	public abstract function execute();

	/**
	 * Executes the Specified WHMCS Command
	 * @param  string $type The Type of Command
	 * @param  array  $data The Data for the Command
	 * @return boolean 		True if successful
	 */
	protected function executeWHMCSCommand($type, array $data) {
		$user = $this->module->getSystemUsername();

		if(empty($user)) {
			throw new Exception('System user not defined');
		}

        $command = localAPI($type, $data, $user);

	 	if($command['result'] != 'success') {
	 		throw new Exception($command['message']);
	 	}

	 	return $command['result'] == 'success';
	}

	/**
	 * Creates an Event based up the webhook request data
	 * @param  array  $data 		The webhook request data
	 * @return ColoCrossing_Event   The Event
	 */
	public static function create(array $data)
	{
		//Verify event type is specified in request data
		if(empty($data['event']) || empty($data['event']['id']) || empty($data['event']['name'])) {
			throw new Exception('Unspecified event type');
		}

		//Parse Event Params
		$id = intval($data['event']['id']);
		$name = $data['event']['name'];

		//Restrict Data to Event Specific Fields
		unset($data['event'], $data['sent_at']);

		//Create Concrete Event Based on Id
		switch ($id) {
			case 1: //Ticket Created
				require_once 'events/TicketCreatedEvent.php';
				return new ColoCrossing_TicketCreatedEvent($id, $name, $data);
		}

		throw new Exception('Unrecognized event type');
	}

}