<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Null Routes</h1>

<?php

$options = array(
	'sort' => '-date_expire'
);
$null_routes = $colocrossing_client->null_routes->findAll($options);

foreach ($null_routes as $key => $null_route)
{
	$expire_date = date('c', $null_route->getDateExpire());
	echo '<p>Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . ' - ' . $expire_date . '</p>';
}

?>
