<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Remove Null Routes</h1>

<?php

//There Multiple Ways to Find a Null Route

//Find By Id
$null_route_id = 27; //Enter your null route id here
$null_route = $colocrossing_client->null_routes->find($null_route_id);

if (isset($null_route))
{
	echo '<p>Removing Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . '</p>';
	echo '<p>Success? ' . ($null_route->remove() ? 'Yes' : 'No') . '</p>';
}

//Find By Ip Address
$ip_address = '1.1.2.2'; //Enter your null route ip address here
$null_routes = $colocrossing_client->null_routes->findAllByIpAddress($ip_address);

foreach ($null_routes as $key => $null_route)
{
	echo '<p>Removing Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . '</p>';
	echo '<p>Success? ' . ($null_route->remove() ? 'Yes' : 'No') . '</p>';
}

//Retrieve Subnet
$subnet_id = 715; //Enter your subnet id here
$subnet = $colocrossing_client->subnets->find($subnet_id);

//Find By Id Through Subnet
$null_route_id = 30; //Enter your null route id here
$null_route = $subnet->getNullRoute($null_route_id);

if (isset($null_route))
{
	echo '<p>Removing Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . '</p>';
	echo '<p>Success? ' . ($null_route->remove() ? 'Yes' : 'No') . '</p>';
}

//Find By Ip Address Through Subnet
$ip_address = '1.1.2.3'; //Enter your null route ip address here
$null_routes = $subnet->getNullRoutesByIpAddress($ip_address);

foreach ($null_routes as $key => $null_route)
{
	echo '<p>Removing Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . '</p>';
	echo '<p>Success? ' . ($null_route->remove() ? 'Yes' : 'No') . '</p>';
}

?>
