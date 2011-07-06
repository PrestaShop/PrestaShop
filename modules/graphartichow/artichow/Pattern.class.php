<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once dirname(__FILE__)."/Graph.class.php";

/**
 * All patterns must derivate from this class
 *
 * @package Artichow
 */
abstract class awPattern {

	/**
	 * Pattern arguments
	 *
	 * @var array
	 */
	protected $args = array();
	
	/**
	 * Load a pattern
	 *
	 * @param string $pattern Pattern name
	 * @return Component
	 */
	public static function get($pattern) {
	
		$file = ARTICHOW_PATTERN.DIRECTORY_SEPARATOR.$pattern.'.php';
	
		if(is_file($file)) {
		
			require_once $file;
			
			$class = $pattern.'Pattern';
			
			if(class_exists($class)) {
				return new $class;
			} else {
				awImage::drawError("Class Pattern: Class '".$class."' does not exist.");
			}
		
		} else {
			awImage::drawError("Class Pattern: Pattern '".$pattern."' does not exist.");
		}
	
	}
	
	/**
	 * Change pattern argument
	 *
	 * @param string $name Argument name
	 * @param mixed $value Argument value
	 */
	public function setArg($name, $value) {
		if(is_string($name)) {
			$this->args[$name] = $value;
		}
	}
	
	/**
	 * Get an argument
	 *
	 * @param string $name
	 * @param mixed $default Default value if the argument does not exist (default to NULL)
	 * @return mixed Argument value
	 */
	protected function getArg($name, $default = NULL) {
		if(array_key_exists($name, $this->args)) {
			return $this->args[$name];
		} else {
			return $default;
		}
	}
	
	/**
	 * Change several arguments
	 *
	 * @param array $args New arguments
	 */
	public function setArgs($args) {
		if(is_array($args)) {
			foreach($args as $name => $value) {
				$this->setArg($name, $value);
			}
		}
	}

}

registerClass('Pattern', TRUE);
