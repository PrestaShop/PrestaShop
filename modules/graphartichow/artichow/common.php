<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

/*
 * Get the minimum of an array and ignore non numeric values
 */
function array_min($array) {

	if(is_array($array) and count($array) > 0) {
	
		do {
			$min = array_pop($array);
			if(is_numeric($min) === FALSE) {
				$min = NULL;
			}
		} while(count($array) > 0 and $min === NULL);
		
		if($min !== NULL) {
			$min = (float)$min;
		}
		
		foreach($array as $value) {
			if(is_numeric($value) and (float)$value < $min) {
				$min = (float)$value;
			}
		}
		
		return $min;
	
	}
	
	return NULL;

}

/*
 * Get the maximum of an array and ignore non numeric values
 */
function array_max($array) {

	if(is_array($array) and count($array) > 0) {
	
		do {
			$max = array_pop($array);
			if(is_numeric($max) === FALSE) {
				$max = NULL;
			}
		} while(count($array) > 0 and $max === NULL);

		if($max !== NULL) {
			$max = (float)$max;
		}
		
		foreach($array as $value) {
			if(is_numeric($value) and (float)$value > $max) {
				$max = (float)$value;
			}
		}
		
		return $max;
	
	}
	
	return NULL;

}
/*
 * Define file_put_contents() if needed
 */
if(function_exists('file_put_contents') === FALSE) {

	function file_put_contents($file, $content) {
		$fp = fopen($file, 'w');
		if($fp) {
			fwrite($fp, $content);
			fclose($fp);
		}
	}
	
}

/*
 * Change error handler
 */
set_error_handler('errorHandlerArtichow');

function errorHandlerArtichow($level, $message, $file, $line) {
	awImage::drawError($message.' in '.$file.' on line '.$line.'.');
}

