<?php

/**
 * Handles retrieving data from the API's subnet rDNS records sub-resource.
 * Also handles updating the rDNS records via the API.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 * @subpackage ColoCrossing_Resource_Child_Subnets
 */
class ColoCrossing_Resource_Child_Subnets_ReverseDNSRecords extends ColoCrossing_Resource_Child_Abstract
{

	/**
	 * @param ColoCrossing_Client $client The API Client
	 */
	public function __construct(ColoCrossing_Client $client)
	{
		parent::__construct($client->subnets, $client, 'rdns_record', '/rdns-records');
	}

	/**
	 * Updates a Reverse DNS Records in the provided.
	 * @param  int|ColoCrossing_Object_Subnet 					$subnet 	The Subnet or Subnet Id
	 * @param  int|ColoCrossing_Object_Subnet_ReverseDNSRecord  $record 	The Reverse DNS Record  or Id
	 * @param  string 											$value 		The new value of the record
	 * @return boolean|int 		True if successful, false otherwise. If a ticket to review the request
	 *                            	must be created, then the ticket id is returned.
	 */
	public function update($subnet, $record, $value)
	{
		$records = array();
		$records[] = array(
			'id' => is_object($record) ? $record->getId() : $record,
			'value' => $value
		);

		return $this->updateAll($subnet, $records);
	}

	/**
	 * Updates Multiple Reverse DNS Records in the provided Subnet all at once.
	 * @param  int|ColoCrossing_Object_Subnet 	$subnet 		The Subnet or Subnet Id
	 * @param  array<array(id, value)> 			$rdns_records  	List of Arrays that have an id attribute with the Id
	 *                                  						of the record and a value attribute with the value
	 *                                  				  		of the record.
	 * @return boolean|int 		True if successful, false otherwise. If a ticket to review the request
	 *                            	must be created, then the ticket id is returned.
	 */
	public function updateAll($subnet, array $records)
	{
		if(is_numeric($subnet))
		{
			$client = $this->getClient();
			$subnet = $client->subnets->find($subnet);
		}

		if (empty($subnet) || $subnet->isPendingServiceRequest()  || !$subnet->isReverseDnsEnabled())
		{
			return false;
		}

		$data = array('record' => array());
		foreach ($records as $key => $record) {
			$data['record'][$record['id']] = $record['value'];
		}

		$url = $this->createCollectionUrl($subnet->getId());

		$response = $this->sendRequest($url, 'PUT', $data);

		if (empty($response))
		{
			return false;
		}

		$content = $response->getContent();

		if (empty($content) || empty($content['status']) || $content['status'] == 'error')
		{
			return false;
		}

		if (isset($content['ticket']) && isset($content['ticket']['id']) && is_numeric($content['ticket']['id']) && $content['ticket']['id'] > 0)
		{
			return intval($content['ticket']['id']);
		}

		return true;
	}

}
