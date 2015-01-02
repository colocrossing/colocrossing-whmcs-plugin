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
$subnets = $colocrossing_client->subnets->findAll($options);

foreach ($subnets as $key => $subnet)
{
	echo '<p>Subnet #' . $subnet->getId() . ' - ' . $subnet->getIpAddress() . '</p>';
}

?>
