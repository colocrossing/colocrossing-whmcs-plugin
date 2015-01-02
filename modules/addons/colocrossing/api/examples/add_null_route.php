<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Add Null Route</h1>

<?php

//Retrieve Subnet
$subnet_id = 715; //Enter your subnet id here
$subnet = $colocrossing_client->subnets->find($subnet_id);

if (isset($subnet))
{
	echo '<p>Subnet #' . $subnet->getId() . ' - ' . $subnet->getIpAddress() . '</p>';

	$ip_address = '1.1.2.3'; //Enter your ip address here
	$comment = 'Example Null Route'; //Enter your comment here
	$expire_date = strtotime('+1 day'); //Enter your expiration date here. This defaults to 4hrs if none provided.

	$null_route = $subnet->addNullRoute($ip_address, $comment, $expire_date);

	if (isset($null_route) && $null_route)
	{
		echo '<p>Added Null Route #' . $null_route->getId() . ' - ' . $null_route->getIpAddress() . '</p>';
	}
	else
	{
		echo '<p>Adding Null Route Failed!</p>';
	}
}

?>
