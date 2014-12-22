<?php

/**
 * A General Utility Class For Misc Helper Methods
 */
class ColoCrossing_Utilities {

	public static function parseTemplate($file, array $data = array()) {
	    extract($data);
	    ob_start();
	    require $file;
	    return ob_get_clean();
	}

}
