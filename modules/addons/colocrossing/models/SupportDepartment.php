<?php

if(!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * A WHMCS Support Department
 */
class ColoCrossing_Model_SupportDepartment extends ColoCrossing_Model {

	/**
	 * The Support Department Columns
	 * @var array<string>
	 */
	protected static $COLUMNS = array('id', 'name');

	/**
	 * The Table Name
	 * @var string
	 */
	protected static $TABLE = 'tblticketdepartments';

	/**
	 * @return string The Name of the Support Department
	 */
	public function getName() {
		$name = $this->getValue('name');

		return empty($name) ? '' : $name;
	}

	public static function getNames() {
		$departments = self::findAll();
		$names = array();

		foreach ($departments as $department) {
			$names[] = $department->getName();
		}

		return $names;
	}

}
