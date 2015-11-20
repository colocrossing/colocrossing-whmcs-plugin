<?php

/**
 * Handles retrieving data from the API's Sales Devices resource
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Resource
 */
class ColoCrossing_Resource_SalesDevices extends ColoCrossing_Resource_Abstract
{

    /**
     * @param ColoCrossing_Client $client The API Client
     */
    public function __construct(ColoCrossing_Client $client)
    {
        parent::__construct($client, 'sales_device', '/sales/devices', 'device');
    }

    /**
     * Retrieve a Collection of Devices of to the provided type.
     * @param  integer  $type_id  The type id to filter by
     * @param  array    $options        The Options for the page and sort.
     * @return array|ColoCrossing_Collection<ColoCrossing_Object_SalesDevice> The Devices
     */
    public function findByType($type_id, array $options = null)
    {
        $options = isset($options) && is_array($options) ? $options : array();
        $options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
        $options['filters']['type'] = $type_id;

        return $this->findAll($options);
    }


}
