<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Network</h1>

<?php

$network_id = 55; //Enter your network id here
$network = $colocrossing_client->networks->find($network_id);

if (isset($network))
{

	echo '<p>Id: ' . $network->getId() . '</p>';
	echo '<p>IP Address: ' . $network->getIpAddress() . '</p>';
	echo '<p>CIDR: ' . $network->getCidr() . '</p>';
	echo '<p>Total Number of IP Addresses: ' . $network->getNumberOfIpAddresses() . '</p>';

	echo '<h2>Subnets</h2>';

	$subnets = $network->getSubnets();

	foreach ($subnets as $key => $subnet)
	{
		echo '<p>Subnet #' . $subnet->getId() . ' - ' . $subnet->getIpAddress() . '</p>';
	}

	echo '<h2>Null Routes</h2>';

	$null_routes = $network->getNullRoutes();

	foreach ($null_routes as $key => $null_route)
	{
		$expire_date = date('c', $null_route->getDateExpire());
		echo '<p>Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . ' - ' . $expire_date . '</p>';
	}
}

?>
