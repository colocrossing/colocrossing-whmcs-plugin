<?php

/**
 * ColoCrossing Events Controller for WHMCS Module.
 * Handles Responding to all Requests Related to Events
 */
class ColoCrossing_EventsController extends ColoCrossing_Controller {

	public function index(array $params) {
		$this->events = ColoCrossing_Event::findAll();
	}

}
