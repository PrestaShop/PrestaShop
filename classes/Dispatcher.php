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
	 * @var array List of default routes
	 */
	public $default_routes = array(
		'product_rule' => array(
			'controller' =>	'product',
			'rule' =>		'{category:/}{id}-{rewrite}{-:ean13}.html',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_product'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'ean13' =>			array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'category' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'reference' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'manufacturer' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'supplier' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'price' =>			array('regexp' => '[0-9\.,]*'),
				'tags' =>			array('regexp' => '[a-zA-Z0-9-\pL]*'),
			),
		),
		'category_rule' => array(
			'controller' =>	'category',
			'rule' =>		'{id}-{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_category'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
			),
		),
		'supplier_rule' => array(
			'controller' =>	'supplier',
			'rule' =>		'{id}__{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_supplier'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
			),
		),
		'manufacturer_rule' => array(
			'controller' =>	'manufacturer',
			'rule' =>		'{id}_{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_manufacturer'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
			),
		),
		'cms_rule' => array(
			'controller' =>	'cms',
			'rule' =>		'content/{id}-{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_cms'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
			),
		),
		'cms_category_rule' => array(
			'controller' =>	'cms',
			'rule' =>		'content/category/{id}-{rewrite}',
			'keywords' => array(
				'id' =>				array('regexp' => '[0-9]+', 'param' => 'id_cms_category'),
				'rewrite' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_keywords' =>	array('regexp' => '[a-zA-Z0-9-\pL]*'),
				'meta_title' =>		array('regexp' => '[a-zA-Z0-9-\pL]*'),
			),
		),
		'module' => array(
			'controller' =>	'module',
			'rule' =>		'module/{module}/{process}',
			'keywords' => array(
				'module' =>			array('regexp' => '[a-zA-Z0-9_-]+', 'param' => 'module'),
				'process' =>		array('regexp' => '[a-zA-Z0-9_-]+', 'param' => 'process'),
			),
		),
	);

	/**
	 * @var bool If true, use routes to build URL (mod rewrite must be activated)
	 */
	protected $use_routes = false;

	/**
	 * @var array List of loaded routes
	 */
	protected $routes = array();

	/**
	 * @var string Current controller name
	 */
	protected $controller;

	/**
	 * @var string Current request uri
	 */
	protected $request_uri;

	/**
	 * @var array Store empty route (a route with an empty rule)
	 */
	protected $empty_route;

	/**
	 * @var string Set default controller, which will be used if http parameter 'controller' is empty
	 */
	protected $default_controller = 'Index';

	/**
	 * @var string Controller to use if found controller doesn't exist
	 */
	protected $controller_not_found = 'pagenotfound';

	/**
	 * @var array List of controllers where are stored controllers
	 */
	protected $controller_directories = array();

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
		$this->setDefaultController('index');
		$this->setControllerDirectories(_PS_FRONT_CONTROLLER_DIR_);
		$this->use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
		$this->loadRoutes();

		// Get request uri (HTTP_X_REWRITE_URL is used by IIS)
		if (isset($_SERVER['REQUEST_URI']))
			$this->request_uri = $_SERVER['REQUEST_URI'];
		else if (isset($_SERVER['HTTP_X_REWRITE_URL']))
			$this->request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
		$this->request_uri = rawurldecode($this->request_uri);
	}

	/**
	 * Set default controller, which will be used if http parameter 'controller' is empty
	 *
	 * @param string $controller
	 */
	public function setDefaultController($controller)
	{
		$this->default_controller = $controller;
	}

	/**
	 * Controller to use if found controller doesn't exist
	 *
	 * @var string $controller
	 */
	public function setControllerNotFound($controller)
	{
		$this->controller_not_found = $controller;
	}

	/**
	 * Set list of controllers where are stored controllers
	 *
	 * @param mixed A directory, or an array of directory
	 */
	public function setControllerDirectories($dir)
	{
		if (!is_array($dir))
			$dir = array($dir);
		$this->controller_directories = $dir;
	}

	/**
	 * Get the controller row in db
	 *
	 * @param string $name
	 */
	public static function getAdminController($name)
	{
		if (!Validate::isTabName($name))
		return false;

		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT id_tab, class_name, module
		FROM `'._DB_PREFIX_.'tab`
		WHERE LOWER(class_name) = \''.pSQL($name).'\'');
		return $row;
	}

	/**
	 * Include the file containing a controller or tab from a mdodule
	 *
	 * @param string $module
	 * @param string $name controller name
	 * @return string|bool controller type or false if file not found
	 */
	public static function includeModuleClass($module, $name)
	{
		if (file_exists(_PS_MODULE_DIR_.$module.'/'.$name.'Controller.php'))
		{
			include(_PS_MODULE_DIR_.$module.'/'.$name.'Controller.php');
			return 'controller';
		}

		if (file_exists(_PS_MODULE_DIR_.$module.'/'.$name.'.php'))
		{
			include(_PS_MODULE_DIR_.$module.'/'.$name.'.php');
			return 'tab';
		}
		return false;
	}

	/**
	 * Find the controller and instantiate it
	 */
	public function dispatch()
	{
		$this->request_uri = preg_replace('#^'.preg_quote(Context::getContext()->shop->getBaseURI(), '#').'#i', '/', $this->request_uri);

		// If there are several languages, get language from uri
		if ($this->use_routes && Language::isMultiLanguageActivated())
			if (preg_match('#^/([a-z]{2})/#', $this->request_uri, $m))
			{
				$_GET['isolang'] = $m[1];
				$this->request_uri = substr($this->request_uri, 3);
			}

		// Get current controller and list of controllers
		$this->getController();

		if (!$this->controller)
			$this->controller = $this->default_controller;

		// FO dispatch
		if (!defined('_PS_ADMIN_DIR_'))
		{
			$controllers = self::getControllers($this->controller_directories);
 			if (!isset($controllers[$this->controller]))
 				$this->controller = strtolower($this->controller_not_found);
 			$controller_class = $controllers[$this->controller];

			// If module controller is called, we have to call the right module controller
			if ($controller_class == 'ModuleController')
			{
				$module_name = Tools::getValue('module');
				$module = Module::getInstanceByName($module_name);
				if (Validate::isLoadedObject($module) && $module->active && file_exists(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'Controller.php'))
				{
					include_once(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'Controller.php');
					$controller_class = 'Module'.$module_name.'Controller';
				}
				else
					$controller_class = $controllers[$this->controller_not_found];
			}
		}
		// BO dispatch
		else
		{
			// Get controller class name
			$controller_row = self::getAdminController($this->controller);
			if (empty($controller_row))
			{
				// We need controller_not_found to be the camelcase controller name
				$this->controller = strtolower($this->controller_not_found);
				$controller_class = $this->controller_not_found;
			}
			else
				$controller_class = $controller_row['class_name'];

			// If Tab/Controller is in module, include it
			if (!empty($controller_row['module']))
				$controller_type = self::includeModuleClass($controller_row['module'], $controller_class);
			// If it is an AdminTab, include it
			else if (file_exists(_PS_ADMIN_DIR_.'/tabs/'.$controller_class.'.php'))
			{
				include(_PS_ADMIN_DIR_.'/tabs/'.$controller_class.'.php');
				$controller_type = 'tab';
			}
			// For retrocompatibility with admin/tabs/ old system
			if (isset($controller_type) && $controller_type == 'tab')
			{
				require_once(_PS_ADMIN_DIR_.'/functions.php');
				$ajax_mode = !empty($_REQUEST['ajaxMode']);
				runAdminTab($controller_class, $ajax_mode);
				return;
			}

			$controller_class = $controller_class.'Controller';
		}

		// Instantiate controller
		try
		{
			Controller::getController($controller_class)->run();
		}
		catch (PrestashopException $e)
		{
			$e->displayMessage();
		}
	}

	/**
	 * Load default routes
	 */
	protected function loadRoutes()
	{
		$context = Context::getContext();
		foreach ($this->default_routes as $id => $route)
			$this->addRoute($id, $route['rule'], $route['controller'], $route['keywords']);

		if ($this->use_routes)
		{
			// Load routes from meta table
			$sql = 'SELECT m.page, ml.url_rewrite
					FROM `'._DB_PREFIX_.'meta` m
					LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta'.$context->shop->addSqlRestrictionOnLang('ml').')
					WHERE id_lang = '.(int)$context->language->id.'
					ORDER BY LENGTH(ml.url_rewrite) DESC';
			if ($results = Db::getInstance()->executeS($sql))
				foreach ($results as $row)
				{
					if ($row['url_rewrite'])
						$this->addRoute($row['page'], $row['url_rewrite'], $row['page']);
					else
						$this->empty_route = array(
							'routeID' =>	$row['page'],
							'rule' =>		$row['url_rewrite'],
							'controller' =>	$row['page'],
						);
				}

			// Load custom routes
			foreach ($this->default_routes as $route_id => $route_data)
				if ($custom_route = Configuration::get('PS_ROUTE_'.$route_id))
					$this->addRoute($route_id, $custom_route, $route_data['controller'], $route_data['keywords']);
		}
	}

	/**
	 *
	 * @param string $id Name of the route (need to be uniq, a second route with same name will override the first)
	 * @param string $rule Url rule
	 * @param string $controller Controller to call if request uri match the rule
	 */
	public function addRoute($route_id, $rule, $controller, $keywords = array())
	{
		$regexp = preg_quote($rule, '#');
		if ($keywords)
		{
			$transform_keywords = array();
			preg_match_all('#\\\{(([^{}]+)\\\:)?('.implode('|', array_keys($keywords)).')(\\\:([^{}]+))?\\\}#', $regexp, $m);
			for ($i = 0, $total = count($m[0]); $i < $total; $i++)
			{
				$prepend = $m[2][$i];
				$keyword = $m[3][$i];
				$append = $m[5][$i];
				$transform_keywords[$keyword] = array(
					'required' =>	isset($keywords[$keyword]['param']),
					'prepend' =>	stripslashes($prepend),
					'append' =>		stripslashes($append),
				);

				if (isset($keywords[$keyword]['param']))
					$regexp = str_replace($m[0][$i], (($prepend) ? '('.$prepend.')?' : '').'(?P<'.$keywords[$keyword]['param'].'>'.$keywords[$keyword]['regexp'].')'.(($append) ? '('.$append.')?' : ''), $regexp);
				else
					$regexp = str_replace($m[0][$i], (($prepend) ? '('.$prepend.')?' : '').'('.$keywords[$keyword]['regexp'].')'.(($append) ? '('.$append.')?' : ''), $regexp);

			}
			$keywords = $transform_keywords;
		}

		$regexp = '#^/'.$regexp.'#u';
		$this->routes[$route_id] = array(
			'rule' =>		$rule,
			'regexp' =>		$regexp,
			'controller' =>	$controller,
			'keywords' =>	$keywords,
		);
	}

	/**
	 * Check if a keyword is written in a route rule
	 *
	 * @param string $route_id
	 * @param string $keyword
	 * @return bool
	 */
	public function hasKeyword($route_id, $keyword)
	{
		if (!isset($this->routes[$route_id]))
			return false;

		return preg_match('#\{([^{}]+:)?'.preg_quote($keyword, '#').'(:[^{}])?\}#', $this->routes[$route_id]['rule']);
	}

	/**
	 * Check if a route rule contain all required keywords of default route definition
	 *
	 * @param string $route_id
	 * @param string $rule Rule to verify
	 * @param array $errors List of missing keywords
	 */
	public function validateRoute($route_id, $rule, &$errors = array())
	{
		$errors = array();
		if (!isset($this->default_routes[$route_id]))
			return false;

		foreach ($this->default_routes[$route_id]['keywords'] as $keyword => $data)
			if (isset($data['param']) && !preg_match('#\{([^{}]+:)?'.$keyword.'(:[^{}])?\}#', $rule))
				$errors[] = $keyword;

		return (count($errors)) ? false : true;
	}

	/**
	 * Create an url from
	 *
	 * @param string $route_id Name the route
	 * @param array $params
	 * @param bool $use_routes If false, don't use to create this url
	 * @param string $anchor Optional anchor to add at the end of this url
	 */
	public function createUrl($route_id, $params = array(), $use_routes = true, $anchor = '')
	{
		if (!is_array($params))
			die('Dispatcher::createUrl() $params must be an array');

		if (!isset($this->routes[$route_id]))
		{
			$query = http_build_query($params);
			return ($route_id == 'index') ? 'index.php'.(($query) ? '?'.$query : '') : 'index.php?controller='.$route_id.(($query) ? '&'.$query : '').$anchor;
		}
		$route = $this->routes[$route_id];

		// Check required fields
		$query_params = array();
		foreach ($route['keywords'] as $key => $data)
		{
			if (!$data['required'])
				continue;

			if (!array_key_exists($key, $params))
				die('Dispatcher::createUrl() miss required parameter "'.$key.'" for route "'.$route_id.'"');
			$query_params[$this->default_routes[$route_id]['keywords'][$key]['param']] = $params[$key];
		}

		// Build an url which match a route
		if ($this->use_routes && $use_routes)
		{
			$url = $route['rule'];
			$add_param = array();
			foreach ($params as $key => $value)
			{
				if (!isset($route['keywords'][$key]))
				{
					if (!isset($this->default_routes[$route_id]['keywords'][$key]))
						$add_param[$key] = $value;
				}
				else
				{
					if ($params[$key])
						$replace = $route['keywords'][$key]['prepend'].$params[$key].$route['keywords'][$key]['append'];
					else
						$replace = '';
					$url = preg_replace('#\{([^{}]+:)?'.$key.'(:[^{}])?\}#', $replace, $url);
				}
			}
			$url = preg_replace('#\{([^{}]+:)?[a-z0-9_]+?(:[^{}])?\}#', '', $url);
			if (count($add_param))
				$url .= '?'.http_build_query($add_param);
		}
		// Build a classic url index.php?controller=foo&...
		else
			$url = 'index.php?controller='.$route['controller'].(($query_params) ? '&'.http_build_query($query_params) : '');

		return $url.$anchor;
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
		if ($this->use_routes && !$controller)
		{
			if (!$this->request_uri)
				return strtolower($this->controller_not_found);
			$controller = $this->default_controller;

			// Add empty route as last route to prevent this greedy regexp to match request uri before right time
			if ($this->empty_route)
				$this->addRoute($this->empty_route['routeID'], $this->empty_route['rule'], $this->empty_route['controller']);

			foreach ($this->routes as $route)
				if (preg_match($route['regexp'], $this->request_uri, $m))
				{
					// Route found ! Now fill $_GET with parameters of uri
					$controller = $route['controller'];
					foreach ($m as $k => $v)
						if (!is_numeric($k))
							$_GET[$k] = $v;
					break;
				}

			if ($controller == 'index')
				$controller = $this->default_controller;
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
	 * Get list of all available FO controllers
	 *
	 * @var mixed $dirs
	 * @return array
	 */
	public static function getControllers($dirs)
	{
		if (!is_array($dirs))
			$dirs = array($dirs);

		$controllers = array();
		foreach ($dirs as $dir)
			$controllers = array_merge($controllers, Dispatcher::getControllersInDirectory($dir));

		// Add default controllers
		$controllers['index'] = 'IndexController';
		if (isset($controllers['auth']))
			$controllers['authentication'] = $controllers['auth'];
		if (isset($controllers['compare']))
			$controllers['productscomparison'] = $controllers['compare'];

		return $controllers;
	}

	/**
	 * Get list of available controllers from the specified dir
	 *
	 * @param string dir directory to scan (recursively)
	 * @return array
	 */
	public static function getControllersInDirectory($dir)
	{
		$controllers = array();
		$controller_files = scandir($dir);
		foreach ($controller_files as $controller_filename)
		{
			if ($controller_filename[0] != '.')
			{
				if (is_dir($dir.$controller_filename))
					$controllers += Dispatcher::getControllersInDirectory($dir.$controller_filename.DIRECTORY_SEPARATOR);
				else if ($controller_filename != 'index.php')
				{
					$key = str_replace(array('controller.php', '.php'), array('', ''), strtolower($controller_filename));
					$controllers[$key] = basename($controller_filename, '.php');
				}
			}
		}

		return $controllers;
	}
}
