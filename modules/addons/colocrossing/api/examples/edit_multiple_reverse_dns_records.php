<?php

require_once('../src/ColoCrossing.php');

$colocrossing_client = new ColoCrossing_Client();
$colocrossing_client->setAPIToken('YOUR_API_TOKEN');

?>

<h1>Edit Multiple Reverse DNS Record</h1>

<?php

//Retrieve Subnet
$subnet_id = 715; //Enter your subnet id here
$subnet = $colocrossing_client->subnets->find($subnet_id);

if (isset($subnet) && $subnet->isReverseDnsEnabled())
{
	echo '<p>Subnet #' . $subnet->getId() . ' - ' . $subnet->getIpAddress() . '</p>';

	if (!$subnet->isPendingServiceRequest())
	{

		$rdns_records = $subnet->getReverseDNSRecords();
		$records_updates = array();

		foreach ($rdns_records as $key => $rdns_record)
		{
			$value = str_replace('.', '-', $rdns_record->getIpAddress()) . '-host.colocrossing.com';
			$records_updates[] = array(
				'id' => $rdns_record->getId(),
				'value' => $value
			);
		}

		$result = $subnet->updateReverseDNSRecords($records_updates);

		echo '<p>Success? ' . ($result ? 'Yes' : 'No') . '</p>';

		// Depending on client a ticket may need to be created to make rDNS update. If so, then the ticket
		// Id will be returned. Otherwise a boolean is returned to specify success.
		if (is_int($result))
		{
			echo '<p> Ticket #' . $result . ' was created.</p>';
		}
	}
	else
	{
		echo '<p>Subnet is currently pending service request. Cannot submit another rDNS modification request.</p>';
	}
}

?>
