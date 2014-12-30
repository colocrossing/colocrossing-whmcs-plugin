<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A WHMCS Product
 */
class ColoCrossing_Model_Product extends ColoCrossing_Model {

	/**
	 * The Product Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'gid', 'name', 'description');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblproducts';

	/**
	 * A Class Wide Cache of Product Groups
	 * @var array<integer, ColoCrossing_Model_ProductGroup>
	 */
	protected static $GROUPS = array();

	/**
	 * @return string The Name of the Product
	 */
	public function getName() {
		$name = $this->getValue('name');

		return empty($name) ? '' : $name;
	}

	/**
	 * @return string The Description of the Product
	 */
	public function getDescription() {
		$description = $this->getValue('description');

		return empty($description) ? '' : $description;
	}

	/**
	 * @return ColoCrossing_Model_ProductGroup The Group of the Product
	 */
	public function getGroup() {
		$id = $this->getValue('gid');

		if(empty($id)) {
			return null;
		}

		if(isset(self::$GROUPS[$id])) {
			return self::$GROUPS[$id];
		}

		return self::$GROUPS[$id] = ColoCrossing_Model_ProductGroup::find($id);
	}

}
