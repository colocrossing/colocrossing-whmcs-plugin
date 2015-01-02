<?php

//Verify cURL extension is available
if (!function_exists('curl_init'))
{
  throw new Exception('ColoCrossing API Client needs the CURL PHP extension.');
}

//Verify JSON extension is available
if (!function_exists('json_decode'))
{
  throw new Exception('ColoCrossing API Client needs the JSON PHP extension.');
}

//Include Client Class
require_once(dirname(__FILE__) . '/ColoCrossing/Client.php');

//Include Http Package
require_once(dirname(__FILE__) . '/ColoCrossing/Http/Request.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Http/Executor.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Http/Response.php');

//Include Collection Classes
require_once(dirname(__FILE__) . '/ColoCrossing/AbstractCollection.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Collection.php');
require_once(dirname(__FILE__) . '/ColoCrossing/PagedCollection.php');

//Include Resources Package
require_once(dirname(__FILE__) . '/ColoCrossing/Resource.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Resource/Abstract.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Resource/Factory.php');

//Include Child Resources Package
require_once(dirname(__FILE__) . '/ColoCrossing/Resource/Child/Abstract.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Resource/Child/Factory.php');

//Include Objects Package
require_once(dirname(__FILE__) . '/ColoCrossing/Object.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Resource/Object.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Object/Factory.php');

//Include Errors Package
require_once(dirname(__FILE__) . '/ColoCrossing/Error.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Error/Api.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Error/Authorization.php');
require_once(dirname(__FILE__) . '/ColoCrossing/Error/NotFound.php');

//Include Utility Class
require_once(dirname(__FILE__) . '/ColoCrossing/Utility.php');
