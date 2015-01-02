<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Subnets</h1>

<?php

$options = array(
	'sort' => 'ip_address'
);
$networks = $colocrossing_client->networks->findAll($options);

foreach ($networks as $key => $network)
{
	echo '<p>Network #' . $network->getId() . ' - ' . $network->getIpAddress() . '</p>';
}

?>
