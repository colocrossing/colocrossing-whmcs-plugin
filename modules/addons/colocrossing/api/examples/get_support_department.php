<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Support Department</h1>

<?php

$department_id = 2; //Enter your department id here
$department = $colocrossing_client->support_departments->find($department_id);

if (isset($department))
{
	echo '<p>Id: ' . $department->getId() . '</p>';
	echo '<p>Name: ' . $department->getName() . '</p>';

	echo '<h2>Tickets</h2>';

	$tickets = $department->getTickets();

	foreach ($tickets as $key => $ticket)
	{
		echo '<p>Ticket #' . $ticket->getId() . ' - ' . $ticket->getSubject() . '</p>';
	}
}

?>
