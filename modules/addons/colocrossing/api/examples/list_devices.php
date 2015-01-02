<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Devices</h1>

<?php

$options = array(
	'sort' => 'id'
);
$devices = $colocrossing_client->devices->findAll($options);

foreach ($devices as $key => $device)
{
	echo '<p>Device #' . $device->getId() . ' - ' . $device->getName() . '</p>';
}

?>
