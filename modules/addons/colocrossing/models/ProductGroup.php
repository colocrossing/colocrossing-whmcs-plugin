<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A WHMCS Product Group
 */
class ColoCrossing_Model_ProductGroup extends ColoCrossing_Model {

	/**
	 * The Product Group Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'name');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblproductgroups';

	/**
	 * @return string The Name of the Product Group
	 */
	public function getName() {
		$name = $this->getValue('name');

		return empty($name) ? '' : $name;
	}

}
