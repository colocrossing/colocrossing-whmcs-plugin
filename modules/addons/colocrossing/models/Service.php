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
	protected static $COLUMNS = array('id', 'userid', 'packageid', 'domain', 'domainstatus', 'nextduedate', 'billingcycle', 'regdate');

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
	 * The Products Table Name
	 * @var string
	 */
	protected static $PRODUCTS_TABLE = 'tblproducts';

	/**
	 * A Class Wide Cache of Products
	 * @var array<integer, ColoCrossing_Model_Product>
	 */
	protected static $PRODUCTS = array();

	/**
	 * A Class Wide Cache of Clients
	 * @var array<integer, ColoCrossing_Model_Client>
	 */
	protected static $CLIENTS = array();

	/**
	 * @return string The Hostname of the Service
	 */
	public function getHostname() {
		$hostname = $this->getValue('domain');

		return empty($hostname) ? '' : $hostname;
	}

	/**
	 * @return string The Registration Date of the Service
	 */
	public function getRegistrationDate() {
		$date = strtotime($this->getValue('regdate'));

		return empty($date) ? time() : $date;
	}

	/**
	 * @return string The Next Due Date of the Service
	 */
	public function getNextDueDate() {
		$date = strtotime($this->getValue('nextduedate'));

		return empty($date) ? time() : $date;
	}

	/**
	 * @return string The Billing Cycle of the Service
	 */
	public function getBillingCycle() {
		$cycle = $this->getValue('billingcycle');

		return empty($cycle) ? 'Monthly' : $cycle;
	}

	/**
	 * @return string The Length of the Services Billing Cycle in text
	 */
	public function getBillingCycleLength() {
		$billing_cycle = $this->getBillingCycle();

		switch ($billing_cycle) {
			case 'Quarterly':
				return '3 months';
			case 'Semi-Annually':
				return '6 months';
			case 'Annually':
				return '1 year';
			case 'Biennially':
				return '2 years';
			case 'Triennially':
				return '3 years';
		}

		return '1 month';
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
		$id = $this->getClientId();

		if(empty($id)) {
			return null;
		}

		if(isset(self::$CLIENTS[$id])) {
			return self::$CLIENTS[$id];
		}

		return self::$CLIENTS[$id] = ColoCrossing_Model_Client::find($id);
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
		$id = $this->getValue('packageid');

		if(empty($id)) {
			return null;
		}

		if(isset(self::$PRODUCTS[$id])) {
			return self::$PRODUCTS[$id];
		}

		return self::$PRODUCTS[$id] = ColoCrossing_Model_Product::find($id);
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

		insert_query(self::$DEVICES_JOIN_TABLE, array(
			'service_id' => $this->getId(),
			'device_id' => $device_id
		));

		return true;
	}

	/**
	 * Unassigns This Service From Any Devices
	 */
	public function unassignFromDevice() {
		full_query('DELETE FROM `' . self::$DEVICES_JOIN_TABLE . '` WHERE `service_id` = '. $this->getId());
	}

	/**
	 * Retrieves a collection of Services that are assigned to a Device
	 * @param  array|null $options Optional options to modify the results. I.e. filters, sort, order, pagination
	 * @return array<ColoCrossing_Model_Service> The Services
	 * @static
	 */
	public static function findAllAssignedToDevices(array $options = array()) {
		$options['join'] = '`' . self::$DEVICES_JOIN_TABLE . '` ON `' . self::$DEVICES_JOIN_TABLE . '`.`service_id` = `' . self::$TABLE . '`.`id`';
		$options['filters'] = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : array();
		$options['filters']['device_id'] = array('sqltype' => 'NEQ', 'value' => '0');

		return self::findAll($options);
	}

	/**
	 * Retrieves a collection of Services that are unassigned
	 * @param  array|null $options Optional options to modify the results. I.e. filters, sort, order, pagination
	 * @return array<ColoCrossing_Model_Service> The Services
	 * @static
	 */
	public static function findAllUnassigned(array $options = array()) {
		$table = '`' . static::$TABLE . '`';

		$columns = array();
		foreach (static::$COLUMNS as $i => $column) {
			$columns[] = '`s`.`' . $column . '`';
		}
		$columns = implode(',', $columns);

		$products_join = ' INNER JOIN `' . self::$PRODUCTS_TABLE . '` AS `p` ON `p`.`id` = `s`.`packageid` ';
		$devices_join = ' LEFT OUTER JOIN `' . self::$DEVICES_JOIN_TABLE . '` AS `d` ON `d`.`service_id` = `s`.`id` ';

		$conditions = array();
		$conditions[] = '`p`.`servertype` = "colocrossing"';
		$conditions[] = '`d`.`device_id` IS NULL OR `d`.`device_id` <= 0';
		$conditions[] = '`s`.`domainstatus` = "Pending" OR `s`.`domainstatus` = "Active"';

		$where = ' WHERE  (' . implode (') AND (', $conditions) . ') ';

		$limit = '';
		if(isset($options['pagination']) && is_array($options['pagination'])) {
			$pagination = array_merge(array('number' => 1, 'size' => 30), $options['pagination']);
			$size = min(max($pagination['size'], 1), 100);
			$offset = (max($pagination['number'], 1) - 1) * $size;
			$limit = ' LIMIT ' . $offset . ', ' . $size;
		}

		$query = 'SELECT ' . $columns .' FROM ' . $table . ' AS `s` ' . $products_join . $devices_join . $where . $limit;
		$rows = full_query($query);

		$services = array();

		while ($values = mysql_fetch_array($rows)) {
		    $services[] = self::createInstanceFromRow($values);
		}

		return $services;
	}

	/**
	 * Gets the Count of Services that are Unassigned
	 * @return integer The Num of Unassigned Services
	 */
	public static function getTotalUnassigned() {
		$table = '`' . static::$TABLE . '`';

		$columns = 'COUNT(*) as `total_count`';

		$products_join = ' INNER JOIN `' . self::$PRODUCTS_TABLE . '` AS `p` ON `p`.`id` = `s`.`packageid` ';
		$devices_join = ' LEFT OUTER JOIN `' . self::$DEVICES_JOIN_TABLE . '` AS `d` ON `d`.`service_id` = `s`.`id` ';

		$where = ' WHERE `p`.`servertype` = "colocrossing" AND (`d`.`device_id` IS NULL || `d`.`device_id` <= 0) ';

		$query = 'SELECT ' . $columns .' FROM ' . $table . ' AS `s` ' . $products_join . $devices_join . $where;
		$result = full_query($query);

		$data = mysql_fetch_array($result);

		return isset($data) && isset($data['total_count']) ? intval($data['total_count']) : 0;
	}

	/**
	 * Finds the Service from the DB that is assigned to the provided Device Id
	 * @param  integer|ColoCrossing_Object_Device $device The Device or Id
	 * @return ColoCrossing_Model_Service|null The Service, Null if it is not found or device is unassigned.
	 * @static
	 */
	public static function findByDevice($device) {
		$device_id = is_numeric($device) ? intval($device) : $device->getId();

		$columns = implode(',', self::$COLUMNS);
		$where = array('device_id' => $device_id);
		$join = '`' . self::$DEVICES_JOIN_TABLE . '` ON `' . self::$DEVICES_JOIN_TABLE . '`.`service_id` = `' . self::$TABLE . '`.`id`';

		$rows = select_query(self::$TABLE, $columns, $where, null, null, 1, $join);

		if(mysql_num_rows($rows) == 0) {
			return null;
		}

		$values = mysql_fetch_array($rows);

		return self::createInstanceFromRow($values);
	}

	/**
	 * Retrieves a Device Id to Service Id Mapping
	 * @return array<int, int> The Mapping of Devices to Services
	 * @static
	 */
	public static function getAssignedDevices() {
		$table = '`' . self::$DEVICES_JOIN_TABLE . '`';
		$columns = '`d`.`device_id, `d`.`service_id';
		$join = '`' . self::$TABLE . '` AS `s` ON `s`.`id` = `d`.`service_id` ';

		$rows = select_query($table, $columns, null, null, null, null, $join);

		$devices_services = array();

		while ($data = mysql_fetch_array($rows)) {
			$devices_services[intval($data['device_id'])] = intval($data['service_id']);
		}

		return $devices_services;
	}

}
