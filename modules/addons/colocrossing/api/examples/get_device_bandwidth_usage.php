<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<?php

$device_id = 30; //Enter your device id here
$device = $colocrossing_client->devices->find($device_id);


if(isset($device) && $device->getType()->isNetworkEndpoint())
{
	echo '<h2>Device Bandwidth Usages</h2>';

	echo '<p>Id: ' . $device->getId() . '</p>';
	echo '<p>Name: ' . $device->getName() . '</p>';
	echo '<p>Hostname: ' . $device->getHostname() . '</p>';

	$switches = $device->getSwitches();

	foreach ($switches as $i => $switch)
	{
		echo '<h3>Switch #' . $switch->getId() . ' - ' . $switch->getName() . '</h3>';

		$ports = $switch->getPorts();

		foreach ($ports as $j => $port)
		{
			if($port->isBandwidthUsageAvailable())
			{
				$bandwidth_usage = $port->getBandwidthUsage();

				if (isset($bandwidth_usage))
				{
					echo '<p>Port #' . $port->getId() . ' - ' . $port->getStatus() . '</p>';
					echo '<p>Total Usage: ' . $bandwidth_usage->getTotalUsage() . ' GB</p>';
					echo '<p>95th Usage: ' . $bandwidth_usage->getNinetyFifthUsage() . ' mbit</p>';
				}
			}
		}
	}
}

?>