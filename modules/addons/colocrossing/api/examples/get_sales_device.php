<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('9d7f7540aa8eb463114be5d629159f23dc1348ba');

?>

<h1>Sales Device</h1>

<?php

$device_id = 10215; //Enter your device id or exact name here
$device = $colocrossing_client->sales_devices->find($device_id);

if (isset($device))
{

    echo '<p>Id: ' . $device->getId() . '</p>';
    echo '<p>Name: ' . $device->getName() . '</p>';
    echo '<p>Description: ' . $device->getDescription() . '</p>';
    echo '<p>Subzone: ' . $device->getSubzone() . '</p>';

    echo '<h2>Type</h2>';

    $type = $device->getType();

    echo '<p>Id: ' . $type->getId() . '</p>';
    echo '<p>Name: ' . $type->getName() . '</p>';

    echo '<h2>Assets</h2>';

    $assets = $device->getAssets();

    foreach ($assets as $i => $asset)
    {
        echo '<p>Asset #' . $asset->getId() . ' - ' . $asset->getName() . '</p>';

        $groups = $asset->getGroups();

        foreach ($groups as $j => $group)
        {
            echo '<p>Belongs to Group #' . $group->getId() . ' - ' . $group->getName() . '</p>';
        }
    }

}

?>
