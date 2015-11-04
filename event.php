<?php

//Add getallheaders() if Running on NGINX
if(!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if(substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }
}

//Get Request Headers
$headers = getallheaders();

//Verify Request Content Type is JSON
if($headers['Content-Type'] != 'application/json') {
	http_response_code(415);
	exit('Unsupported content type');
}

//Parse Request Data
$body = file_get_contents('php://input');
$data = json_decode($body, true);

//Verify Request Data Parse Properly
if(empty($data)) {
	http_response_code(400);
	exit('Unable to parse JSON');
}

//Verify Time in Acceptable Range
if(empty($data['sent_at']) || strtotime($data['sent_at']) < strtotime('-5 minutes')) {
	http_response_code(401);
	exit('Excessive time mismatch');
}

//Require WHMCS and ColoCrossing Module
require 'init.php';
require 'modules/addons/colocrossing/Module.php';

//Get ColoCrossing Module
$module = ColoCrossing_Module::getInstance();

//Get Secret From Config
$secret = $module->getAPIHookSecret();

//Verfiy Signature
if($headers['X-Signature'] != hash_hmac('sha1', $body, $secret)) {
	http_response_code(401);
	exit('Signature mismatch');
}

//Import Additional Functions
require_once 'includes/adminfunctions.php';

//Create Event
try {
	$event = ColoCrossing_Event::create($data);
} catch (Exception $e) {
	http_response_code(422);
	exit($e->getMessage());
}

//Execute Event
try {
	echo $event->execute() ? 'True' : 'False';
} catch (Exception $e) {
	http_response_code(500);
	exit($e->getMessage());
}
