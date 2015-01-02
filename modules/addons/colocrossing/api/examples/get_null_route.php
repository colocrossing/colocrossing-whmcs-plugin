<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Null Route</h1>

<?php

$null_route_id = 64; //Enter your null route id here
$null_route = $colocrossing_client->null_routes->find($null_route_id);

if (isset($null_route))
{

	echo '<p>Id: ' . $null_route->getId() . '</p>';
	echo '<p>IP Address: ' . $null_route->getIpAddress() . '</p>';
	echo '<p>Comment: ' . $null_route->getComment() . '</p>';
	echo '<p>Expiration Date: ' . date('c', $null_route->getDateExpire()) . '</p>';
	echo '<p>Add Date: ' . date('c', $null_route->getDateAdded()) . '</p>';

	echo '<h2>Subnet</h2>';

	$subnet = $null_route->getSubnet();

	if (isset($subnet))
	{
		echo '<p>Id: ' . $subnet->getId() . '</p>';
		echo '<p>IP Address: ' . $subnet->getIpAddress() . '</p>';
		echo '<p>CIDR: ' . $subnet->getCidr() . '</p>';
	}
}

?>
