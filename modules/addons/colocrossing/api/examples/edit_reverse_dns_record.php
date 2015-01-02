<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Edit Reverse DNS Record</h1>

<?php

//Retrieve Subnet
$subnet_id = 715; //Enter your subnet id here
$subnet = $colocrossing_client->subnets->find($subnet_id);

if (isset($subnet) && $subnet->isReverseDnsEnabled())
{
	echo '<p>Subnet #' . $subnet->getId() . ' - ' . $subnet->getIpAddress() . '</p>';

	if (!$subnet->isPendingServiceRequest())
	{
		$rdns_record_id = 9473; //Enter you rDNS record id here
		$rdns_record = $subnet->getReverseDNSRecord($rdns_record_id);

		if (isset($rdns_record))
		{
			$record = 'server1.example.com'; // enter you new record here
			$result = $rdns_record->update($record);
			echo '<p>Success? ' . ($result ? 'Yes' : 'No') . '</p>';

			// Depending on client a ticket may need to be created to make rDNS update. If so, then the ticket
			// Id will be returned. Otherwise a boolean is returned to specify success.
			if (is_int($result))
			{
				echo '<p> Ticket #' . $result . ' was created.</p>';
			}
		}
	}
	else
	{
		echo '<p>Subnet is currently pending service request. Cannot Submit another rDNS modification request.</p>';
	}
}

?>
