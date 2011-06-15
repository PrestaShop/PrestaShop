<?php

class DispatcherCore
{
	public $controllers;

	function __construct()
	{
		$this->loadControllers();
	}

	public function dispatch()
	{
		$requested_controller = $this->getController();

		$controller = $this->controllers[str_replace('-', '', strtolower($requested_controller))];
		ControllerFactory::getController($controller)->run();
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
	}

	public function getController()
	{
		return (isset($_GET['controller'])) ? $_GET['controller'] : 'index';
	}
}

