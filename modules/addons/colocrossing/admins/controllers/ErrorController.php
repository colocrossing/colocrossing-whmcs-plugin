<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Error Controller for WHMCS Admin Module.
 * Handles Responding to all Requests that have Errors
 */
class ColoCrossing_Admins_ErrorController extends ColoCrossing_Admins_Controller {

	public function generic(array $params) {
		$this->message = $params['message'];
	}

	public function missing(array $params) {
		$this->message = $params['message'];
	}

	public function authentication(array $params) {
		$this->message = $params['message'];
	}

}
