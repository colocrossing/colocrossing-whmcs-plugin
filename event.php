<?php

$headers = getallheaders();

if($headers['Content-Type'] != 'application/json') {
	http_response_code(415);
	exit('Unsupported content type');
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if(empty($data)) {
	http_response_code(400);
	exit('Unable to parse JSON');
}

if(empty($data['sent_at']) || strtotime($data['sent_at']) < strtotime('-5 minutes')) {
	http_response_code(401);
	exit('Excessive time mismatch');
}

$secret = 'FDG%$YHB%#$VVB%&^$TY';
$signature = hash_hmac('sha1', $body, $secret);

if($headers['X-Signature'] != $signature) {
	http_response_code(401);
	exit('Signature mismatch');
}

switch ($data['event']['id']) {
	case 1:
		echo '#' . $data['ticket']['id'] . ' - ' . $data['ticket']['subject'];
		break;
}
