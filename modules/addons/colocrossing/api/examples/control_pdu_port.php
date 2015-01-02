<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Control a PDU Port</h1>

<?php

$pdu_id = 37; //Enter your pdu id here
$pdu = $colocrossing_client->devices->find($pdu_id);

if (isset($pdu) && $pdu->getType()->isPowerDistribution())
{
	$port_id = 2; //Enter your port id here
	$port = $pdu->getPort($port_id);

	if (isset($port) && $port->isControllable())
	{
		$success = $port->turnOn();
		//$success = $port->turnOff();
		//$success = $port->restart();

		echo '<p>Success? ' . ($success ? 'Yes' : 'No') . '</p>';
	}
}

?>
