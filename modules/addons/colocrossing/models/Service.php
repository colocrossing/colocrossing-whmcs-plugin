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
	protected static $COLUMNS = array('id', 'userid', 'packageid', 'domain', 'domainstatus');

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
	 * Returns the status of this service. Possible values include Pending, Active,
	 * Suspended, Terminated, Cancelled, or Fraud
	 * @return string The Status
	 */
	public function getStatus() {
		$status = $this->getValue('domainstatus');

		return empty($status) ? 'Pending' : $status;
	}

	/**
	 * Determines if Service is Active
	 * @return boolean True if service is active
	 */
	public function isActive() {
		$status = $this->getStatus();

		return isset($status) && $status == 'Active';
	}

	/**
	 * @return ColoCrossing_Model_Client|null The Client this Service is Assigned to
	 */
	public function getClient() {
		$client_id = $this->getValue('userid');

		return ColoCrossing_Model_Client::find($client_id);
	}

	/**
	 * @return integer The Client Id this Service is Assigned to
	 */
	public function getClientId() {
		$client_id = $this->getValue('userid');

		return empty($client_id) ? null : intval($client_id);
	}

	/**
	 * Determines if this Service is Assigned to the User
	 * @param  ColoCrossing_Object_User  $user The User
	 * @return boolean  True if the Service is Assigned to User
	 */
	public function isAssignedToUser($user) {
		if(empty($user) || $user->getType() != 2) {
			return false;
		}

		return $this->getClientId() == $user->getId();
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
	 * Determines if this Service is Assigned to the specified Device
	 * @param  integer|ColoCrossing_Object_Device  $device The Device or Id
	 * @return boolean  True if the Service is Assigned to Device
	 */
	public function isAssignedToDevice($device) {
		$device_id = is_numeric($device) ? $device : $device->getId();

		return $this->getDeviceId() == $device_id;
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

	/**
	 * Finds the Service from the DB that is assigned to the provided Device Id
	 * @param  integer $device_id The Device Id
	 * @return ColoCrossing_Model_Service|null The Service, Null if it is not found or device is unassigned.
	 * @static
	 */
	public static function findByDevice($device_id) {
		$columns = implode(',', static::$COLUMNS);
		$where = array('device_id' => $device_id);
		$join = '`' . static::$DEVICES_JOIN_TABLE . '` ON `' . static::$DEVICES_JOIN_TABLE . '`.`service_id` = `' . static::$TABLE . '`.`id`';

		$rows = select_query(static::$TABLE, $columns, $where, null, null, 1, $join);

		if(mysql_num_rows($rows) == 0) {
			return null;
		}

		$values = mysql_fetch_array($rows);

		return self::createInstanceFromRow($values);
	}

}
