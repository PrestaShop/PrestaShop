<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
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
	 * List of available front controllers types
	 */
	const FC_FRONT = 1;
	const FC_ADMIN = 2;
	const FC_MODULE = 3;

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
				'categories' =>		array('regexp' => '[/a-zA-Z0-9-\pL]*'),
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
			'controller' =>	null,
			'rule' =>		'module/{module}{/:controller}',
			'keywords' => array(
				'module' =>			array('regexp' => '[a-zA-Z0-9_-]+', 'param' => 'module'),
				'controller' =>		array('regexp' => '[a-zA-Z0-9_-]+', 'param' => 'controller'),
			),
			'params' => array(
				'fc' => 'module',
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
	protected $default_controller = 'index';

	/**
	 * @var string Controller to use if found controller doesn't exist
	 */
	protected $controller_not_found = 'pagenotfound';

	/**
	 * @var string Front controller to use
	 */
	protected $front_controller = self::FC_FRONT;

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
		// Select right front controller
		if (defined('_PS_ADMIN_DIR_'))
		{
			$this->front_controller = self::FC_ADMIN;
			$this->controller_not_found = 'adminnotfound';
			$this->default_controller = 'adminhome';
		}
		else if (Tools::getValue('fc') == 'module')
		{
			$this->front_controller = self::FC_MODULE;
			$this->controller_not_found = 'pagenotfound';
			$this->default_controller = 'default';
		}
		else
		{
			$this->front_controller = self::FC_FRONT;
			$this->controller_not_found = 'pagenotfound';
			$this->default_controller = 'index';
		}

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

		// Get current controller
		$this->getController();
		if (!$this->controller)
			$this->controller = $this->default_controller;

		// Dispatch with right front controller
		switch ($this->front_controller)
		{
			// Dispatch front office controller
			case self::FC_FRONT :
				$controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);

				$controllers['index'] = 'IndexController';
				if (isset($controllers['auth']))
					$controllers['authentication'] = $controllers['auth'];
				if (isset($controllers['compare']))
					$controllers['productscomparison'] = $controllers['compare'];
                if (isset($controllers['contact']))
                    $controllers['contactform'] = $controllers['contact'];

				if (!isset($controllers[$this->controller]))
					$this->controller = 'pagenotfound';
				$controller_class = $controllers[$this->controller];
			break;

			// Dispatch module controller for front office
			case self::FC_MODULE :
				$module_name = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
				$module = Module::getInstanceByName($module_name);
				$controller_class = 'PageNotFoundController';
				if (Validate::isLoadedObject($module) && $module->active)
				{
					$controllers = Dispatcher::getControllers(_PS_MODULE_DIR_.$module_name.'/controllers/front/');
					if (isset($controllers[$this->controller]))
					{
						include_once(_PS_MODULE_DIR_.$module_name.'/controllers/front/'.$this->controller.'.php');
						$controller_class = $module_name.$this->controller.'ModuleFrontController';
					}
				}
			break;

			// Dispatch back office controller + module back office controller
			case self::FC_ADMIN :
				$tab = Tab::getInstanceFromClassName($this->controller);
				$retrocompatibility_admin_tab = null;
				if ($tab->module)
				{
					if (file_exists(_PS_MODULE_DIR_.$tab->module.'/'.$tab->class_name.'.php'))
						$retrocompatibility_admin_tab = _PS_MODULE_DIR_.$tab->module.'/'.$tab->class_name.'.php';
					else
					{
						$controllers = Dispatcher::getControllers(_PS_MODULE_DIR_.$tab->module.'/controllers/admin/');
						if (!isset($controllers[$this->controller]))
						{
							$this->controller = 'adminnotfound';
							$controller_class = 'AdminNotFoundController';
						}
						else
						{
							include_once(_PS_MODULE_DIR_.$tab->module.'/controllers/admin/'.$this->controller.'.php');
							$controller_class = $controllers[$this->controller].'Controller';
						}
					}
				}
				else
				{
					$controllers = Dispatcher::getControllers(array(_PS_ADMIN_DIR_.'/tabs/', _PS_ADMIN_CONTROLLER_DIR_));
					if (!isset($controllers[$this->controller]))
						$this->controller = 'adminnotfound';
					$controller_class = $controllers[$this->controller];

					if (file_exists(_PS_ADMIN_DIR_.'/tabs/'.$controller_class.'.php'))
						$retrocompatibility_admin_tab = _PS_ADMIN_DIR_.'/tabs/'.$controller_class.'.php';
				}

				// @retrocompatibility with admin/tabs/ old system
				if ($retrocompatibility_admin_tab)
				{
					include_once($retrocompatibility_admin_tab);
					include_once(_PS_ADMIN_DIR_.'/functions.php');
					runAdminTab($this->controller, !empty($_REQUEST['ajaxMode']));
					return;
				}
			break;

			default :
				throw new PrestaShopException('Bad front controller chosen');
		}

		// Instantiate controller
		try
		{
			Controller::getController($controller_class)->run();
		}
		catch (PrestaShopException $e)
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
			$this->addRoute(
				$id,
				$route['rule'],
				$route['controller'],
				$route['keywords'],
				isset($route['params']) ? $route['params'] : array()
			);

		if ($this->use_routes)
		{
			// Load routes from meta table
			$sql = 'SELECT m.page, ml.url_rewrite
					FROM `'._DB_PREFIX_.'meta` m
					LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta'.Shop::addSqlRestrictionOnLang('ml').')
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
					$this->addRoute(
						$route_id,
						$custom_route,
						$route_data['controller'],
						$route_data['keywords'],
						isset($route_data['params']) ? $route_data['params'] : array()
					);
		}
	}

	/**
	 *
	 * @param string $id Name of the route (need to be uniq, a second route with same name will override the first)
	 * @param string $rule Url rule
	 * @param string $controller Controller to call if request uri match the rule
	 */
	public function addRoute($route_id, $rule, $controller, array $keywords = array(), array $params = array())
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

		$regexp = '#^/'.$regexp.'(\?.*)?$#u';
		$this->routes[$route_id] = array(
			'rule' =>		$rule,
			'regexp' =>		$regexp,
			'controller' =>	$controller,
			'keywords' =>	$keywords,
			'params' =>		$params,
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
	public function createUrl($route_id, array $params = array(), $use_routes = true, $anchor = '')
	{
		if (!isset($this->routes[$route_id]))
		{
			$query = http_build_query($params);
			return ($route_id == 'index') ? 'index.php'.(($query) ? '?'.$query : '') : 'index.php?controller='.$route_id.(($query) ? '&'.$query : '').$anchor;
		}
		$route = $this->routes[$route_id];

		// Check required fields
		$query_params = isset($route['params']) ? $route['params'] : array();
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
		{
			$add_params = array();
			foreach ($params as $key => $value)
				if (!isset($route['keywords'][$key]) && !isset($this->default_routes[$route_id]['keywords'][$key]))
					$add_params[$key] = $value;

			if (!empty($route['controller']))
				$query_params['controller'] = $route['controller'];
			$url = 'index.php?'.http_build_query(array_merge($add_params, $query_params));
		}

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

		if (isset($controller) && is_string($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m))
		{
			$controller = $m[1];
			if (isset($_GET['controller']))
				$_GET[$m[2]] = $m[3];
			else if (isset($_POST['controller']))
				$_POST[$m[2]] = $m[3];
		}

		if (!Validate::isControllerName($controller))
			$controller = false;

		// Use routes ? (for url rewriting)
		if ($this->use_routes && !$controller)
		{
			if (!$this->request_uri)
				return strtolower($this->controller_not_found);
			$controller = $this->controller_not_found;

			// Add empty route as last route to prevent this greedy regexp to match request uri before right time
			if ($this->empty_route)
				$this->addRoute($this->empty_route['routeID'], $this->empty_route['rule'], $this->empty_route['controller']);

			foreach ($this->routes as $route)
				if (preg_match($route['regexp'], $this->request_uri, $m))
				{
					// Route found ! Now fill $_GET with parameters of uri
					foreach ($m as $k => $v)
						if (!is_numeric($k))
							$_GET[$k] = $v;

					$controller = $route['controller'] ? $route['controller'] : $_GET['controller'];
					if (!empty($route['params']))
						foreach ($route['params'] as $k => $v)
							$_GET[$k] = $v;

					if (isset($_GET['fc']) && $_GET['fc'] == 'module')
						$this->front_controller = self::FC_MODULE;
					break;
				}

			if ($controller == 'index' || $this->request_uri == '/index.php')
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
		if (!is_dir($dir))
			return array();

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
