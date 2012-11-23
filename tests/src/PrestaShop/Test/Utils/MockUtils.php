<?php

namespace PrestaShop\Test\Utils;

class MockUtils {
	
	/**
	 * Mocks the Configuration :: get() method to return predefined values.
	 * @param array $config - configuration keys/values  
	 */
	public static function mockConfiguration($config = array()) {
		$mock = \Mockery :: mock('alias:Configuration');
		foreach ($config as $key => $value) {
			$mock->shouldReceive('get')->with($key)->andReturn($value);
		}
		
	}
	
	/**
	 * "Mocks" the Context by filling the singleton by reference. 
	 * @param array $vars - keys/values to inject in the Context instance
	 */
	public static function mockContext($vars = array()) {
		
		// We can't mock the Context class
		// because we would need to autoload it
		$context = Context :: getContext();
		foreach ($vars as $key => $value) {
			$context->{$key} = $value;
		}
		
	}
	
}