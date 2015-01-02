<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Devices Like Name</h1>

<?php

$options = array(
	'sort' => 'id'
);

$name = 'COLO-01'; //Enter your device name here
$devices = $colocrossing_client->devices->findLikeName($name, $options);

foreach ($devices as $key => $device)
{
	echo '<p>Device #' . $device->getId() . ' - ' . $device->getName() . '</p>';
}

?>
