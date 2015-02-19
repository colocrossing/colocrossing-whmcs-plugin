<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Support Departments</h1>

<?php

$options = array(
	'sort' => 'name'
);
$departments = $colocrossing_client->support_departments->findAll($options);

foreach ($departments as $key => $department)
{
	echo '<p>Department #' . $department->getId() . ' - ' . $department->getName() . '</p>';
}

?>
