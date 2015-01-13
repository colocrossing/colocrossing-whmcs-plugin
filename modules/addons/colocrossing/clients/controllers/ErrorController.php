<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Error Controller for WHMCS Client Module.
 * Handles Responding to all Requests that have Errors
 */
class ColoCrossing_Clients_ErrorController extends ColoCrossing_Clients_Controller {

	public function generic(array $params) {
		$this->message = $params['message'];

		return 'Error';
	}

	public function missing(array $params) {
		$this->message = $params['message'];

		return 'Not Found';
	}

	public function authentication(array $params) {
		$this->message = $params['message'];

		return 'Unauthorized';
	}

}
