<?php

class DispatcherCore
{
	/**
	 * @var Dispatcher
	 */
	public static $instance = null;

	/**
	 * @var array
	 */
	protected $defaultRoutes = array(
		'product' => array(
			'controller' =>	'product',
			'rule' =>		'{number:id_product}-{word}.html',
		),
		'category' => array(
			'controller' =>	'category',
			'rule' =>		'{number:id_category}-{word}',
		),
		'product_alt' => array(
			'controller' =>	'product',
			'rule' =>		'{word1}/{number:id_product}-{word2}.html',
		),
		'supplier' => array(
			'controller' =>	'supplier',
			'rule' =>		'{number:id_supplier}__{word}',
		),
		'manufacturer' => array(
			'controller' =>	'manufacturer',
			'rule' =>		'{number:id_manufacturer}_{word}',
		),
		'cms' => array(
			'controller' =>	'cms',
			'rule' =>		'content/{number:id_cms}-{word}',
		),
		'cms_category' => array(
			'controller' =>	'cms',
			'rule' =>		'content/category/{number:id_cms_category}-{word}',
		),
	);
	
	/**
	 * @var $useRoutes bool
	 */
	protected $useRoutes = false;

	/**
	 * @var $routes array
	 */
	protected $routes = array();
	
	/**
	 * @var array
	 */
	protected $keywords = array(
		'number' =>	'[0-9]+',
		'word' =>	'[a-zA-Z0-9-]*',
	);

	public static $controllers = array();
	public static $controller;

	/**
	 * Get current instance of dispatcher (singleton)
	 * 
	 * @return Dispatcher
	 */
	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new Dispatcher();
		return self::$instance;
	}

	/**
	 * Need to be instancied from getInstance() method
	 */
	protected function __construct()
	{
		$this->useRoutes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
		$this->loadControllers();
		
		// Load default routes
		if ($this->useRoutes)
			foreach ($this->defaultRoutes as $id => $route)
				$this->addRoute($id, $route['rule'], $route['controller']);
	}

	/**
	 * "main" method of dispatcher, call the controller
	 */
	public function dispatch()
	{
		self::$controller = $this->getController();

		self::$controller = str_replace('-', '', strtolower(self::$controller));
		if (!isset(self::$controllers[self::$controller]))
			self::$controller = 'index';
		ControllerFactory::getController(self::$controllers[self::$controller])->run();
	}
	
	/**
	 * 
	 * @param string $id Name of the route (need to be uniq, a second route with same name will override the first)
	 * @param string $rule URL rule
	 * @param string $controller Controller to call if request uri match the rule
	 */
	public function addRoute($routeID, $rule, $controller)
	{	
		$regexp = preg_quote($rule, '#');
		$required = array();
		preg_match_all('#\\\{('.implode('|', array_keys($this->keywords)).')[0-9]*(\\\:([a-z0-9_]+))?\\\}#', $regexp, $m);
		for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			if ($m[3][$i])
			{
				$regexp = str_replace($m[0][$i], '(?P<'.$m[3][$i].'>'.$this->keywords[$m[1][$i]].')', $regexp);
				$required[$m[3][$i]] = $m[1][$i];
			}
			else
				$regexp = str_replace($m[0][$i], '('.$this->keywords[$m[1][$i]].')', $regexp);

		$regexp = '#^/'.$regexp.'#';
		$this->routes[$routeID] = array(
			'rule' =>		$rule,
			'regexp' =>		$regexp,
			'controller' =>	$controller,
			'required' =>	$required,
		);
	}

	/**
	 * 
	 * 
	 * @param string $routeID Name the route
	 * @param array $params 
	 */
	public function createUrl($routeID, $params = array())
	{
		if (!is_array($params))
			die('Dispatcher::createURL() $params must be an array');

		if (!isset($this->routes[$routeID]))
			return '';
		$route = $this->routes[$routeID];
		
		// Get required parameters
		$queryParams = array();
		foreach (array_keys($route['required']) as $key)
		{
			if (!array_key_exists($key, $params))
				die("Dispatcher::createURL() miss required parameter '$key'");
			$queryParams[$key] = $params[$key];
		}

		// Build an URL which match a route
		if ($this->useRoutes)
		{
			$url = $route['rule'];
			
			// Replace required parameters
			foreach ($route['required'] as $key => $keyword)
			{
				$url = str_replace('{'.$keyword.':'.$key.'}', $params[$key], $url);
				unset($params[$key]);
			}

			// Replace other parameters
			foreach ($params as $key => $value)
				$url = str_replace('{'.$key.'}', $value, $url);
		}
		// Build a classic URL index.php?controller=foo&...
		else
			$url = 'index.php?controller='.$route['controller'].(($queryParams) ? '&'.http_build_query($queryParams) : '');
		
		return $url;
	}

	/**
	 * Load list of available controllers
	 */
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

	/**
	 * Retrieve the controller from URL or request URI if routes are activated
	 * 
	 * @return string
	 */
	public function getController()
	{
		// Use routes ? (for URL rewriting)
		if ($this->useRoutes)
		{
			// Get request URI (HTTP_X_REWRITE_URL is used by IIS)
			if (isset($_SERVER['REQUEST_URI']))
				$request = $_SERVER['REQUEST_URI'];
			else if (isset($_SERVER['HTTP_X_REWRITE_URL']))
				$request = $_SERVER['HTTP_X_REWRITE_URL'];
			else
				return 'index';

			$controller = 'index';
			foreach ($this->routes as $route)
				if (preg_match($route['regexp'], $request, $m))
				{
					// Route found ! Now fill $_GET with parameters of URI
					$controller = $route['controller'];
					foreach ($m as $k => $v)
						if (!is_numeric($k))
							$_GET[$k] = $v;
					break;
				}
			
			$_GET['controller'] = $controller;
			return $controller;
		}
		// Default mode, take controller from URL
		else
		{
			$controller = Tools::getValue('controller');
			if (isset($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m))
			{
				$controller = $m[1];
				if (isset($_GET['controller']))
					$_GET[$m[2]] = $m[3];
				else if (isset($_POST['controller']))
					$_POST[$m[2]] = $m[3];
			}
			return (!empty($controller)) ? $controller : 'index';
		}
	}
}
