<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Support Responses</h1>

<?php

$options = array(
	'sort' => 'name'
);
$responses = $colocrossing_client->support_responses->findAll($options);

foreach ($responses as $key => $response)
{
	echo '<h2>Response #' . $response->getId() . '</h2>';
	echo '<div style="width: 800px; white-space: pre-wrap;">' . $response->getMessage() . '</div>';
}

?>
