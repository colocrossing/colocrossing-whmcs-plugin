<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List DDoS Zones</h1>

<?php

if($colocrossing_client->hasPermission('ddos_protection'))
{

	$options = array(
		'sort' => 'name'
	);
	$zones = $colocrossing_client->ddos_zones->findAll($options);

	foreach ($zones as $key => $zone)
	{
		echo '<p>Zone #' . $zone->getId() . ' - ' . $zone->getName() . ' - ' . $zone->getSubzone() . '</p>';
	}
}
else
{
	echo '<p>DDoS protection is not enabled!</p>';
}

?>
