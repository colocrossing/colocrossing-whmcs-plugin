<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * ColoCrossing Devices Controller for WHMCS Client Module.
 * Handles Responding to all Requests Related to Devices
 */
class ColoCrossing_Clients_DevicesController extends ColoCrossing_Clients_Controller {

	public function view(array $params) {
		return 'Device';
	}

}
