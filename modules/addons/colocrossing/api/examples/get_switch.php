<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Switch Device</h1>

<?php

$switch_id = 38; //Enter your switch id here
$switch = $colocrossing_client->devices->find($switch_id);

if (isset($switch))
{

	echo '<p>Id: ' . $switch->getId() . '</p>';
	echo '<p>Name: ' . $switch->getName() . '</p>';
	echo '<p>Hostname: ' . $switch->getHostname() . '</p>';
	echo '<p>Subzone: ' . $switch->getSubzone() . '</p>';

	echo '<h2>Type</h2>';

	$type = $switch->getType();

	echo '<p>Id: ' . $type->getId() . '</p>';
	echo '<p>Name: ' . $type->getName() . '</p>';

	if ($type->isNetworkDistribution())
	{
		echo '<h2>Ports</h2>';

		$ports = $switch->getPorts();

		foreach ($ports as $key => $port)
		{
			echo '<p>Port #' . $port->getId() . ' - ' . $port->getStatus() . ' - ' . $port->getDescription() . '</p>';

			$device = $port->getDevice();

			if (isset($device))
			{
				echo '<p>Device #' . $device->getId() . ' - ' . $device->getName() . '</p>';
			}
		}
	}
	else
	{
		echo '<p>Device is not a Switch!</p>';
	}

}

?>
