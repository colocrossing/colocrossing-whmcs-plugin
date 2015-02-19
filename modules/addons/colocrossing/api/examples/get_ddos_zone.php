<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>DDoS Zone</h1>

<?php

$zone_id = 2; //Enter your zone id here
$zone = $colocrossing_client->ddos_zones->find($zone_id);

if (isset($zone))
{
	echo '<p>Id: ' . $zone->getId() . '</p>';
	echo '<p>Name: ' . $zone->getName() . '</p>';
	echo '<p>Subzone: ' . $zone->getSubzone() . '</p>';

	$owner = $zone->getOwner();

	echo '<p>Owner: #' . $owner->getId() . ' - ' . $owner->getName() . '</p>';

	$traffic = $zone->getTraffic();

	if(isset($traffic))
	{
		echo '<h2>Traffic</h2>';

		echo '<p>Packets Incoming: ' . $traffic->getPacketsIn() . ' packet/s</p>';
		echo '<p>Packets Outcoming: ' . $traffic->getPacketsOut() . ' packet/s</p>';
		echo '<p>Attack Packets: ' . $traffic->getPacketsAttack() . ' packet/s</p>';

		echo '<p>Bits Incoming: ' . $traffic->getBitsIn() . ' bit/s</p>';
		echo '<p>Bits Outcoming: ' . $traffic->getBitsOut() . ' bit/s</p>';
		echo '<p>Attack Bits: ' . $traffic->getBitsAttack() . ' bit/s</p>';
	}

	echo '<h2>Attacks</h2>';

	$attacks = $zone->getAttacks();

	foreach ($attacks as $key => $attack)
	{
		echo '<p>Attack: #' . $attack->getId() . ' - ' . $attack->getIpAddress() . '</p>';
		echo '<p>Size: ' . intval($attack->getAverageBitRate()) . 'bits/s</p>';

		$type = $attack->getType();

		echo '<p>Type: #' . $type->getId() . ' - ' . $type->getName() . '</p>';
	}
}

?>
