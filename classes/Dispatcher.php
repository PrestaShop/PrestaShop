<?php

class DispatcherCore
{
	public $controllers;
	public static $controller;

	function __construct()
	{
		$this->loadControllers();
	}

	public function dispatch()
	{
		self::$controller = $this->getController();

		self::$controller = str_replace('-', '', strtolower(self::$controller));
		if (!isset($this->controllers[self::$controller]))
			self::$controller = 'index';
		ControllerFactory::getController($this->controllers[self::$controller])->run();
	}

	protected function loadControllers()
	{
		$controller_files = scandir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'controllers');
		foreach($controller_files as $controller_filename)
		{
			if (substr($controller_filename, -14, 14) == 'Controller.php')
				$this->controllers[strtolower(substr($controller_filename, 0, -14))] = basename($controller_filename, '.php');
		}

		// add default controller
		$this->controllers['index'] = 'IndexController';
		$this->controllers['authentication'] = $this->controllers['auth'];
		$this->controllers['productscomparison'] = $this->controllers['compare'];
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
