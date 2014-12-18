<?php

require_once dirname(__FILE__) . '/../Model.php';

class ColoCrossing_Event extends ColoCrossing_Model {

	protected static $COLUMNS = array('id', 'user_type', 'user_id', 'service_id', 'description', 'time');

	protected static $TABLE = 'mod_colocrossing_events';

}
