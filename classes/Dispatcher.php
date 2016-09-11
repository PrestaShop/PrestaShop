<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class DispatcherCore
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
        'category_rule' => array(
            'controller' => 'category',
            'rule' => '{id}-{rewrite}',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_category'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'supplier_rule' => array(
            'controller' => 'supplier',
            'rule' => '{id}__{rewrite}',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_supplier'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'manufacturer_rule' => array(
            'controller' => 'manufacturer',
            'rule' => '{id}_{rewrite}',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_manufacturer'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'cms_rule' => array(
            'controller' => 'cms',
            'rule' => 'content/{id}-{rewrite}',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_cms'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'cms_category_rule' => array(
            'controller' => 'cms',
            'rule' => 'content/category/{id}-{rewrite}',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_cms_category'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
        'module' => array(
            'controller' => null,
            'rule' => 'module/{module}{/:controller}',
            'keywords' => array(
                'module' => array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'),
                'controller' => array('regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'),
            ),
            'params' => array(
                'fc' => 'module',
            ),
        ),
        'product_rule' => array(
            'controller' => 'product',
            'rule' => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_product'),
                'id_product_attribute' => array('regexp' => '[0-9]+', 'param' => 'id_product_attribute'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'ean13' => array('regexp' => '[0-9\pL]*'),
                'category' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'categories' => array('regexp' => '[/_a-zA-Z0-9-\pL]*'),
                'reference' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'manufacturer' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'supplier' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'price' => array('regexp' => '[0-9\.,]*'),
                'tags' => array('regexp' => '[a-zA-Z0-9-\pL]*'),
            ),
        ),
        /* Must be after the product and category rules in order to avoid conflict */
        'layered_rule' => array(
            'controller' => 'category',
            'rule' => '{id}-{rewrite}{/:selected_filters}',
            'keywords' => array(
                'id' => array('regexp' => '[0-9]+', 'param' => 'id_category'),
                /* Selected filters is used by the module blocklayered */
                'selected_filters' => array('regexp' => '.*', 'param' => 'selected_filters'),
                'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
            ),
        ),
    );

    /**
     * @var bool If true, use routes to build URL (mod rewrite must be activated)
     */
    protected $use_routes = false;

    protected $multilang_activated = false;

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
    protected $default_controller;
    protected $use_default_controller = false;

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
        if (!self::$instance) {
            self::$instance = new Dispatcher();
        }

        return self::$instance;
    }

    /**
     * Need to be instancied from getInstance() method
     */
    protected function __construct()
    {
        $this->use_routes = (bool) Configuration::get('PS_REWRITING_SETTINGS');

        // Select right front controller
        if (defined('_PS_ADMIN_DIR_')) {
            $this->front_controller = self::FC_ADMIN;
            $this->controller_not_found = 'adminnotfound';
        } elseif (Tools::getValue('fc') == 'module') {
            $this->front_controller = self::FC_MODULE;
            $this->controller_not_found = 'pagenotfound';
        } else {
            $this->front_controller = self::FC_FRONT;
            $this->controller_not_found = 'pagenotfound';
        }

        $this->setRequestUri();

        // Switch language if needed (only on front)
        if (in_array($this->front_controller, array(self::FC_FRONT, self::FC_MODULE))) {
            Tools::switchLanguage();
        }

        if (Language::isMultiLanguageActivated()) {
            $this->multilang_activated = true;
        }

        $this->loadRoutes();
    }

    /**
     * Use the default controller
     *
     * @return false|null|string
     */
    public function useDefaultController()
    {
        $this->use_default_controller = true;
        if ($this->default_controller === null) {
            if (defined('_PS_ADMIN_DIR_')) {
                if (isset(Context::getContext()->employee) && Validate::isLoadedObject(Context::getContext()->employee) && isset(Context::getContext()->employee->default_tab)) {
                    $this->default_controller = Tab::getClassNameById((int)Context::getContext()->employee->default_tab);
                }
                if (empty($this->default_controller)) {
                    $this->default_controller = 'AdminDashboard';
                }
            } elseif (Tools::getValue('fc') == 'module') {
                $this->default_controller = 'default';
            } else {
                $this->default_controller = 'index';
            }
        }

        return $this->default_controller;
    }

    /**
     * Find the controller and instantiate it
     */
    public function dispatch()
    {
        $controllerClass = '';

        // Get current controller
        $this->getController();
        if (!$this->controller) {
            $this->controller = $this->useDefaultController();
        }
        // Dispatch with right front controller
        switch ($this->front_controller) {
            // Dispatch front office controller
            case self::FC_FRONT:
                $controllers = Dispatcher::getControllers(array(_PS_FRONT_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/front/'));
                $controllers['index'] = 'IndexController';
                if (isset($controllers['auth'])) {
                    $controllers['authentication'] = $controllers['auth'];
                }
                if (isset($controllers['contact'])) {
                    $controllers['contactform'] = $controllers['contact'];
                }

                if (!isset($controllers[strtolower($this->controller)])) {
                    $this->controller = $this->controller_not_found;
                }
                $controllerClass = $controllers[strtolower($this->controller)];
                $paramsHookActionDispatcher = array('controller_type' => self::FC_FRONT, 'controller_class' => $controllerClass, 'is_module' => 0);
                break;

            // Dispatch module controller for front office
            case self::FC_MODULE:
                $moduleName = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
                $module = Module::getInstanceByName($moduleName);
                $controllerClass = 'PageNotFoundController';
                if (Validate::isLoadedObject($module) && $module->active) {
                    $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_.$moduleName.'/controllers/front/');
                    if (isset($controllers[strtolower($this->controller)])) {
                        include_once(_PS_MODULE_DIR_.$moduleName.'/controllers/front/'.$this->controller.'.php');
                        $controllerClass = $moduleName.$this->controller.'ModuleFrontController';
                    }
                }
                $paramsHookActionDispatcher = array('controller_type' => self::FC_FRONT, 'controller_class' => $controllerClass, 'is_module' => 1);
                break;

            // Dispatch back office controller + module back office controller
            case self::FC_ADMIN:
                if ($this->use_default_controller && !Tools::getValue('token') && Validate::isLoadedObject(Context::getContext()->employee) && Context::getContext()->employee->isLoggedBack()) {
                    Tools::redirectAdmin('index.php?controller='.$this->controller.'&token='.Tools::getAdminTokenLite($this->controller));
                }

                $tab = Tab::getInstanceFromClassName($this->controller, Configuration::get('PS_LANG_DEFAULT'));
                if ($tab->module) {
                    $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_.$tab->module.'/controllers/admin/');
                    if (!isset($controllers[strtolower($this->controller)])) {
                        $this->controller = $this->controller_not_found;
                        $controllerClass = 'AdminNotFoundController';
                    } else {
                        // Controllers in modules can be named AdminXXX.php or AdminXXXController.php
                        include_once(_PS_MODULE_DIR_.$tab->module.'/controllers/admin/'.$controllers[strtolower($this->controller)].'.php');
                        $controllerClass = $controllers[strtolower($this->controller)].(strpos($controllers[strtolower($this->controller)], 'Controller') ? '' : 'Controller');
                    }

                    $paramsHookActionDispatcher = array('controller_type' => self::FC_ADMIN, 'controller_class' => $controllerClass, 'is_module' => 1);
                } else {
                    $controllers = Dispatcher::getControllers(array(_PS_ADMIN_DIR_.'/tabs/', _PS_ADMIN_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/admin/'));
                    if (!isset($controllers[strtolower($this->controller)])) {
                        // If this is a parent tab, load the first child
                        if (Validate::isLoadedObject($tab) && $tab->id_parent == 0 && ($tabs = Tab::getTabs(Context::getContext()->language->id, $tab->id)) && isset($tabs[0])) {
                            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($tabs[0]['class_name']));
                        }
                        $this->controller = $this->controller_not_found;
                    }

                    $controllerClass = $controllers[strtolower($this->controller)];
                    $paramsHookActionDispatcher = array('controller_type' => self::FC_ADMIN, 'controller_class' => $controllerClass, 'is_module' => 0);
                }
                break;

            default:
                throw new PrestaShopException('Bad front controller chosen');
        }

        // Instantiate controller
        try {
            // Loading controller
            $controller = Controller::getController($controllerClass);

            // Execute hook dispatcher
            if (isset($paramsHookActionDispatcher)) {
                Hook::exec('actionDispatcher', $paramsHookActionDispatcher);
            }

            // Running controller
            $controller->run();
        } catch (PrestaShopException $e) {
            $e->displayMessage();
        }
    }

    /**
     * Set request uri and iso lang
     */
    protected function setRequestUri()
    {
        // Get request uri (HTTP_X_REWRITE_URL is used by IIS)
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->request_uri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $this->request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        $this->request_uri = rawurldecode($this->request_uri);

        if (isset(Context::getContext()->shop) && is_object(Context::getContext()->shop)) {
            $this->request_uri = preg_replace('#^'.preg_quote(Context::getContext()->shop->getBaseURI(), '#').'#i', '/', $this->request_uri);
        }

        // If there are several languages, get language from uri
        if ($this->use_routes && Language::isMultiLanguageActivated()) {
            if (preg_match('#^/([a-z]{2})(?:/.*)?$#', $this->request_uri, $m)) {
                $_GET['isolang'] = $m[1];
                $this->request_uri = substr($this->request_uri, 3);
            }
        }
    }

    /**
     * Load default routes group by languages
     */
    protected function loadRoutes($idShop = null)
    {
        $context = Context::getContext();

        // Load custom routes from modules
        $modulesRoutes = Hook::exec('moduleRoutes', array('id_shop' => $idShop), null, true, false);
        if (is_array($modulesRoutes) && count($modulesRoutes)) {
            foreach ($modulesRoutes as $moduleRoute) {
                if (is_array($moduleRoute) && count($moduleRoute)) {
                    foreach ($moduleRoute as $route => $routeDetails) {
                        if (array_key_exists('controller', $routeDetails) && array_key_exists('rule', $routeDetails)
                            && array_key_exists('keywords', $routeDetails) && array_key_exists('params', $routeDetails)) {
                            if (!isset($this->default_routes[$route])) {
                                $this->default_routes[$route] = array();
                            }
                            $this->default_routes[$route] = array_merge($this->default_routes[$route], $routeDetails);
                        }
                    }
                }
            }
        }

        $languageIds = Language::getIDs();

        if (isset($context->language) && !in_array($context->language->id, $languageIds)) {
            $languageIds[] = (int) $context->language->id;
        }

        // Set default routes
        foreach ($languageIds as $idLang) {
            foreach ($this->default_routes as $id => $route) {
                $this->addRoute(
                    $id,
                    $route['rule'],
                    $route['controller'],
                    $idLang,
                    $route['keywords'],
                    isset($route['params']) ? $route['params'] : array(),
                    $idShop
                );
            }
        }

        // Load the custom routes prior the defaults to avoid infinite loops
        if ($this->use_routes) {
            // Load routes from meta table
            $sql = 'SELECT m.page, ml.url_rewrite, ml.id_lang
					FROM `'._DB_PREFIX_.'meta` m
					LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta'.Shop::addSqlRestrictionOnLang('ml', (int) $idShop).')
					ORDER BY LENGTH(ml.url_rewrite) DESC';
            if ($results = Db::getInstance()->executeS($sql)) {
                foreach ($results as $row) {
                    if ($row['url_rewrite']) {
                        $this->addRoute($row['page'], $row['url_rewrite'], $row['page'], $row['id_lang'], array(), array(), $idShop);
                    }
                }
            }

            // Set default empty route if no empty route (that's weird I know)
            if (!$this->empty_route) {
                $this->empty_route = array(
                    'routeID' =>    'index',
                    'rule' =>        '',
                    'controller' =>    'index',
                );
            }

            // Load custom routes
            foreach ($this->default_routes as $routeId => $routeData) {
                if ($customRoute = Configuration::get('PS_ROUTE_'.$routeId, null, null, $idShop)) {
                    if (isset($context->language) && !in_array($context->language->id, $languageIds)) {
                        $languageIds[] = (int) $context->language->id;
                    }

                    foreach ($languageIds as $idLang) {
                        $this->addRoute(
                            $routeId,
                            $customRoute,
                            $routeData['controller'],
                            $idLang,
                            $routeData['keywords'],
                            isset($routeData['params']) ? $routeData['params'] : array(),
                            $idShop
                        );
                    }
                }
            }
        }
    }

    /**
     *
     * @param string $routeId Name of the route (need to be uniq, a second route with same name will override the first)
     * @param string $rule Url rule
     * @param string $controller Controller to call if request uri match the rule
     * @param int    $idLang
     * @param int    $idShop
     */
    public function addRoute($routeId, $rule, $controller, $idLang = null, array $keywords = array(), array $params = array(), $idShop = null)
    {
        if (isset(Context::getContext()->language) && $idLang === null) {
            $idLang = (int) Context::getContext()->language->id;
        }

        if (isset(Context::getContext()->shop) && $idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        $regexp = preg_quote($rule, '#');
        if ($keywords) {
            $transformKeywords = array();
            preg_match_all('#\\\{(([^{}]*)\\\:)?('.implode('|', array_keys($keywords)).')(\\\:([^{}]*))?\\\}#', $regexp, $m);
            for ($i = 0, $total = count($m[0]); $i < $total; $i++) {
                $prepend = $m[2][$i];
                $keyword = $m[3][$i];
                $append = $m[5][$i];
                $transformKeywords[$keyword] = array(
                    'required' =>    isset($keywords[$keyword]['param']),
                    'prepend' =>    stripslashes($prepend),
                    'append' =>        stripslashes($append),
                );

                $prependRegexp = $appendRegexp = '';
                if ($prepend || $append) {
                    $prependRegexp = '('.$prepend;
                    $appendRegexp = $append.')?';
                }

                if (isset($keywords[$keyword]['param'])) {
                    $regexp = str_replace($m[0][$i], $prependRegexp.'(?P<'.$keywords[$keyword]['param'].'>'.$keywords[$keyword]['regexp'].')'.$appendRegexp, $regexp);
                } else {
                    $regexp = str_replace($m[0][$i], $prependRegexp.'('.$keywords[$keyword]['regexp'].')'.$appendRegexp, $regexp);
                }
            }
            $keywords = $transformKeywords;
        }

        $regexp = '#^/'.$regexp.'$#u';
        if (!isset($this->routes[$idShop])) {
            $this->routes[$idShop] = array();
        }
        if (!isset($this->routes[$idShop][$idLang])) {
            $this->routes[$idShop][$idLang] = array();
        }

        $this->routes[$idShop][$idLang][$routeId] = array(
            'rule' =>        $rule,
            'regexp' =>        $regexp,
            'controller' =>    $controller,
            'keywords' =>    $keywords,
            'params' =>        $params,
        );
    }

    /**
     * Check if a route exists
     *
     * @param string $route_id
     * @param int    $idLang
     * @param int    $idShop
     *
*@return bool
     */
    public function hasRoute($route_id, $idLang = null, $idShop = null)
    {
        if (isset(Context::getContext()->language) && $idLang === null) {
            $idLang = (int) Context::getContext()->language->id;
        }
        if (isset(Context::getContext()->shop) && $idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        return isset($this->routes[$idShop]) && isset($this->routes[$idShop][$idLang]) && isset($this->routes[$idShop][$idLang][$route_id]);
    }

    /**
     * Check if a keyword is written in a route rule
     *
     * @param string $routeId
     * @param int    $idLang
     * @param string $keyword
     * @param int    $idShop
     *
     * @return bool
     */
    public function hasKeyword($routeId, $idLang, $keyword, $idShop = null)
    {
        if ($idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        if (!isset($this->routes[$idShop])) {
            $this->loadRoutes($idShop);
        }

        if (!isset($this->routes[$idShop]) || !isset($this->routes[$idShop][$idLang]) || !isset($this->routes[$idShop][$idLang][$routeId])) {
            return false;
        }

        return preg_match('#\{([^{}]*:)?'.preg_quote($keyword, '#').'(:[^{}]*)?\}#', $this->routes[$idShop][$idLang][$routeId]['rule']);
    }

    /**
     * Check if a route rule contain all required keywords of default route definition
     *
     * @param string $routeId
     * @param string $rule   Rule to verify
     * @param array  $errors List of missing keywords
     *
     * @return bool
     */
    public function validateRoute($routeId, $rule, &$errors = array())
    {
        $errors = array();
        if (!isset($this->default_routes[$routeId])) {
            return false;
        }

        foreach ($this->default_routes[$routeId]['keywords'] as $keyword => $data) {
            if (isset($data['param']) && !preg_match('#\{([^{}]*:)?'.$keyword.'(:[^{}]*)?\}#', $rule)) {
                $errors[] = $keyword;
            }
        }

        return (count($errors)) ? false : true;
    }

    /**
     * Create an url from
     *
     * @param string $routeId    Name the route
     * @param int    $idLang     Language ID
     * @param array  $params
     * @param bool   $use_routes If false, don't use to create this url
     * @param string $anchor     Optional anchor to add at the end of this url
     *
     * @return string
     */
    public function createUrl($routeId, $idLang = null, array $params = array(), $forceRoutes = false, $anchor = '', $idShop = null)
    {
        if ($idLang === null) {
            $idLang = (int) Context::getContext()->language->id;
        }
        if ($idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        if (!isset($this->routes[$idShop])) {
            $this->loadRoutes($idShop);
        }

        if (!isset($this->routes[$idShop][$idLang][$routeId])) {
            $query = http_build_query($params, '', '&');
            $indexLink = $this->use_routes ? '' : 'index.php';

            return ($routeId == 'index') ? $indexLink.(($query) ? '?'.$query : '') : ((trim($routeId) == '') ? '' : 'index.php?controller='.$routeId).(($query) ? '&'.$query : '').$anchor;
        }
        $route = $this->routes[$idShop][$idLang][$routeId];
        // Check required fields
        $queryParams = isset($route['params']) ? $route['params'] : array();
        foreach ($route['keywords'] as $key => $data) {
            if (!$data['required']) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                throw new PrestaShopException('Dispatcher::createUrl() miss required parameter "'.$key.'" for route "'.$routeId.'"');
            }
            if (isset($this->default_routes[$routeId])) {
                $queryParams[$this->default_routes[$routeId]['keywords'][$key]['param']] = $params[$key];
            }
        }

        // Build an url which match a route
        if ($this->use_routes || $forceRoutes) {
            $url = $route['rule'];
            $addParam = array();

            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key])) {
                    if (!isset($this->default_routes[$routeId]['keywords'][$key])) {
                        $addParam[$key] = $value;
                    }
                } else {
                    if ($params[$key]) {
                        $replace = $route['keywords'][$key]['prepend'].$params[$key].$route['keywords'][$key]['append'];
                    } else {
                        $replace = '';
                    }
                    $url = preg_replace('#\{([^{}]*:)?'.$key.'(:[^{}]*)?\}#', $replace, $url);
                }
            }
            $url = preg_replace('#\{([^{}]*:)?[a-z0-9_]+?(:[^{}]*)?\}#', '', $url);
            if (count($addParam)) {
                $url .= '?'.http_build_query($addParam, '', '&');
            }
        } else {
            // Build a classic url index.php?controller=foo&...
            $addParams = array();
            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key]) && !isset($this->default_routes[$routeId]['keywords'][$key])) {
                    $addParams[$key] = $value;
                }
            }

            if (!empty($route['controller'])) {
                $queryParams['controller'] = $route['controller'];
            }
            $query = http_build_query(array_merge($addParams, $queryParams), '', '&');
            if ($this->multilang_activated) {
                $query .= (!empty($query) ? '&' : '').'id_lang='.(int) $idLang;
            }
            $url = 'index.php?'.$query;
        }

        return $url.$anchor;
    }

    /**
     * Retrieve the controller from url or request uri if routes are activated
     *
     * @return string
     */
    public function getController($idShop = null)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $_GET['controllerUri'] = Tools::getValue('controller');
        }
        if ($this->controller) {
            $_GET['controller'] = $this->controller;

            return $this->controller;
        }

        if (isset(Context::getContext()->shop) && $idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        $controller = Tools::getValue('controller');

        if (isset($controller) && is_string($controller) && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m)) {
            $controller = $m[1];
            if (isset($_GET['controller'])) {
                $_GET[$m[2]] = $m[3];
            } elseif (isset($_POST['controller'])) {
                $_POST[$m[2]] = $m[3];
            }
        }

        if (!Validate::isControllerName($controller)) {
            $controller = false;
        }

        // Use routes ? (for url rewriting)
        if ($this->use_routes && !$controller && !defined('_PS_ADMIN_DIR_')) {
            if (!$this->request_uri) {
                return strtolower($this->controller_not_found);
            }
            $controller = $this->controller_not_found;
            $testRequestUri = preg_replace('/(=http:\/\/)/', '=', $this->request_uri);

            // If the request_uri matches a static file, then there is no need to check the routes, we keep "controller_not_found" (a static file should not go through the dispatcher)
            if (!preg_match('/\.(gif|jpe?g|png|css|js|ico)$/i', parse_url($testRequestUri, PHP_URL_PATH))) {
                // Add empty route as last route to prevent this greedy regexp to match request uri before right time
                if ($this->empty_route) {
                    $this->addRoute($this->empty_route['routeID'], $this->empty_route['rule'], $this->empty_route['controller'], Context::getContext()->language->id, array(), array(), $idShop);
                }

                list($uri) = explode('?', $this->request_uri);

                if (isset($this->routes[$idShop][Context::getContext()->language->id])) {
                    foreach ($this->routes[$idShop][Context::getContext()->language->id] as $route) {
                        if (preg_match($route['regexp'], $uri, $m)) {
                            // Route found ! Now fill $_GET with parameters of uri
                            foreach ($m as $k => $v) {
                                if (!is_numeric($k)) {
                                    $_GET[$k] = $v;
                                }
                            }

                            $controller = $route['controller'] ? $route['controller'] : $_GET['controller'];
                            if (!empty($route['params'])) {
                                foreach ($route['params'] as $k => $v) {
                                    $_GET[$k] = $v;
                                }
                            }

                            // A patch for module friendly urls
                            if (preg_match('#module-([a-z0-9_-]+)-([a-z0-9_]+)$#i', $controller, $m)) {
                                $_GET['module'] = $m[1];
                                $_GET['fc'] = 'module';
                                $controller = $m[2];
                            }

                            if (isset($_GET['fc']) && $_GET['fc'] == 'module') {
                                $this->front_controller = self::FC_MODULE;
                            }
                            break;
                        }
                    }
                }
            }

            if ($controller == 'index' || preg_match('/^\/index.php(?:\?.*)?$/', $this->request_uri)) {
                $controller = $this->useDefaultController();
            }
        }

        $this->controller = str_replace('-', '', $controller);
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
        if (!is_array($dirs)) {
            $dirs = array($dirs);
        }

        $controllers = array();
        foreach ($dirs as $dir) {
            $controllers = array_merge($controllers, Dispatcher::getControllersInDirectory($dir));
        }

        return $controllers;
    }

    /**
     * Get list of all available Module Front controllers
     *
     * @return array
     */
    public static function getModuleControllers($type = 'all', $module = null)
    {
        $modulesControllers = array();
        if (is_null($module)) {
            $modules = Module::getModulesOnDisk(true);
        } elseif (!is_array($module)) {
            $modules = array(Module::getInstanceByName($module));
        } else {
            $modules = array();
            foreach ($module as $_mod) {
                $modules[] = Module::getInstanceByName($_mod);
            }
        }

        foreach ($modules as $mod) {
            foreach (Dispatcher::getControllersInDirectory(_PS_MODULE_DIR_.$mod->name.'/controllers/') as $controller) {
                if ($type == 'admin') {
                    if (strpos($controller, 'Admin') !== false) {
                        $modulesControllers[$mod->name][] = $controller;
                    }
                } elseif ($type == 'front') {
                    if (strpos($controller, 'Admin') === false) {
                        $modulesControllers[$mod->name][] = $controller;
                    }
                } else {
                    $modulesControllers[$mod->name][] = $controller;
                }
            }
        }

        return $modulesControllers;
    }

    /**
     * Get list of available controllers from the specified dir
     *
     * @param string $dir Directory to scan (recursively)
     * @return array
     */
    public static function getControllersInDirectory($dir)
    {
        if (!is_dir($dir)) {
            return array();
        }

        $controllers = array();
        $controllerFiles = scandir($dir);
        foreach ($controllerFiles as $controllerFilename) {
            if ($controllerFilename[0] != '.') {
                if (!strpos($controllerFilename, '.php') && is_dir($dir.$controllerFilename)) {
                    $controllers += Dispatcher::getControllersInDirectory($dir.$controllerFilename.DIRECTORY_SEPARATOR);
                } elseif ($controllerFilename != 'index.php') {
                    $key = str_replace(array('controller.php', '.php'), '', strtolower($controllerFilename));
                    $controllers[$key] = basename($controllerFilename, '.php');
                }
            }
        }

        return $controllers;
    }
}
