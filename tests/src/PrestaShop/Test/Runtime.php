<?php

namespace PrestaShop\Test;

use Mockery as Mockery;
use PrestaShop as PrestaShop;

class Runtime {
	
	/**
	 * "Disables" the database by mocking the Db :: getInstance() method, 
	 * and returning always an implementation that does nothing. 
	 * Also defines some constants used inside the Db class. 
	 * @return void
	 */
	public static function disableDb() {
		
		define('_DB_SERVER_', 	'');
		define('_DB_NAME_', 	'');
		define('_DB_USER_', 	'');
		define('_DB_PASSWD_', 	'');
		define('_DB_PREFIX_', 	'');
		define('_PS_DEBUG_SQL_', 0);
		
		$db = new PrestaShop\Test\Stubs\Db();
		$mock = Mockery :: mock('alias:Db');
		$mock->shouldReceive('getInstance')->andReturn($db);
		
	}
	
	/**
	 * Use this method to force the Configuration class to return what you want. 
	 * @param array $config - configuration keys/values
	 * @return void
	 */
	public static function configuration($config = array()) {
		$mock = Mockery :: mock('alias:Configuration');
		foreach ($config as $key => $value) {
			$mock->shouldReceive('get')->with($key)->andReturn($value);
		}
		
	}
	
	/**
	 * Use this method to inject the desired values into the Context instance. 
	 * @param array $vars - keys/values to inject in the Context instance
	 * @return void
	 */
	public static function context($vars = array()) {
		
		// We can't mock the Context class
		// because we would need to autoload it
		$context = \Context :: getContext();
		foreach ($vars as $key => $value) {
			$context->{$key} = $value;
		}
		
	}
	
}