<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A WHMCS Client Service
 */
class ColoCrossing_Model_Service extends ColoCrossing_Model {

	/**
	 * The Service Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'userid', 'packageid', 'domain');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblhosting';

	/**
	 * The Device Join Table Name
	 * @var string
	 */
	protected static $DEVICES_JOIN_TABLE = 'mod_colocrossing_devices_services';

	/**
	 * @return string The Domain of the Service
	 */
	public function getDomain() {
		$domain = $this->getValue('domain');

		return empty($domain) ? '' : $domain;
	}

	/**
	 * @return ColoCrossing_Model_Client|null The Client this Service is Assigned to
	 */
	public function getClient() {
		$client_id = $this->getValue('userid');

		return ColoCrossing_Model_Client::find($client_id);
	}

	/**
	 * @return ColoCrossing_Model_Product|null The Product this Service was created from
	 */
	public function getProduct() {
		$product_id = $this->getValue('packageid');

		return ColoCrossing_Model_Product::find($product_id);
	}

	/**
	 * @return intger|null The Device ID assigned to the Service, Null if unassigned
	 */
	public function getDeviceId() {
		$device_id = $this->getValue('device_id');

		if(empty($device_id)) {
			$id = $this->getId();
			$result = select_query(static::$DEVICES_JOIN_TABLE, 'device_id', array('service_id' => $id));
			$data = mysql_fetch_array($result);

			if(empty($data)) {
				return null;
			}

			$device_id = intval($data['device_id']);
			$this->setValue('device_id', $device_id);
		}

		return $device_id;
	}

	/**
	 * @return ColoCrossing_Object_Device|null The Device, Null if unassigned
	 */
	public function getDevice() {
		$device_id = $this->getDeviceId();

		if(empty($device_id)) {
			return null;
		}

		return $this->api->devices->find($device_id);
	}

	/**
	 * Unassigns this service from any devices and then assigns it to the device id specified
	 * @param  integer $device_id The Device
	 * @return boolean True if the service is successfully assigned to a device.
	 */
	public function assignToDevice($device_id) {
		$this->unassignFromDevice();

		if(empty($device_id) || $device_id <= 0) {
			return false;
		}

		insert_query(static::$DEVICES_JOIN_TABLE, array(
			'service_id' => $this->getId(),
			'device_id' => $device_id
		));

		return true;
	}

	/**
	 * Unassigns This Service From Any Devices
	 */
	public function unassignFromDevice() {
		full_query('DELETE FROM `' . static::$DEVICES_JOIN_TABLE . '` WHERE `service_id` = '. $this->getId());
	}

	/**
	 * Retrieves a collection of Services that are assigned to a Device
	 * @param  array|null $options Optional options to modify the results. I.e. filters, sort, order, pagination
	 * @return array<ColoCrossing_Model_Service> The Services
	 * @static
	 */
	public static function findAllAssignedToDevices(array $options = array()) {
		$options['join'] = '`' . static::$DEVICES_JOIN_TABLE . '` ON `' . static::$DEVICES_JOIN_TABLE . '`.`service_id` = `' . static::$TABLE . '`.`id`';
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['device_id'] = array('sqltype' => 'NEQ', 'value' => '0');

		return self::findAll($options);
	}

}
