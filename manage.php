<?php

// Modify $_GET and $_REQUEST Globals to Reference the ColoCrossing Module
$_GET['m'] = 'colocrossing';
$_REQUEST['m'] = 'colocrossing';

// Forward Request to WHMCS to Dispatch Module
require('index.php');
