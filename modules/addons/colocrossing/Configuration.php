<?php

/**
 * The Url to the Base of WHMCS
 * If WHMCS is not installed to the Root of the Web Server, you must set this to '/whmcs-dir/'
 */
define('WHMCS_BASE_URL', '/');

/**
 * The Directory for the WHMCS Admin Area.
 * The default value is admin. Commonly changed for security reasons.
 */
define('WHMCS_ADMIN_DIRECTORY', isset($customadminpath) ? $customadminpath : 'admin');

/**
 * The Url to the ColoCrossing Admin Addon.
 */
define('BASE_ADMIN_URL', WHMCS_BASE_URL . WHMCS_ADMIN_DIRECTORY . '/addonmodules.php?module=colocrossing');

/**
 * The Url to the ColoCrossing Client Addon. This may need to be changed for your WHMCS configuration
 */
define('BASE_CLIENT_URL', WHMCS_BASE_URL . 'manage.php');
