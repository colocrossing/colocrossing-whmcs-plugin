<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Announcement</h1>

<?php

$announcement_id = 1; //Enter your announcement id here
$announcement = $colocrossing_client->announcements->find($announcement_id);

if (isset($announcement))
{
	echo '<p>Id: ' . $announcement->getId() . '</p>';

	$type = $announcement->getType();

	echo '<p>Type: #' . $type->getId() . ' - ' . $type->getName() . '</p>';

	$severity = $announcement->getSeverity();

	echo '<p>Severity: #' . $severity->getId() . ' - ' . $severity->getName() . '</p>';

	echo '<p>Subject: ' . $announcement->getSubject() . '</p>';

	echo '<p>Message: ' . $announcement->getMessage() . '</p>';

	echo '<p>Created At: ' . date('Y-m-d', $announcement->getCreateDate()) . '</p>';

	$start_at = $announcement->getStartDate();
	$end_at = $announcement->getEndDate();

	if(isset($start_at) && isset($end_at))
	{
		echo '<p>Start At: ' . date('Y-m-d', $start_at) . '</p>';
		echo '<p>End At: ' . date('Y-m-d', $end_at) . '</p>';
	}

	echo '<h2>Devices</h2>';

	$devices = $announcement->getDevices();

	foreach ($devices as $key => $device)
	{
		echo '<p>#' . $device->getId() . ' - ' . $device->getName() . '</p>';
	}
}

?>
