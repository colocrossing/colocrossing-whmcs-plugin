<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A WHMCS Support Status
 */
class ColoCrossing_Model_SupportStatus extends ColoCrossing_Model {

	/**
	 * The Support Status Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'title');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblticketstatuses';

	/**
	 * @return string The Name of the Support Status
	 */
	public function getName() {
		$name = $this->getValue('title');

		return empty($name) ? '' : $name;
	}

	public static function getNames() {
		$statuses = self::findAll();
		$names = array();

		foreach ($statuses as $status) {
			$names[] = $status->getName();
		}

		return $names;
	}

}
