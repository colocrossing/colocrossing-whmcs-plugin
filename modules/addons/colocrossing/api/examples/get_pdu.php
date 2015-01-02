<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>PDU Device</h1>

<?php

$pdu_id = 37; //Enter your pdu id here
$pdu = $colocrossing_client->devices->find($pdu_id);

if (isset($pdu))
{

	echo '<p>Id: ' . $pdu->getId() . '</p>';
	echo '<p>Name: ' . $pdu->getName() . '</p>';
	echo '<p>Hostname: ' . $pdu->getHostname() . '</p>';
	echo '<p>Subzone: ' . $pdu->getSubzone() . '</p>';

	echo '<h2>Type</h2>';

	$type = $pdu->getType();

	echo '<p>Id: ' . $type->getId() . '</p>';
	echo '<p>Name: ' . $type->getName() . '</p>';

	if ($type->isPowerDistribution())
	{
		echo '<h2>Ports</h2>';

		$ports = $pdu->getPorts();

		foreach ($ports as $key => $port)
		{
			echo '<p>Port #' . $port->getId() . ' - ' . $port->getStatus() . '</p>';

			$device = $port->getDevice();

			if (isset($device))
			{
				echo '<p>Device #' . $device->getId() . ' - ' . $device->getName() . '</p>';
			}
		}
	}
	else
	{
		echo '<p>Device is not a PDU!</p>';
	}

}

?>
