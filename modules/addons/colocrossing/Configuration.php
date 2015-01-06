<?php

/**
 * The Url to the ColoCrossing Admin Addon.
 */
define("BASE_ADMIN_URL", "/" . (isset($customadminpath) ? $customadminpath : 'admin') . "/addonmodules.php?module=colocrossing");

/**
 * The Url to the ColoCrossing Client Addon. This may need to be changed for your WHMCS configuration
 */
define("BASE_CLIENT_URL", "/index.php?m=colocrossing");
