<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>List Announcements</h1>

<?php

$options = array(
  'sort' => 'id'
);
$announcements = $colocrossing_client->announcements->findAll($options);

foreach ($announcements as $key => $announcement)
{
  echo '<p>Announcement #' . $announcement->getId() . ' - ' . $announcement->getSubject() . '</p>';
}

?>
