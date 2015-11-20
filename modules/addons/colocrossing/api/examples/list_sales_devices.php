<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('9d7f7540aa8eb463114be5d629159f23dc1348ba');

?>

<h1>List Available Devices in Sales</h1>

<?php

$options = array(
    'sort' => 'id'
);
$devices = $colocrossing_client->sales_devices->findAll($options);

foreach ($devices as $key => $device)
{
    echo '<p>Device #' . $device->getId() . ' - ' . $device->getName(). ' - ' . $device->getDescription() . '</p>';
}

?>
