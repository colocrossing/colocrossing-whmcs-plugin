<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Support Ticket</h1>

<?php

$ticket_id = 37607; //Enter your ticket id here
$ticket = $colocrossing_client->support_tickets->find($ticket_id);

if (isset($ticket))
{
	echo '<p>Id: ' . $ticket->getId() . '</p>';
	echo '<p>Subject: ' . $ticket->getSubject() . '</p>';

	$status = $ticket->getStatus();

	echo '<p>Status: #' . $status->getId() . ' - ' . $status->getName() . '</p>';

	$priority = $ticket->getPriority();

	echo '<p>Priority: #' . $priority->getId() . ' - ' . $priority->getName() . '</p>';

	$department = $ticket->getDepartment();

	if(isset($department))
	{
		echo '<p>Department: #' . $department->getId() . ' - ' . $department->getName() . '</p>';
	}
	else
	{
		echo '<p>Department: Unassigned</p>';
	}

	echo '<p>Service Request?: ' . ($ticket->isServiceRequest() ? 'Yes' : 'No') . '</p>';

	$assignee = $ticket->getAssignee();

	if(isset($assignee))
	{
		echo '<p>Assigned To: #' . $assignee->getId() . ' - ' . $assignee->getName() . '</p>';
	}
	else
	{
		echo '<p>Assigned To: Unassigned</p>';
	}

	echo '<p>Created At: ' . date('Y-m-d', $ticket->getDateCreated()) . '</p>';
	echo '<p>Updated At: ' . date('Y-m-d', $ticket->getDateUpdated()) . '</p>';

	$updated_by = $ticket->getUpdatedBy();

	if(isset($updated_by))
	{
		echo '<p>Updated By: #' . $updated_by->getId() . ' - ' . $updated_by->getName() . '</p>';
	}
	else
	{
		echo '<p>Updated By: Unknown</p>';
	}

	echo '<h2>Responses</h2>';

	$responses = $ticket->getResponses();

	foreach ($responses as $key => $response)
	{
		$user = $response->getUser();
		$created_at = date('c', $response->getDateCreated());

		echo '<h3>#' . $response->getId() . ' - ' . (isset($user) ? $user->getName() : 'Unknown') . ' - ' . $created_at . '</h3>';
		echo '<div style="width: 800px; white-space: pre-wrap;">' . $response->getMessage() . '</div>';
	}

	echo '<h2>Devices</h2>';

	$devices = $ticket->getDevices();

	foreach ($devices as $key => $device)
	{
		echo '<p>#' . $device->getId() . ' - ' . $device->getName() . '</p>';
	}
}

?>
