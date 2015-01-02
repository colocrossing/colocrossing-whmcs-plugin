<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Device</h1>

<?php

$device_id = 18; //Enter your device id or exact name here
$device = $colocrossing_client->devices->find($device_id);

if (isset($device))
{

	echo '<p>Id: ' . $device->getId() . '</p>';
	echo '<p>Name: ' . $device->getName() . '</p>';
	echo '<p>Hostname: ' . $device->getHostname() . '</p>';
	echo '<p>Subzone: ' . $device->getSubzone() . '</p>';

	echo '<h2>Type</h2>';

	$type = $device->getType();

	echo '<p>Id: ' . $type->getId() . '</p>';
	echo '<p>Name: ' . $type->getName() . '</p>';

	echo '<h2>Subnets</h2>';

	if ($type->isNetworked())
	{
		$subnets = $device->getSubnets();

		foreach ($subnets as $i => $subnet)
		{
			echo '<p>Subnet #' . $subnet->getId() . ' - ' . $subnet->getIpAddress() . '</p>';
		}
	}
	else
	{
		echo '<p>Device is not Networked.</p>';
	}

	//Verify Device has Switches
	if ($type->isNetworkEndpoint())
	{
		echo '<h2>Switches</h2>';

		$switches = $device->getSwitches();

		foreach ($switches as $i => $switch)
		{
			echo '<p>Switch #' . $switch->getId() . ' - ' . $switch->getName() . '</p>';

			$ports = $switch->getPorts();

			foreach ($ports as $j => $port)
			{
				echo '<p>--- Port #' . $port->getId() . ' - ' . $port->getStatus() . '</p>';
			}
		}
	}

	//Verify Device has PDUs
	if ($type->isPowerEndpoint())
	{
		echo '<h2>PDUs</h2>';

		$pdus = $device->getPowerDistributionUnits();

		foreach ($pdus as $i => $pdu)
		{
			echo '<p>PDU #' . $pdu->getId() . ' - ' . $pdu->getName() . '</p>';

			$ports = $pdu->getPorts();

			foreach ($ports as $j => $port)
			{
				echo '<p>--- Port #' . $port->getId() . ' - ' . $port->getStatus() . '</p>';
			}
		}
	}

	echo '<h2>Assets</h2>';

	$assets = $device->getAssets();

	foreach ($assets as $i => $asset)
	{
		echo '<p>Asset #' . $asset->getId() . ' - ' . $asset->getName() . '</p>';

		$groups = $asset->getGroups();

		foreach ($groups as $j => $group)
		{
			echo '<p>Belongs to Group #' . $group->getId() . ' - ' . $group->getName() . '</p>';
		}
	}

	echo '<h2>Notes</h2>';

	$notes = $device->getNotes();

	foreach ($notes as $i => $note)
	{
		echo '<p>Note #' . $note->getId() . ' - ' . $note->getNote() . '</p>';
	}
}

?>
