<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Subnet</h1>

<?php

$subnet_id = 715; //Enter your subnet id here
$subnet = $colocrossing_client->subnets->find($subnet_id);

if (isset($subnet))
{

	echo '<p>Id: ' . $subnet->getId() . '</p>';
	echo '<p>IP Address: ' . $subnet->getIpAddress() . '</p>';
	echo '<p>CIDR: ' . $subnet->getCidr() . '</p>';
	echo '<p>Total Number of IP Addresses: ' . $subnet->getNumberOfIpAddresses() . '</p>';
	echo '<p>IP Addresses: ' . implode(', ', $subnet->getIpAddresses()) . '</p>';

	echo '<h2>Network</h2>';

	$network = $subnet->getNetwork();

	if (isset($network))
	{
		echo '<p>Id: ' . $network->getId() . '</p>';
		echo '<p>IP Address: ' . $network->getIpAddress() . '</p>';
		echo '<p>CIDR: ' . $network->getCidr() . '</p>';
		echo '<p>Type: ' . $network->getType() . '</p>';
	}

	echo '<h2>Device</h2>';

	$device = $subnet->getDevice();

	if (isset($device))
	{
		echo '<p>Id: ' . $device->getId() . '</p>';
		echo '<p>Name: ' . $device->getName() . '</p>';
	}

	if ($subnet->isReverseDnsEnabled())
	{
		echo '<h2>rDNS Records</h2>';

		$options = array(
			'sort' => 'record'
		);
		$rdns_records = $subnet->getReverseDNSRecords($options);

		foreach ($rdns_records as $key => $rdns_record) {
			echo '<p>Record #' . $rdns_record->getId() . ' - ' . $rdns_record->getIpAddress() . ' - ' . $rdns_record->getRecord() . '</p>';
		}

	}

	echo '<h2>Null Routes</h2>';

	$null_routes = $subnet->getNullRoutes();

	foreach ($null_routes as $key => $null_route)
	{
		$expire_date = date('c', $null_route->getDateExpire());
		echo '<p>Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . ' - ' . $expire_date . '</p>';
	}
}

?>
