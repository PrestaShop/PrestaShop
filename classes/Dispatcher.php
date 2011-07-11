<?php

class DispatcherCore
{
	public static $controllers = array();
	public static $controller;

	function __construct()
	{
		$this->loadControllers();
	}

	public function dispatch()
	{
		self::$controller = $this->getController();

		self::$controller = str_replace('-', '', strtolower(self::$controller));
		if (!isset(self::$controllers[self::$controller]))
			self::$controller = 'index';
		ControllerFactory::getController(self::$controllers[self::$controller])->run();
	}

	public static function loadControllers()
	{
		$controller_files = scandir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'controllers');
		foreach ($controller_files as $controller_filename)
		{
			if (substr($controller_filename, -14, 14) == 'Controller.php')
				self::$controllers[strtolower(substr($controller_filename, 0, -14))] = basename($controller_filename, '.php');
		}

		// add default controller
		self::$controllers['index'] = 'IndexController';
		self::$controllers['authentication'] = self::$controllers['auth'];
		self::$controllers['productscomparison'] = self::$controllers['compare'];
	}

	public function getController()
	{
		$controller = Tools::getValue('controller');
		if (isset($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $controller_string))
		{
			$controller = $controller_string[1];
			if (isset($_GET['controller']))
				$_GET[$controller_string[2]] = $controller_string[3];
			elseif (isset($_POST['controller']))
				$_POST[$controller_string[2]] = $controller_string[3];
		}
		return (!empty($controller)) ? $controller : 'index';
	}
}
