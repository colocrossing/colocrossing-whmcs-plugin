<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Support Tickets</h1>

<?php

$options = array(
	'sort' => 'subject'
);
$tickets = $colocrossing_client->support_tickets->findAll($options);

foreach ($tickets as $key => $ticket)
{
	echo '<p>Ticket #' . $ticket->getId() . ' - ' . $ticket->getSubject() . '</p>';
}

?>
