<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class DispatcherCore
{
	/**
	 * @var Dispatcher
	 */
	public static $instance = null;

	/**
	 * List of default routes
	 * 
	 * @var array
	 */
	public $defaultRoutes = array(
		'product_rule' => array(
			'controller' =>	'product',
			'rule' =>		'{category:/}{id}-{rewrite}{-:ean13}.html',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_product'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'ean13' =>			array('regexp' => '[a-zA-Z0-9-]*'),
				'category' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'reference' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'manufacturer' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'supplier' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'price' =>			array('regexp' => '[0-9\.,]*'),
				'tags' =>			array('regexp' => '[a-zA-Z0-9-]*'),
			),
		),
		'category_rule' => array(
			'controller' =>	'category',
			'rule' =>		'{id}-{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_category'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-]*'),
			),
		),
		'supplier_rule' => array(
			'controller' =>	'supplier',
			'rule' =>		'{id}__{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_supplier'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-]*'),
			),
		),
		'manufacturer_rule' => array(
			'controller' =>	'manufacturer',
			'rule' =>		'{id}_{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_manufacturer'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-]*'),
			),
		),
		'cms_rule' => array(
			'controller' =>	'cms',
			'rule' =>		'content/{id}-{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_cms'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-]*'),
			),
		),
		'cms_category_rule' => array(
			'controller' =>	'cms',
			'rule' =>		'content/category/{id}-{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_cms_category'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-]*'),
			),
		),
	);

	/**
	 * If true, use routes to build URL (mod rewrite must be activated)
	 * 
	 * @var bool
	 */
	protected $useRoutes = false;

	/**
	 * List of loaded routes
	 * 
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Current controller name
	 * 
	 * @var string
	 */
	protected $controller;
	
	/**
	 * Current request uri
	 * 
	 * @var string
	 */
	protected $requestURI;
	
	/**
	 * Store empty route (a route with an empty rule)
	 * 
	 * @var array
	 */
	protected $emptyRoute;

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
		$this->loadRoutes();
		
		// Get request uri (HTTP_X_REWRITE_URL is used by IIS)
		if (isset($_SERVER['REQUEST_URI']))
			$this->requestURI = $_SERVER['REQUEST_URI'];
		else if (isset($_SERVER['HTTP_X_REWRITE_URL']))
			$this->requestURI = $_SERVER['HTTP_X_REWRITE_URL'];
	}

	/**
	 * Find the controller and instantiate it
	 */
	public function dispatch()
	{
		$this->requestURI = preg_replace('#^'.preg_quote(Context::getContext()->shop->getBaseURI(), '#').'#i', '/', $this->requestURI);

		// If there are several languages, get language from uri
		if ($this->useRoutes && Language::isMultiLanguageActivated())
			if (preg_match('#^/([a-z]{2})/#', $this->requestURI, $m))
			{
				$_GET['isolang'] = $m[1];
				$this->requestURI = substr($this->requestURI, 3);
			}
		// Get and instantiate controller
		$this->getController();
		$controllers = Dispatcher::getControllers();
		if (!$this->controller)
			$this->controller = 'index';
		if (!isset($controllers[$this->controller]))
			$this->controller = 'pagenotfound';
		ControllerFactory::getController($controllers[$this->controller])->run();
	}
	
	/**
	 * Load default routes
	 */
	protected function loadRoutes()
	{
		$context = Context::getContext();
		foreach ($this->defaultRoutes as $id => $route)
			$this->addRoute($id, $route['rule'], $route['controller'], $route['keywords']);

		if ($this->useRoutes)
		{
			// Load routes from meta table
			$sql = 'SELECT m.page, ml.url_rewrite
					FROM `'._DB_PREFIX_.'meta` m
					LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta'.$context->shop->sqlLang('ml').')
					WHERE id_lang = '.(int)$context->language->id;
			if ($results = Db::getInstance()->ExecuteS($sql))
				foreach ($results as $row)
				{
					if ($row['url_rewrite'])
						$this->addRoute($row['page'], $row['url_rewrite'], $row['page']);
					else
						$this->emptyRoute = array(
							'routeID' =>	$row['page'],
							'rule' =>		$row['url_rewrite'],
							'controller' =>	$row['page'],
						);
				}
				
			// Load custom routes
			foreach ($this->defaultRoutes as $routeID => $routeData)
				if ($customRoute = Configuration::get('PS_ROUTE_'.$routeID))
					$this->addRoute($routeID, $customRoute, $routeData['controller'], $routeData['keywords']);
		}
	}
	
	/**
	 * 
	 * @param string $id Name of the route (need to be uniq, a second route with same name will override the first)
	 * @param string $rule Url rule
	 * @param string $controller Controller to call if request uri match the rule
	 */
	public function addRoute($routeID, $rule, $controller, $keywords = array())
	{
		$regexp = preg_quote($rule, '#');
		if ($keywords)
		{
			$transformKeywords = array();
			preg_match_all('#\\\{(([^{}]+)\\\:)?('.implode('|', array_keys($keywords)).')(\\\:([^{}]+))?\\\}#', $regexp, $m);
			for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			{
				$prepend = $m[2][$i];
				$keyword = $m[3][$i];
				$append = $m[5][$i];
				$transformKeywords[$keyword] = array(
					'required' =>	isset($keywords[$keyword]['param']),
					'prepend' =>	stripslashes($prepend),
					'append' =>		stripslashes($append),
				);

				if (isset($keywords[$keyword]['param']))
					$regexp = str_replace($m[0][$i], (($prepend) ? '('.$prepend.')?' : '').'(?P<'.$keywords[$keyword]['param'].'>'.$keywords[$keyword]['regexp'].')'.(($append) ? '('.$append.')?' : ''), $regexp);
				else
					$regexp = str_replace($m[0][$i], (($prepend) ? '('.$prepend.')?' : '').'('.$keywords[$keyword]['regexp'].')'.(($append) ? '('.$append.')?' : ''), $regexp);
				
			}
			$keywords = $transformKeywords;
		}

		$regexp = '#^/'.$regexp.'#';
		$this->routes[$routeID] = array(
			'rule' =>		$rule,
			'regexp' =>		$regexp,
			'controller' =>	$controller,
			'keywords' =>	$keywords,
		);
	}

	/**
	 * Check if a keyword is written in a route rule
	 * 
	 * @param string $routeID
	 * @param string $keyword
	 * @return bool
	 */
	public function hasKeyword($routeID, $keyword)
	{
		if (!isset($this->routes[$routeID]))
			return false;
			
		return preg_match('#\{([^{}]+:)?'.preg_quote($keyword, '#').'(:[^{}])?\}#', $this->routes[$routeID]['rule']);
	}

	/**
	 * Check if a route rule contain all required keywords of default route definition
	 * 
	 * @param string $routeID
	 * @param string $rule Rule to verify
	 * @param array $errors List of missing keywords
	 */
	public function validateRoute($routeID, $rule, &$errors = array())
	{
		$errors = array();
		if (!isset($this->defaultRoutes[$routeID]))
			return false;

		foreach ($this->defaultRoutes[$routeID]['keywords'] as $keyword => $data)
			if (isset($data['param']) && !preg_match('#\{([^{}]+:)?'.$keyword.'(:[^{}])?\}#', $rule))
				$errors[] = $keyword;

		return (count($errors)) ? false : true;
	}

	/**
	 * Create an url from
	 * 
	 * @param string $routeID Name the route
	 * @param array $params
	 * @param bool $useRoutes If false, don't use to create this url
	 */
	public function createUrl($routeID, $params = array(), $useRoutes = true)
	{
		if (!is_array($params))
			die('Dispatcher::createUrl() $params must be an array');

		if (!isset($this->routes[$routeID]))
		{
			$query = http_build_query($params);
			return ($routeID == 'index') ? 'index.php'.(($query) ? '?'.$query : '') : 'index.php?controller='.$routeID.(($query) ? '&'.$query : '');
		}
		$route = $this->routes[$routeID];

		// Check required fields
		$queryParams = array();
		foreach ($route['keywords'] as $key => $data)
		{
			if (!$data['required'])
				continue;

			if (!array_key_exists($key, $params))
				die("Dispatcher::createUrl() miss required parameter '$key' for route '$routeID'");
			$queryParams[$this->defaultRoutes[$routeID]['keywords'][$key]['param']] = $params[$key];
		}

		// Build an url which match a route
		if ($this->useRoutes && $useRoutes)
		{
			$url = $route['rule'];
			foreach ($params as $key => $value)
			{
				if (!isset($route['keywords'][$key]))
					continue;
				$data = $route['keywords'][$key];
				if ($params[$key])
					$replace = $route['keywords'][$key]['prepend'].$params[$key].$route['keywords'][$key]['append'];
				else
					$replace = '';
				$url = preg_replace('#\{([^{}]+:)?'.$key.'(:[^{}])?\}#', $replace, $url);
			}
			$url = preg_replace('#\{([^{}]+:)?[a-z0-9_]+?(:[^{}])?\}#', '', $url);
		}
		// Build a classic url index.php?controller=foo&...
		else
			$url = 'index.php?controller='.$route['controller'].(($queryParams) ? '&'.http_build_query($queryParams) : '');
		
		return $url;
	}

	/**
	 * Retrieve the controller from url or request uri if routes are activated
	 * 
	 * @return string
	 */
	public function getController()
	{
		if ($this->controller)
			return $this->controller;
			
		$controller = Tools::getValue('controller');

		if (isset($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m))
		{
			$controller = $m[1];
			if (isset($_GET['controller']))
				$_GET[$m[2]] = $m[3];
			else if (isset($_POST['controller']))
				$_POST[$m[2]] = $m[3];
		}

		// Use routes ? (for url rewriting)
		if ($this->useRoutes && !$controller)
		{
			if (!$this->requestURI)
				return 'pagenotfound';
			$controller = 'index';
			
			// Add empty route as last route to prevent this greedy regexp to match request uri before right time
			if ($this->emptyRoute)
				$this->addRoute($this->emptyRoute['routeID'], $this->emptyRoute['rule'], $this->emptyRoute['controller']);

			foreach ($this->routes as $route)
				if (preg_match($route['regexp'], $this->requestURI, $m))
				{
					// Route found ! Now fill $_GET with parameters of uri
					$controller = $route['controller'];
					foreach ($m as $k => $v)
						if (!is_numeric($k))
							$_GET[$k] = $v;
					break;
				}
			$this->controller = $controller;
		}
		// Default mode, take controller from url
		else
			$this->controller = $controller;

		$this->controller = str_replace('-', '', strtolower($this->controller));
		$_GET['controller'] = $this->controller;
		return $this->controller;
	}
	
	/**
	 * Get list of available controllers
	 * 
	 * @return array
	 */
	public static function getControllers()
	{
		$controller_files = scandir(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'controllers');
		$controllers = array();
		foreach ($controller_files as $controller_filename)
		{
			if (substr($controller_filename, -14, 14) == 'Controller.php')
				$controllers[strtolower(substr($controller_filename, 0, -14))] = basename($controller_filename, '.php');
		}

		// add default controller
		$controllers['index'] = 'IndexController';
		$controllers['authentication'] = $controllers['auth'];
		$controllers['productscomparison'] = $controllers['compare'];
		
		return $controllers;
	}
}
