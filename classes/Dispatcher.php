<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * @since 1.5.0
 */
class DispatcherCore
{
    /**
     * List of available front controllers types.
     */
    const FC_FRONT = 1;
    const FC_ADMIN = 2;
    const FC_MODULE = 3;

    const REWRITE_PATTERN = '[_a-zA-Z0-9\x{0600}-\x{06FF}\pL\pS-]*?';

    /**
     * @var Dispatcher
     */
    public static $instance = null;

    /**
     * @var SymfonyRequest
     */
    private $request;

    /**
     * @var array List of default routes
     */
    public $default_routes = [
        'category_rule' => [
            'controller' => 'category',
            'rule' => '{id}-{rewrite}',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
            ],
        ],
        'supplier_rule' => [
            'controller' => 'supplier',
            'rule' => 'supplier/{id}-{rewrite}',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_supplier'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
            ],
        ],
        'manufacturer_rule' => [
            'controller' => 'manufacturer',
            'rule' => 'brand/{id}-{rewrite}',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_manufacturer'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
            ],
        ],
        'cms_rule' => [
            'controller' => 'cms',
            'rule' => 'content/{id}-{rewrite}',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_cms'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
            ],
        ],
        'cms_category_rule' => [
            'controller' => 'cms',
            'rule' => 'content/category/{id}-{rewrite}',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_cms_category'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
            ],
        ],
        'module' => [
            'controller' => null,
            'rule' => 'module/{module}{/:controller}',
            'keywords' => [
                'module' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'module'],
                'controller' => ['regexp' => '[_a-zA-Z0-9_-]+', 'param' => 'controller'],
            ],
            'params' => [
                'fc' => 'module',
            ],
        ],
        'product_rule' => [
            'controller' => 'product',
            'rule' => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_product'],
                'id_product_attribute' => ['regexp' => '[0-9]+', 'param' => 'id_product_attribute'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN, 'param' => 'rewrite'],
                'ean13' => ['regexp' => '[0-9\pL]*'],
                'category' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'categories' => ['regexp' => '[/_a-zA-Z0-9-\pL]*'],
                'reference' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'manufacturer' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'supplier' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'price' => ['regexp' => '[0-9\.,]*'],
                'tags' => ['regexp' => '[a-zA-Z0-9-\pL]*'],
            ],
        ],
        /* Must be after the product and category rules in order to avoid conflict */
        'layered_rule' => [
            'controller' => 'category',
            'rule' => '{id}-{rewrite}{/:selected_filters}',
            'keywords' => [
                'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                /* Selected filters is used by the module blocklayered */
                'selected_filters' => ['regexp' => '.*', 'param' => 'selected_filters'],
                'rewrite' => ['regexp' => self::REWRITE_PATTERN],
                'meta_keywords' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
                'meta_title' => ['regexp' => '[_a-zA-Z0-9-\pL]*'],
            ],
        ],
    ];

    /**
     * @var bool If true, use routes to build URL (mod rewrite must be activated)
     */
    protected $use_routes = false;

    protected $multilang_activated = false;

    /**
     * @var array List of loaded routes
     */
    protected $routes = [];

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
     * Get current instance of dispatcher (singleton).
     *
     * @return Dispatcher
     *
     * @throws PrestaShopException
     */
    public static function getInstance(SymfonyRequest $request = null)
    {
        if (!self::$instance) {
            if (null === $request) {
                $request = SymfonyRequest::createFromGlobals();
            }
            self::$instance = new Dispatcher($request);
        }

        return self::$instance;
    }

    /**
     * Needs to be instantiated from getInstance() method.
     *
     * @param SymfonyRequest|null $request
     *
     * @throws PrestaShopException
     */
    protected function __construct(SymfonyRequest $request = null)
    {
        $this->setRequest($request);

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
        if (in_array($this->front_controller, [self::FC_FRONT, self::FC_MODULE])) {
            Tools::switchLanguage();
        }

        if (Language::isMultiLanguageActivated()) {
            $this->multilang_activated = true;
        }

        $this->loadRoutes();
    }

    /**
     * Either sets a given request or a new one.
     *
     * @param SymfonyRequest|null $request
     */
    private function setRequest(SymfonyRequest $request = null)
    {
        if (null === $request) {
            $request = SymfonyRequest::createFromGlobals();
        }

        $this->request = $request;
    }

    /**
     * Returns the request property.
     *
     * @return SymfonyRequest
     */
    private function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets and returns the default controller.
     *
     * @param int $frontControllerType The front controller type
     * @param Employee|null $employee The current employee
     *
     * @return string
     */
    private function getDefaultController($frontControllerType, Employee $employee = null)
    {
        switch ($frontControllerType) {
            case self::FC_ADMIN:
                // Default
                $defaultController = 'AdminDashboard';
                // If there is an employee with a default tab set
                if (null !== $employee) {
                    $tabClassName = $employee->getDefaultTabClassName();
                    if (null !== $tabClassName) {
                        $tabProfileAccess = Profile::getProfileAccess($employee->id_profile, Tab::getIdFromClassName($tabClassName));
                        if (is_array($tabProfileAccess) && isset($tabProfileAccess['view']) && $tabProfileAccess['view'] === '1') {
                            $defaultController = $tabClassName;
                        }
                    }
                }

                break;
            case self::FC_MODULE:
                $defaultController = 'default';

                break;
            default:
                $defaultController = 'index';
        }

        $this->setDefaultController($defaultController);

        return $defaultController;
    }

    /**
     * Sets the default controller.
     *
     * @param string $defaultController
     */
    private function setDefaultController($defaultController)
    {
        $this->default_controller = $defaultController;
    }

    /**
     * Sets use_default_controller to true, sets and returns the default controller.
     *
     * @return string
     */
    public function useDefaultController()
    {
        $this->use_default_controller = true;

        // If it was already set just return it
        if (null !== $this->default_controller) {
            return $this->default_controller;
        }

        $employee = Context::getContext()->employee;

        return $this->getDefaultController($this->front_controller, $employee);
    }

    /**
     * Find the controller and instantiate it.
     */
    public function dispatch()
    {
        $controller_class = '';

        // Get current controller
        $this->getController();
        if (!$this->controller) {
            $this->controller = $this->useDefaultController();
        }
        // Execute hook dispatcher before
        Hook::exec('actionDispatcherBefore', ['controller_type' => $this->front_controller]);

        // Dispatch with right front controller
        switch ($this->front_controller) {
            // Dispatch front office controller
            case self::FC_FRONT:
                $controllers = Dispatcher::getControllers([
                    _PS_FRONT_CONTROLLER_DIR_,
                    _PS_OVERRIDE_DIR_ . 'controllers/front/',
                ]);
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
                $controller_class = $controllers[strtolower($this->controller)];
                $params_hook_action_dispatcher = [
                    'controller_type' => self::FC_FRONT,
                    'controller_class' => $controller_class,
                    'is_module' => 0,
                ];

                break;

            // Dispatch module controller for front office
            case self::FC_MODULE:
                $module_name = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
                $module = Module::getInstanceByName($module_name);
                $controller_class = 'PageNotFoundController';
                if (Validate::isLoadedObject($module) && $module->active) {
                    $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_ . "$module_name/controllers/front/");
                    if (isset($controllers[strtolower($this->controller)])) {
                        include_once _PS_MODULE_DIR_ . "$module_name/controllers/front/{$this->controller}.php";
                        if (file_exists(
                            _PS_OVERRIDE_DIR_ . "modules/$module_name/controllers/front/{$this->controller}.php"
                        )) {
                            include_once _PS_OVERRIDE_DIR_ . "modules/$module_name/controllers/front/{$this->controller}.php";
                            $controller_class = $module_name . $this->controller . 'ModuleFrontControllerOverride';
                        } else {
                            $controller_class = $module_name . $this->controller . 'ModuleFrontController';
                        }
                    }
                }
                $params_hook_action_dispatcher = [
                    'controller_type' => self::FC_FRONT,
                    'controller_class' => $controller_class,
                    'is_module' => 1,
                ];

                break;

            // Dispatch back office controller + module back office controller
            case self::FC_ADMIN:
                if ($this->use_default_controller
                    && !Tools::getValue('token')
                    && Validate::isLoadedObject(Context::getContext()->employee)
                    && Context::getContext()->employee->isLoggedBack()
                ) {
                    Tools::redirectAdmin(
                        "index.php?controller={$this->controller}&token=" . Tools::getAdminTokenLite($this->controller)
                    );
                }

                $tab = Tab::getInstanceFromClassName($this->controller, Configuration::get('PS_LANG_DEFAULT'));
                $retrocompatibility_admin_tab = null;

                if ($tab->module) {
                    if (file_exists(_PS_MODULE_DIR_ . "{$tab->module}/{$tab->class_name}.php")) {
                        $retrocompatibility_admin_tab = _PS_MODULE_DIR_ . "{$tab->module}/{$tab->class_name}.php";
                    } else {
                        $controllers = Dispatcher::getControllers(_PS_MODULE_DIR_ . $tab->module . '/controllers/admin/');
                        if (!isset($controllers[strtolower($this->controller)])) {
                            $this->controller = $this->controller_not_found;
                            $controller_class = 'AdminNotFoundController';
                        } else {
                            $controller_name = $controllers[strtolower($this->controller)];
                            // Controllers in modules can be named AdminXXX.php or AdminXXXController.php
                            include_once _PS_MODULE_DIR_ . "{$tab->module}/controllers/admin/$controller_name.php";
                            if (file_exists(
                                _PS_OVERRIDE_DIR_ . "modules/{$tab->module}/controllers/admin/$controller_name.php"
                            )) {
                                include_once _PS_OVERRIDE_DIR_ . "modules/{$tab->module}/controllers/admin/$controller_name.php";
                                $controller_class = $controller_name . (
                                    strpos($controller_name, 'Controller') ? 'Override' : 'ControllerOverride'
                                );
                            } else {
                                $controller_class = $controller_name . (
                                    strpos($controller_name, 'Controller') ? '' : 'Controller'
                                );
                            }
                        }
                    }
                    $params_hook_action_dispatcher = [
                        'controller_type' => self::FC_ADMIN,
                        'controller_class' => $controller_class,
                        'is_module' => 1,
                    ];
                } else {
                    $controllers = Dispatcher::getControllers(
                        [
                            _PS_ADMIN_DIR_ . '/tabs/',
                            _PS_ADMIN_CONTROLLER_DIR_,
                            _PS_OVERRIDE_DIR_ . 'controllers/admin/',
                        ]
                    );
                    if (!isset($controllers[strtolower($this->controller)])) {
                        // If this is a parent tab, load the first child
                        if (Validate::isLoadedObject($tab)
                            && $tab->id_parent == 0
                            && ($tabs = Tab::getTabs(Context::getContext()->language->id, $tab->id))
                            && isset($tabs[0])
                        ) {
                            Tools::redirectAdmin(Context::getContext()->link->getAdminLink($tabs[0]['class_name']));
                        }
                        $this->controller = $this->controller_not_found;
                    }

                    $controller_class = $controllers[strtolower($this->controller)];
                    $params_hook_action_dispatcher = [
                        'controller_type' => self::FC_ADMIN,
                        'controller_class' => $controller_class,
                        'is_module' => 0,
                    ];

                    if (file_exists(_PS_ADMIN_DIR_ . '/tabs/' . $controller_class . '.php')) {
                        $retrocompatibility_admin_tab = _PS_ADMIN_DIR_ . '/tabs/' . $controller_class . '.php';
                    }
                }

                // @retrocompatibility with admin/tabs/ old system
                if ($retrocompatibility_admin_tab) {
                    include_once $retrocompatibility_admin_tab;
                    include_once _PS_ADMIN_DIR_ . '/functions.php';
                    runAdminTab($this->controller, !empty($_REQUEST['ajaxMode']));

                    return;
                }

                break;

            default:
                throw new PrestaShopException('Bad front controller chosen');
        }

        // Instantiate controller
        try {
            // Loading controller
            $controller = Controller::getController($controller_class);

            // Execute hook dispatcher
            if (isset($params_hook_action_dispatcher)) {
                Hook::exec('actionDispatcher', $params_hook_action_dispatcher);
            }

            // Running controller
            $controller->run();

            // Execute hook dispatcher after
            if (isset($params_hook_action_dispatcher)) {
                Hook::exec('actionDispatcherAfter', $params_hook_action_dispatcher);
            }
        } catch (PrestaShopException $e) {
            $e->displayMessage();
        }
    }

    /**
     * Sets request uri and if necessary $_GET['isolang'].
     */
    protected function setRequestUri()
    {
        $shop = Context::getContext()->shop;
        if (!Validate::isLoadedObject($shop)) {
            $shop = null;
        }

        $this->request_uri = $this->buildRequestUri(
            $this->getRequest()->getRequestUri(),
            Language::isMultiLanguageActivated(),
            $shop
        );
    }

    /**
     * Builds request URI and if necessary sets $_GET['isolang'].
     *
     * @param string $requestUri To retrieve the request URI from it
     * @param bool $isMultiLanguageActivated
     * @param Shop $shop
     *
     * @return string
     */
    private function buildRequestUri($requestUri, $isMultiLanguageActivated, Shop $shop = null)
    {
        // Decode raw request URI
        $requestUri = rawurldecode($requestUri);

        // Remove the shop base URI part from the request URI
        if (null !== $shop) {
            $requestUri = preg_replace(
                '#^' . preg_quote($shop->getBaseURI(), '#') . '#i',
                '/',
                $requestUri
            );
        }

        // If there are several languages, set $_GET['isolang'] and remove the language part from the request URI
        if (
            $this->use_routes &&
            $isMultiLanguageActivated &&
            preg_match('#^/([a-z]{2})(?:/.*)?$#', $requestUri, $matches)
        ) {
            $_GET['isolang'] = $matches[1];
            $requestUri = substr($requestUri, 3);
        }

        return $requestUri;
    }

    /**
     * Load default routes group by languages.
     *
     * @param int $id_shop
     */
    protected function loadRoutes($id_shop = null)
    {
        $context = Context::getContext();

        if (isset($context->shop) && $id_shop === null) {
            $id_shop = (int) $context->shop->id;
        }

        // Load custom routes from modules
        $modules_routes = Hook::exec('moduleRoutes', ['id_shop' => $id_shop], null, true, false);
        if (is_array($modules_routes) && count($modules_routes)) {
            foreach ($modules_routes as $module_route) {
                if (is_array($module_route) && count($module_route)) {
                    foreach ($module_route as $route => $route_details) {
                        if (array_key_exists('controller', $route_details)
                            && array_key_exists('rule', $route_details)
                            && array_key_exists('keywords', $route_details)
                            && array_key_exists('params', $route_details)
                        ) {
                            if (!isset($this->default_routes[$route])) {
                                $this->default_routes[$route] = [];
                            }
                            $this->default_routes[$route] = array_merge($this->default_routes[$route], $route_details);
                        }
                    }
                }
            }
        }

        $language_ids = Language::getIDs();

        if (isset($context->language) && !in_array($context->language->id, $language_ids)) {
            $language_ids[] = (int) $context->language->id;
        }

        // Set default routes
        foreach ($this->default_routes as $id => $route) {
            $route = $this->computeRoute(
                $route['rule'],
                $route['controller'],
                $route['keywords'],
                isset($route['params']) ? $route['params'] : []
            );
            foreach ($language_ids as $id_lang) {
                // the default routes are the same, whatever the language
                $this->routes[$id_shop][$id_lang][$id] = $route;
            }
        }

        // Load the custom routes prior the defaults to avoid infinite loops
        if ($this->use_routes) {
            // Load routes from meta table
            $sql = 'SELECT m.page, ml.url_rewrite, ml.id_lang
					FROM `' . _DB_PREFIX_ . 'meta` m
					LEFT JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON (m.id_meta = ml.id_meta' . Shop::addSqlRestrictionOnLang('ml', (int) $id_shop) . ')
					ORDER BY LENGTH(ml.url_rewrite) DESC';
            if ($results = Db::getInstance()->executeS($sql)) {
                foreach ($results as $row) {
                    if ($row['url_rewrite']) {
                        $this->addRoute(
                            $row['page'],
                            $row['url_rewrite'],
                            $row['page'],
                            $row['id_lang'],
                            [],
                            [],
                            $id_shop
                        );
                    }
                }
            }

            // Set default empty route if no empty route (that's weird I know)
            if (!$this->empty_route) {
                $this->empty_route = [
                    'routeID' => 'index',
                    'rule' => '',
                    'controller' => 'index',
                ];
            }

            // Load custom routes
            foreach ($this->default_routes as $route_id => $route_data) {
                if ($custom_route = Configuration::get('PS_ROUTE_' . $route_id, null, null, $id_shop)) {
                    if (isset($context->language) && !in_array($context->language->id, $language_ids)) {
                        $language_ids[] = (int) $context->language->id;
                    }

                    $route = $this->computeRoute(
                        $custom_route,
                        $route_data['controller'],
                        $route_data['keywords'],
                        isset($route_data['params']) ? $route_data['params'] : []
                    );
                    foreach ($language_ids as $id_lang) {
                        // those routes are the same, whatever the language
                        $this->routes[$id_shop][$id_lang][$route_id] = $route;
                    }
                }
            }
        }
    }

    /**
     * Create the route array, by computing the final regex & keywords.
     *
     * @param string $rule Url rule
     * @param string $controller Controller to call if request uri match the rule
     * @param array $keywords keywords associated with the route
     * @param array $params optional params of the route
     *
     * @return array
     */
    public function computeRoute($rule, $controller, array $keywords = [], array $params = [])
    {
        $regexp = preg_quote($rule, '#');
        if ($keywords) {
            $transform_keywords = [];
            preg_match_all(
                '#\\\{(([^{}]*)\\\:)?(' .
                implode('|', array_keys($keywords)) . ')(\\\:([^{}]*))?\\\}#',
                $regexp,
                $m
            );
            for ($i = 0, $total = count($m[0]); $i < $total; ++$i) {
                $prepend = $m[2][$i];
                $keyword = $m[3][$i];
                $append = $m[5][$i];
                $transform_keywords[$keyword] = [
                    'required' => isset($keywords[$keyword]['param']),
                    'prepend' => stripslashes($prepend),
                    'append' => stripslashes($append),
                ];

                $prepend_regexp = $append_regexp = '';
                if ($prepend || $append) {
                    $prepend_regexp = '(' . $prepend;
                    $append_regexp = $append . ')?';
                }

                if (isset($keywords[$keyword]['param'])) {
                    $regexp = str_replace(
                        $m[0][$i],
                        $prepend_regexp .
                        '(?P<' . $keywords[$keyword]['param'] . '>' . $keywords[$keyword]['regexp'] . ')' .
                        $append_regexp,
                        $regexp
                    );
                } else {
                    $regexp = str_replace(
                        $m[0][$i],
                        $prepend_regexp .
                        '(' . $keywords[$keyword]['regexp'] . ')' .
                        $append_regexp,
                        $regexp
                    );
                }
            }
            $keywords = $transform_keywords;
        }

        $regexp = '#^/' . $regexp . '$#u';

        return [
            'rule' => $rule,
            'regexp' => $regexp,
            'controller' => $controller,
            'keywords' => $keywords,
            'params' => $params,
        ];
    }

    /**
     * @param string $route_id Name of the route (need to be uniq,a second route with same name will override the first)
     * @param string $rule Url rule
     * @param string $controller Controller to call if request uri match the rule
     * @param int $id_lang
     * @param array $keywords
     * @param array $params
     * @param int $id_shop
     */
    public function addRoute(
        $route_id,
        $rule,
        $controller,
        $id_lang = null,
        array $keywords = [],
        array $params = [],
        $id_shop = null
    ) {
        $context = Context::getContext();

        if (isset($context->language) && $id_lang === null) {
            $id_lang = (int) $context->language->id;
        }

        if (isset($context->shop) && $id_shop === null) {
            $id_shop = (int) $context->shop->id;
        }

        $route = $this->computeRoute($rule, $controller, $keywords, $params);

        if (!isset($this->routes[$id_shop])) {
            $this->routes[$id_shop] = [];
        }
        if (!isset($this->routes[$id_shop][$id_lang])) {
            $this->routes[$id_shop][$id_lang] = [];
        }

        $this->routes[$id_shop][$id_lang][$route_id] = $route;
    }

    /**
     * Check if a route exists.
     *
     * @param string $route_id
     * @param int $id_lang
     * @param int $id_shop
     *
     * @return bool
     */
    public function hasRoute($route_id, $id_lang = null, $id_shop = null)
    {
        if (isset(Context::getContext()->language) && $id_lang === null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if (isset(Context::getContext()->shop) && $id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        return isset($this->routes[$id_shop][$id_lang][$route_id]);
    }

    /**
     * Check if a keyword is written in a route rule.
     *
     * @param string $route_id
     * @param int $id_lang
     * @param string $keyword
     * @param int $id_shop
     *
     * @return bool
     */
    public function hasKeyword($route_id, $id_lang, $keyword, $id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!isset($this->routes[$id_shop])) {
            $this->loadRoutes($id_shop);
        }

        if (!isset($this->routes[$id_shop]) || !isset($this->routes[$id_shop][$id_lang])
            || !isset($this->routes[$id_shop][$id_lang][$route_id])) {
            return false;
        }

        return preg_match('#\{([^{}]*:)?' . preg_quote($keyword, '#') .
            '(:[^{}]*)?\}#', $this->routes[$id_shop][$id_lang][$route_id]['rule']);
    }

    /**
     * Check if a route rule contain all required keywords of default route definition.
     *
     * @param string $route_id
     * @param string $rule Rule to verify
     * @param array $errors List of missing keywords
     *
     * @return bool
     */
    public function validateRoute($route_id, $rule, &$errors = [])
    {
        $errors = [];
        if (!isset($this->default_routes[$route_id])) {
            return false;
        }

        foreach ($this->default_routes[$route_id]['keywords'] as $keyword => $data) {
            if (isset($data['param']) && !preg_match('#\{([^{}]*:)?' . $keyword . '(:[^{}]*)?\}#', $rule)) {
                $errors[] = $keyword;
            }
        }

        return (count($errors)) ? false : true;
    }

    /**
     * Create an url from.
     *
     * @param string $route_id Name the route
     * @param int $id_lang
     * @param array $params
     * @param bool $force_routes
     * @param string $anchor Optional anchor to add at the end of this url
     * @param null $id_shop
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function createUrl(
        $route_id,
        $id_lang = null,
        array $params = [],
        $force_routes = false,
        $anchor = '',
        $id_shop = null
    ) {
        if ($id_lang === null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if ($id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        if (!isset($this->routes[$id_shop])) {
            $this->loadRoutes($id_shop);
        }

        if (!isset($this->routes[$id_shop][$id_lang][$route_id])) {
            $query = http_build_query($params, '', '&');
            $index_link = $this->use_routes ? '' : 'index.php';

            return ($route_id == 'index') ? $index_link . (($query) ? '?' . $query : '') :
                ((trim($route_id) == '') ? '' : 'index.php?controller=' . $route_id) . (($query) ? '&' . $query : '') . $anchor;
        }
        $route = $this->routes[$id_shop][$id_lang][$route_id];
        // Check required fields
        $query_params = isset($route['params']) ? $route['params'] : [];
        foreach ($route['keywords'] as $key => $data) {
            if (!$data['required']) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                throw new PrestaShopException('Dispatcher::createUrl() miss required parameter "' . $key . '" for route "' . $route_id . '"');
            }
            if (isset($this->default_routes[$route_id])) {
                $query_params[$this->default_routes[$route_id]['keywords'][$key]['param']] = $params[$key];
            }
        }

        // Build an url which match a route
        if ($this->use_routes || $force_routes) {
            $url = $route['rule'];
            $add_param = [];

            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key])) {
                    if (!isset($this->default_routes[$route_id]['keywords'][$key])) {
                        $add_param[$key] = $value;
                    }
                } else {
                    if ($params[$key]) {
                        $parameter = $params[$key];
                        if (is_array($parameter)) {
                            if (array_key_exists($id_lang, $parameter)) {
                                $parameter = $parameter[$id_lang];
                            } else {
                                // made the choice to return the first element of the array
                                $parameter = reset($parameter);
                            }
                        }
                        $replace = $route['keywords'][$key]['prepend'] . $parameter . $route['keywords'][$key]['append'];
                    } else {
                        $replace = '';
                    }
                    $url = preg_replace('#\{([^{}]*:)?' . $key . '(:[^{}]*)?\}#', $replace, $url);
                }
            }
            $url = preg_replace('#\{([^{}]*:)?[a-z0-9_]+?(:[^{}]*)?\}#', '', $url);
            if (count($add_param)) {
                $url .= '?' . http_build_query($add_param, '', '&');
            }
        } else {
            // Build a classic url index.php?controller=foo&...
            $add_params = [];
            foreach ($params as $key => $value) {
                if (!isset($route['keywords'][$key]) && !isset($this->default_routes[$route_id]['keywords'][$key])) {
                    $add_params[$key] = $value;
                }
            }

            if (!empty($route['controller'])) {
                $query_params['controller'] = $route['controller'];
            }
            $query = http_build_query(array_merge($add_params, $query_params), '', '&');
            if ($this->multilang_activated) {
                $query .= (!empty($query) ? '&' : '') . 'id_lang=' . (int) $id_lang;
            }
            $url = 'index.php?' . $query;
        }

        return $url . $anchor;
    }

    /**
     * Retrieve the controller from url or request uri if routes are activated.
     *
     * @param int $id_shop
     *
     * @return string
     */
    public function getController($id_shop = null)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            $_GET['controllerUri'] = Tools::getValue('controller');
        }
        if ($this->controller) {
            $_GET['controller'] = $this->controller;

            return $this->controller;
        }

        if (isset(Context::getContext()->shop) && $id_shop === null) {
            $id_shop = (int) Context::getContext()->shop->id;
        }

        $controller = Tools::getValue('controller');

        if (isset($controller)
            && is_string($controller)
            && preg_match('/^([0-9a-z_-]+)\?(.*)=(.*)$/Ui', $controller, $m)
        ) {
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
            $test_request_uri = preg_replace('/(=http:\/\/)/', '=', $this->request_uri);

            // If the request_uri matches a static file, then there is no need to check the routes, we keep
            // "controller_not_found" (a static file should not go through the dispatcher)
            if (!preg_match(
                '/\.(gif|jpe?g|png|css|js|ico)$/i',
                parse_url($test_request_uri, PHP_URL_PATH)
            )) {
                // Add empty route as last route to prevent this greedy regexp to match request uri before right time
                if ($this->empty_route) {
                    $this->addRoute(
                        $this->empty_route['routeID'],
                        $this->empty_route['rule'],
                        $this->empty_route['controller'],
                        Context::getContext()->language->id,
                        [],
                        [],
                        $id_shop
                    );
                }

                list($uri) = explode('?', $this->request_uri);

                if (isset($this->routes[$id_shop][Context::getContext()->language->id])) {
                    foreach ($this->routes[$id_shop][Context::getContext()->language->id] as $route) {
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
     * Get list of all available FO controllers.
     *
     * @var mixed
     *
     * @return array
     */
    public static function getControllers($dirs)
    {
        if (!is_array($dirs)) {
            $dirs = [$dirs];
        }

        $controllers = [];
        foreach ($dirs as $dir) {
            $controllers = array_merge($controllers, Dispatcher::getControllersInDirectory($dir));
        }

        return $controllers;
    }

    /**
     * Get list of all available Module Front controllers.
     *
     * @param string $type
     * @param string $module
     *
     * @return array
     */
    public static function getModuleControllers($type = 'all', $module = null)
    {
        $modules_controllers = [];
        if (null === $module) {
            $modules = Module::getModulesOnDisk(true);
        } elseif (!is_array($module)) {
            $modules = [Module::getInstanceByName($module)];
        } else {
            $modules = [];
            foreach ($module as $_mod) {
                $modules[] = Module::getInstanceByName($_mod);
            }
        }

        foreach ($modules as $mod) {
            foreach (Dispatcher::getControllersInDirectory(_PS_MODULE_DIR_ . $mod->name . '/controllers/') as $controller) {
                if ($type == 'admin') {
                    if (strpos($controller, 'Admin') !== false) {
                        $modules_controllers[$mod->name][] = $controller;
                    }
                } elseif ($type == 'front') {
                    if (strpos($controller, 'Admin') === false) {
                        $modules_controllers[$mod->name][] = $controller;
                    }
                } else {
                    $modules_controllers[$mod->name][] = $controller;
                }
            }
        }

        return $modules_controllers;
    }

    /**
     * Get list of available controllers from the specified dir.
     *
     * @param string $dir Directory to scan (recursively)
     *
     * @return array
     */
    public static function getControllersInDirectory($dir)
    {
        if (!is_dir($dir)) {
            return [];
        }

        $controllers = [];
        $controller_files = scandir($dir, SCANDIR_SORT_NONE);
        foreach ($controller_files as $controller_filename) {
            if ($controller_filename[0] != '.') {
                if (!strpos($controller_filename, '.php') && is_dir($dir . $controller_filename)) {
                    $controllers += Dispatcher::getControllersInDirectory(
                        $dir . $controller_filename . DIRECTORY_SEPARATOR
                    );
                } elseif ($controller_filename != 'index.php') {
                    $key = str_replace(['controller.php', '.php'], '', strtolower($controller_filename));
                    $controllers[$key] = basename($controller_filename, '.php');
                }
            }
        }

        return $controllers;
    }
}
