<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Support Response</h1>

<?php

$response_id = 300549; //Enter your response id here
$response = $colocrossing_client->support_responses->find($response_id);

if (isset($response))
{
	echo '<p>Id: ' . $response->getId() . '</p>';
	echo '<p>Message:</p>';
	echo '<div style="width: 800px; white-space: pre-wrap;">' . $response->getMessage() . '</div>';
	echo '<p>Created At: ' . date('Y-m-d', $response->getDateCreated()) . '</p>';


	$ticket = $response->getTicket();

	if(isset($ticket))
	{
		echo '<p>Ticket: #' . $ticket->getId() . ' - ' . $ticket->getSubject() . '</p>';
	}

	$user = $response->getUser();

	if(isset($user))
	{
		echo '<p>User: #' . $user->getId() . ' - ' . $user->getName() . ' - ' . ucfirst($user->getType()) . '</p>';
	}

}

?>
