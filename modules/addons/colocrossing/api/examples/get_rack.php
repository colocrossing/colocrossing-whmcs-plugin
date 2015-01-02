<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Rack Device</h1>

<?php

$rack_id = 35; //Enter your rack id here
$rack = $colocrossing_client->devices->find($rack_id);

if (isset($rack))
{

	echo '<p>Id: ' . $rack->getId() . '</p>';
	echo '<p>Name: ' . $rack->getName() . '</p>';
	echo '<p>Hostname: ' . $rack->getHostname() . '</p>';
	echo '<p>Subzone: ' . $rack->getSubzone() . '</p>';

	echo '<h2>Type</h2>';

	$type = $rack->getType();

	echo '<p>Id: ' . $type->getId() . '</p>';
	echo '<p>Name: ' . $type->getName() . '</p>';

	if ($type->isRack())
	{
		echo '<h2>Devices</h2>';

		$devices = $rack->getDevices(array('sort' => 'id'));

		foreach ($devices as $key => $device)
		{
			echo '<p>Device #' . $device->getId() . ' - ' . $device->getName() . ' - U Space ' . $device->getUSpace() . '</p>';
		}
	}
	else
	{
		echo '<p>Device is not a Rack!</p>';
	}

}

?>
