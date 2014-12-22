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
	protected static $COLUMNS = array('id', 'name', 'description');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblproducts';

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

}
