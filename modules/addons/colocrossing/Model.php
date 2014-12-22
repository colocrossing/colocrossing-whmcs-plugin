<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require 'models/Event.php';
require 'models/User.php';
require 'models/Admin.php';
require 'models/Client.php';
require 'models/Service.php';
require 'models/Product.php';

/**
 * A Simple Model Implementation to interact with rows of a DB table
 */
abstract class ColoCrossing_Model {

	/**
	 * Columns in the Associated Table
	 * @var array<string>
	 * @static
	 */
	protected static $COLUMNS;

	/**
	 * Table Name
	 * @var string
	 * @static
	 */
	protected static $TABLE;

	/**
	 * The Id of this Models Row
	 * @var integer
	 */
	protected $id;

	/**
	 * The Values this Model Holds
	 * @var array<string|integer|float|boolean>
	 */
	protected $values;

	/**
     * The Module
     * @var ColoCrossing_Module
     */
    protected $module;

    /**
     * The API Client
     * @var ColoCrossing_API
     */
    protected $api;

	/**
	 * Constructs a model with the provided Values and Id
	 * @param integer  	$id        The Id, Can be null if this model is not persisted
	 * @param array   	$values    The Values
	 */
	protected function __construct($id = null, array $values) {
        $this->module = ColoCrossing_Module::getInstance();
        $this->api = ColoCrossing_API::getInstance();

		$this->id = isset($id) ? intval($id) : null;
		$this->values = $values;
	}

	/**
	 * The Id of this Model. Returns Null if this model is not yet persisted.
	 * @return integer|null The Id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns the Values that are columns in the table associated with this model
	 * @return array The Values
	 */
	public function getValues() {
		$values = array();

		foreach (static::$COLUMNS as $index => $column) {
			if(isset($this->values[$column])) {
				$values[$column] = $this->values[$column];
			}
		}

		return $values;
	}

	/**
	 * @param  string $key 	The key to one the this object's values
	 * @return mixed      	The value corresponding to the key if present, false otherwise.
	 */
	public function getValue($key)
	{
		return isset($this->values[$key]) ? $this->values[$key] : false;
	}

	/**
	 * Sets the value of the local object. Does Not Update anything in the API.
	 * @param string 	$key   The key to one the this object's values.
	 * @param mixed 	$value The new value.
	 */
	public function setValue($key, $value)
	{
		return $this->values[$key] = $value;
	}

	/**
	 * Determines if this model has been save to the DB and has an ID
	 * @return boolean True if the model has been inserted
	 */
	public function isPersisted() {
		return isset($this->id);
	}

	/**
	 * Determines if the model's current values are valid and can be saved to the DB.
	 * If this returns false the model will fail to save to the DB
	 * Can be overridden to implement model validations
	 * @return boolean True if the model is valid
	 */
	public function isValid() {
		return true;
	}

	/**
	 * Saves the model to the DB
	 * @return boolean True if the model is successfully saved
	 */
	public function save() {
		if(!$this->isValid()) {
			return false;
		}

		if(!$this->isPersisted()) {
			return $this->insert();
		}

		return $this->update();
	}

	/**
	 * Destroy This Model
	 */
	public function destroy() {
		full_query('DELETE FROM `' . static::$TABLE . '` WHERE `id` = '. $this->getId());
	}

	/**
	 * Inserts Db Record for this Model
	 * @return boolean True if the row is successfully inserted
	 */
	protected function insert() {
		$values = $this->getValues();
		$id = insert_query(static::$TABLE, $values);

		$this->id = intval($id);

		return true;
	}

	/**
	 * Updates Db Record for this Model
	 * @return boolean True if the row is successfully updated
	 */
	protected function update() {
		$values = $this->getValues();
		$where = array('id' => $this->getId());

		update_query(static::$TABLE, $values, $where);

		return true;
	}

	/**
	 * Creates a new instance of this Model that has not yet been persisted.
	 * @param  array  $values The values of this model
	 * @return ColoCrossing_Model An instance of this model.
	 * @static
	 */
	public static function create(array $values = array()) {
		return new static(null, $values);
	}

	/**
	 * Finds the Model from the DB with the provided Id
	 * @param  integer $id The Id
	 * @return ColoCrossing_Model|null The Model, Null if it is not found.
	 * @static
	 */
	public static function find($id) {
		$rows = select_query(static::$TABLE, implode(',', static::$COLUMNS), array('id' => $id));

		if(mysql_num_rows($rows) == 0) {
			return null;
		}

		$values = mysql_fetch_array($rows);

		return self::createInstanceFromRow($values);
	}

	/**
	 * Retrieves a collection Models from the DB that match the provided options.
	 * @param  array|null $options Optional options to modify the results. I.e. filters, sort, order, pagination
	 * @return array<ColoCrossing_Model> The Models
	 * @static
	 */
	public static function findAll(array $options = array()) {
		$where = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : null;
		$sort = isset($options['sort']) && in_array($options['sort'], static::$COLUMNS) ? $options['sort'] : 'id';
		$order = isset($options['order']) && strtolower($options['order']) == 'desc' ? 'DESC' : 'ASC';
		$join = isset($options['join']) ? $options['join'] : null;

		if(isset($options['pagination']) && is_array($options['pagination'])) {
			$pagination = array_merge(array('number' => 1, 'size' => 30), $options['pagination']);
			$size = min(max($pagination['size'], 1), 100);
			$offset = (max($pagination['number'], 1) - 1) * $size;
			$limit = $offset . ',' . $size;
		}

		$rows = select_query(static::$TABLE, implode(',', static::$COLUMNS), $where, $sort, $order, $limit, $join);

		$instances = array();
		while ($values = mysql_fetch_array($rows)) {
		    $instances[] = self::createInstanceFromRow($values);
		}
		return $instances;
	}

	/**
	 * Retrieve the Total Number of Rows in the Table
	 * @param  array|null $options Optional options to modify the results. I.e. filters
	 * @return integer 		The total record count
	 * @static
	 */
	public static function getTotalRecordCount(array $options = array()) {
		$where = isset($options['filters']) && is_array($options['filters']) ? $options['filters'] : null;

		$result = select_query(static::$TABLE, 'COUNT(*) as `total_count`', $where);
		$data = mysql_fetch_array($result);

		return isset($data) && isset($data['total_count']) ? intval($data['total_count']) : 0;
	}

	/**
	 * Creates an Instance of the Model form a DB row
	 * @param  array $values  The Values from the Row
	 * @return ColoCrossing_Model The Model Instance
	 * @static
	 */
	protected static function createInstanceFromRow($values) {
		$id = $values['id'];

		unset($values['id']);

		return new static($id, $values);
	}
}
