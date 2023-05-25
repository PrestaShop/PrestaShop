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
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Action\ActionsBarButton;
use PrestaShop\PrestaShop\Core\Action\ActionsBarButtonInterface;
use PrestaShop\PrestaShop\Core\Action\ActionsBarButtonsCollection;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Bridge\Helper\AddFlashMessage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class AdminControllerCore extends Controller
{
    /** @var string */
    public $path;

    /** @var string */
    public static $currentIndex;

    /** @var string */
    public $content;

    /** @var array */
    public $warnings = [];

    /** @var array */
    public $informations = [];

    /** @var array */
    public $confirmations = [];

    /** @var string|false */
    public $shopShareDatas = false;

    /** @var array */
    public $_languages = [];

    /** @var int */
    public $default_form_language;

    /** @var int */
    public $allow_employee_form_lang;

    /** @var string */
    public $layout = 'layout.tpl';

    /** @var bool */
    public $bootstrap = false;

    /** @var string|array */
    protected $meta_title = [];

    /** @var string */
    public $template = 'content.tpl';

    /** @var string Associated table name */
    public $table = 'configuration';

    /** @var string|null */
    public $list_id;

    /** @var string|false Object identifier inside the associated table */
    protected $identifier = false;

    /** @var string */
    protected $identifier_name = 'name';

    /** @var string|null Associated object class name */
    public $className;

    /** @var array */
    public $tabAccess;

    /** @var int Tab id */
    public $id = -1;

    /** @var bool */
    public $required_database = false;

    /** @var string Security token */
    public $token;

    /** @var string "shop" or "group_shop" */
    public $shopLinkType;

    /** @var string|bool Default ORDER BY clause when `$_orderBy` is not defined */
    protected $_defaultOrderBy = false;

    /** @var string */
    protected $_defaultOrderWay = 'ASC';

    /** @var array */
    public $tpl_form_vars = [];

    /** @var array */
    public $tpl_list_vars = [];

    /** @var array */
    public $tpl_delete_link_vars = [];

    /** @var array */
    public $tpl_option_vars = [];

    /** @var array */
    public $tpl_view_vars = [];

    /** @var array */
    public $tpl_required_fields_vars = [];

    /** @var string|null */
    public $base_tpl_view = null;

    /** @var string|null */
    public $base_tpl_form = null;

    /** @var bool If you want more fieldsets in the form */
    public $multiple_fieldsets = false;

    /** @var array */
    public $fields_value = [];

    /** @var int|null */
    public $max_image_size = null;

    /** @var bool Define if the header of the list contains filter and sorting links or not */
    protected $list_simple_header;

    /** @var array|null List to be generated */
    protected $fields_list;

    /** @var array Edit form to be generated */
    protected $fields_form;

    /** @var array Override of `$fields_form` */
    protected $fields_form_override;

    /** @var string Override form action */
    protected $submit_action;

    /** @var array List of option forms to be generated */
    protected $fields_options = [];

    /** @var string */
    protected $shopLink;

    /** @var string SQL query */
    protected $_listsql = '';

    /** @var array Cache for query results */
    protected $_list = [];

    /** @var string MySQL error */
    protected $_list_error;

    /** @var string|array Toolbar title */
    protected $toolbar_title;

    /** @var array|null List of toolbar buttons */
    protected $toolbar_btn = null;

    /** @var bool Scrolling toolbar */
    protected $toolbar_scroll = true;

    /** @var bool Set to false to hide toolbar and page title */
    protected $show_toolbar = true;

    /** @var bool Set to true to show toolbar and page title for options */
    protected $show_toolbar_options = false;

    /** @var int Number of results in list */
    protected $_listTotal = 0;

    /** @var bool Automatically join language table if true */
    public $lang = false;

    /** @var array|bool|null WHERE clause determined by filter fields */
    protected $_filter;

    /** @var string|null */
    protected $_filterHaving;

    /** @var string Temporary SQL table WHERE clause determined by filter fields */
    protected $_tmpTableFilter = '';

    /** @var array Number of results in list per page (used in select field) */
    protected $_pagination = [20, 50, 100, 300, 1000];

    /** @var int Default number of results in list per page */
    protected $_default_pagination = 50;

    /** @var string|null ORDER BY clause determined by field/arrows in list header */
    protected $_orderBy;

    /** @var string Order way (ASC, DESC) determined by arrows in list header */
    protected $_orderWay;

    /** @var array List of available actions for each list row - default actions are view, edit, delete, duplicate */
    protected $actions_available = ['view', 'edit', 'duplicate', 'delete'];

    /** @var array List of required actions for each list row */
    protected $actions = [];

    /** @var array List of row ids associated with a given action for witch this action have to not be available */
    protected $list_skip_actions = [];

    /** @var bool Don't show header & footer */
    protected $lite_display = false;

    /** @var bool List content lines are clickable if true */
    protected $list_no_link = false;

    /** @var bool */
    protected $allow_export = false;

    /** @var array Cache for translations */
    public static $cache_lang = [];

    /** @var array Required_fields to display in the Required Fields form */
    public $required_fields = [];

    /** @var HelperList */
    protected $helper;

    /** @var bool */
    private $allowAnonymous = false;

    /** @var string */
    public $override_folder;

    /**
     * @deprecated since 9.0
     *
     * @var int DELETE access level
     */
    public const LEVEL_DELETE = Permission::LEVEL_DELETE;

    /**
     * @deprecated since 9.0
     *
     * @var int ADD access level
     */
    public const LEVEL_ADD = Permission::LEVEL_CREATE;

    /**
     * @deprecated since 9.0
     *
     * @var int EDIT access level
     */
    public const LEVEL_EDIT = Permission::LEVEL_UPDATE;

    /**
     * @deprecated since 9.0
     *
     * @var int VIEW access level
     */
    public const LEVEL_VIEW = Permission::LEVEL_READ;

    /**
     * Actions to execute on multiple selections.
     *
     * Usage:
     *
     * array(
     *      'actionName' => array(
     *      'text' => $this->trans('Message displayed on the submit button (mandatory)'),
     *      'confirm' => $this->trans('If set, this confirmation message will pop-up (optional)')),
     *      'anotherAction' => array(...)
     * );
     *
     * If your action is named 'actionName', you need to have a method named bulkactionName() that will be executed when the button is clicked.
     *
     * @var array|null
     */
    protected $bulk_actions;

    /** @var array Ids of the rows selected */
    protected $boxes;

    /** @var bool Do not automatically select * anymore but select only what is necessary */
    protected $explicitSelect = false;

    /** @var string|null Add fields into data query to display list */
    protected $_select;

    /** @var string|null Join tables into data query to display list */
    protected $_join;

    /** @var string|null Add conditions into data query to display list */
    protected $_where;

    /** @var string|null Group rows into data query to display list */
    protected $_group;

    /** @var string|null Having rows into data query to display list */
    protected $_having;

    /** @var string|bool Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records */
    protected $_use_found_rows = true;

    /** @var bool */
    protected $is_cms = false;

    /** @var string Identifier to use for changing positions in lists (can be omitted if positions cannot be changed) */
    protected $position_identifier;

    /** @var string|int */
    protected $position_group_identifier;

    /** @var bool Table records are not deleted but marked as deleted if set to true */
    protected $deleted = false;

    /** @var bool Is a list filter set */
    protected $filter;

    /** @var bool */
    protected $noLink;

    /** @var bool|null */
    protected $specificConfirmDelete = null;

    /** @var bool */
    protected $colorOnBackground;

    /** @var bool If true, activates color on hover */
    protected $row_hover = true;

    /** @var string Action to perform : 'edit', 'view', 'add', ... */
    protected $action;

    /** @var string|null */
    protected $display;

    /** @var string */
    public $tpl_folder;

    /** @var string */
    protected $bo_theme;

    /** @var bool Redirect or not after a creation */
    protected $_redirect = true;

    /** @var array Name and directory where class image are located */
    public $fieldImageSettings = [];

    /** @var string Image type */
    public $imageType = 'jpg';

    /** @var ObjectModel|null Instantiation of the class associated with the AdminController */
    protected $object;

    /** @var int Current object ID */
    protected $id_object;

    /** @var string Current controller name without suffix */
    public $controller_name;

    /** @var int */
    public $multishop_context = -1;

    /** @var bool */
    public $multishop_context_group = true;

    /** @var array|null Current breadcrumb position as an array of tab names */
    protected $breadcrumbs;

    /** @var bool Bootstrap variable */
    public $show_page_header_toolbar = false;

    /** @var string Bootstrap variable */
    public $page_header_toolbar_title;

    /** @var array|Traversable Bootstrap variable */
    public $page_header_toolbar_btn = [];

    /** @var bool|null Bootstrap variable */
    public $show_form_cancel_button;

    /** @var string */
    public $admin_webpath;

    /** @var array */
    protected $list_natives_modules = [];

    /** @var array */
    protected $list_partners_modules = [];

    /** @var array<string, string|array> */
    public $modals = [];

    /** @var bool if logged employee has access to AdminImport */
    protected $can_import = false;

    /** @var string */
    protected $tabSlug;

    /** @var int Auth cookie lifetime */
    public const AUTH_COOKIE_LIFETIME = 3600;

    /** @var array */
    public $_conf;

    /** @var bool */
    protected static $is_prestashop_up = true;

    /** @var array */
    protected $translationsTab = [];

    /**
     * @var string
     */
    public $bo_css;

    /**
     * @var array
     */
    public $_error;

    /**
     * @var int
     */
    public $_lang;

    public function __construct($forceControllerName = '', $default_theme_name = 'default')
    {
        $this->controller_type = 'admin';
        $this->controller_name = !empty($forceControllerName) ? $forceControllerName : get_class($this);
        if (strpos($this->controller_name, 'ControllerOverride')) {
            $this->controller_name = substr($this->controller_name, 0, -18);
        }
        if (strpos($this->controller_name, 'Controller')) {
            $this->controller_name = substr($this->controller_name, 0, -10);
        }
        parent::__construct();

        if ($this->multishop_context == -1) {
            $this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;
        }

        if (defined('_PS_BO_ALL_THEMES_DIR_')) {
            if (defined('_PS_BO_DEFAULT_THEME_') && _PS_BO_DEFAULT_THEME_
                && @filemtime(_PS_BO_ALL_THEMES_DIR_ . _PS_BO_DEFAULT_THEME_ . DIRECTORY_SEPARATOR . 'template')) {
                $default_theme_name = _PS_BO_DEFAULT_THEME_;
            }

            $this->bo_theme = $default_theme_name;
            if (!@filemtime(_PS_BO_ALL_THEMES_DIR_ . $this->bo_theme . DIRECTORY_SEPARATOR . 'template')) {
                $this->bo_theme = 'default';
            }

            $this->context->employee->bo_theme = (
                Validate::isLoadedObject($this->context->employee)
                && $this->context->employee->bo_theme
            ) ? $this->context->employee->bo_theme : $this->bo_theme;

            $this->bo_css = (
                Validate::isLoadedObject($this->context->employee)
                && $this->context->employee->bo_css
            ) ? $this->context->employee->bo_css : 'theme.css';
            $this->context->employee->bo_css = $this->bo_css;

            $adminThemeCSSFile = _PS_BO_ALL_THEMES_DIR_ . $this->bo_theme . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $this->bo_css;

            if (file_exists($adminThemeCSSFile)) {
                $this->bo_css = 'theme.css';
            }

            $this->context->smarty->setTemplateDir([
                _PS_BO_ALL_THEMES_DIR_ . $this->bo_theme . DIRECTORY_SEPARATOR . 'template',
                _PS_OVERRIDE_DIR_ . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates',
            ]);
        }

        $this->id = Tab::getIdFromClassName($this->controller_name);
        $this->token = Tools::getAdminToken($this->controller_name . (int) $this->id . (int) $this->context->employee->id);

        $this->_conf = [
            1 => $this->trans('Successful deletion', [], 'Admin.Notifications.Success'),
            2 => $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success'),
            3 => $this->trans('Successful creation', [], 'Admin.Notifications.Success'),
            4 => $this->trans('Successful update', [], 'Admin.Notifications.Success'),
            5 => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
            6 => $this->trans('The settings have been successfully updated.', [], 'Admin.Notifications.Success'),
            7 => $this->trans('Image successfully deleted.', [], 'Admin.Notifications.Success'),
            8 => $this->trans('The module was successfully downloaded.', [], 'Admin.Modules.Notification'),
            9 => $this->trans('The thumbnails were successfully regenerated.', [], 'Admin.Notifications.Success'),
            10 => $this->trans('The message was successfully sent to the customer.', [], 'Admin.Orderscustomers.Notification'),
            11 => $this->trans('Comment successfully added.', [], 'Admin.Notifications.Success'),
            12 => $this->trans('Module(s) installed successfully.', [], 'Admin.Modules.Notification'),
            13 => $this->trans('Module(s) uninstalled successfully.', [], 'Admin.Modules.Notification'),
            14 => $this->trans('The translation was successfully copied.', [], 'Admin.International.Notification'),
            15 => $this->trans('The translations have been successfully added.', [], 'Admin.International.Notification'),
            16 => $this->trans('The module transplanted successfully to the hook.', [], 'Admin.Modules.Notification'),
            17 => $this->trans('The module was successfully removed from the hook.', [], 'Admin.Modules.Notification'),
            18 => $this->trans('Successful upload.', [], 'Admin.Notifications.Success'),
            19 => $this->trans('Duplication was completed successfully.', [], 'Admin.Notifications.Success'),
            20 => $this->trans('The translation was added successfully, but the language has not been created.', [], 'Admin.International.Notification'),
            21 => $this->trans('Module reset successfully.', [], 'Admin.Modules.Notification'),
            22 => $this->trans('Module deleted successfully.', [], 'Admin.Modules.Notification'),
            23 => $this->trans('Localization pack imported successfully.', [], 'Admin.International.Notification'),
            24 => $this->trans('Localization pack imported successfully.', [], 'Admin.International.Notification'),
            25 => $this->trans('The selected images have successfully been moved.', [], 'Admin.Notifications.Success'),
            26 => $this->trans('Your cover image selection has been saved.', [], 'Admin.Notifications.Success'),
            27 => $this->trans('The image\'s shop association has been modified.', [], 'Admin.Notifications.Success'),
            28 => $this->trans('A zone has been assigned to the selection successfully.', [], 'Admin.Notifications.Success'),
            29 => $this->trans('Successful upgrade.', [], 'Admin.Notifications.Success'),
            30 => $this->trans('A partial refund was successfully created.', [], 'Admin.Orderscustomers.Notification'),
            31 => $this->trans('The discount was successfully generated.', [], 'Admin.Catalog.Notification'),
            32 => $this->trans('Successfully signed in to PrestaShop Addons.', [], 'Admin.Modules.Notification'),
        ];

        $this->_error = [
            1 => $this->trans(
                'The root category of the shop %shop% is not associated with the current shop. You can\'t access this page. Please change the root category of the shop.',
                [
                    '%shop%' => $this->context->shop->name,
                ],
                'Admin.Catalog.Notification'
            ),
        ];

        if (!$this->identifier) {
            $this->identifier = 'id_' . $this->table;
        }
        if (!$this->_defaultOrderBy) {
            $this->_defaultOrderBy = $this->identifier;
        }

        // Fix for homepage
        if ($this->controller_name == 'AdminDashboard') {
            $_POST['token'] = $this->token;
        }

        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        }

        $this->override_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)) . '/';
        // Get the name of the folder containing the custom tpl files
        $this->tpl_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)) . '/';

        $this->initShopContext();

        if (defined('_PS_ADMIN_DIR_')) {
            $this->admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
            $this->admin_webpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $this->admin_webpath);
        }

        // Set context mode
        if (isset($this->context->cookie->is_contributor) && (int) $this->context->cookie->is_contributor === 1) {
            $this->context->mode = Context::MODE_STD_CONTRIB;
        } else {
            $this->context->mode = Context::MODE_STD;
        }

        /* Check if logged employee has access to AdminImport controller */
        $import_access = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminImport'));
        if (is_array($import_access) && isset($import_access['view']) && $import_access['view'] == 1) {
            $this->can_import = true;
        }

        $this->context->smarty->assign([
            'context_mode' => $this->context->mode,
            'can_import' => $this->can_import,
        ]);
    }

    /**
     * Gets the multistore header and assigns its html content to a smarty variable
     *
     * @see PrestaShopBundle\Controller\Admin\MultistoreController
     *
     * (the decision to display it or not is taken by the MultistoreController)
     */
    public function initMultistoreHeader(): void
    {
        if (!isset($this->lockedToAllShopContext)) {
            return;
        }

        $this->context->smarty->assign([
            'multistore_header' => $this->get('prestashop.core.admin.multistore')->header($this->lockedToAllShopContext)->getContent(),
        ]);
    }

    /**
     * Set breadcrumbs array for the controller page.
     *
     * @param int|null $tab_id
     * @param array|null $tabs
     */
    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (!is_array($tabs)) {
            $tabs = [];
        }

        if (null === $tab_id) {
            $tab_id = $this->id;
        }

        $tabs = Tab::recursiveTab($tab_id, $tabs);

        $dummy = ['name' => '', 'href' => '', 'icon' => ''];
        $breadcrumbs2 = [
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy,
        ];
        if (!empty($tabs[0])) {
            $this->addMetaTitle($tabs[0]['name']);
            $breadcrumbs2['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs2['tab']['href'] = $this->context->link->getTabLink($tabs[0]);
            if (!isset($tabs[1])) {
                $breadcrumbs2['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (!empty($tabs[1])) {
            $breadcrumbs2['container']['name'] = $tabs[1]['name'];
            $breadcrumbs2['container']['href'] = $this->context->link->getTabLink($tabs[1]);
            $breadcrumbs2['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

        /* content, edit, list, add, details, options, view */
        switch ($this->display) {
            case 'add':
                $breadcrumbs2['action']['name'] = $this->trans('Add', [], 'Admin.Actions');
                $breadcrumbs2['action']['icon'] = 'icon-plus';

                break;
            case 'edit':
                $breadcrumbs2['action']['name'] = $this->trans('Edit', [], 'Admin.Actions');
                $breadcrumbs2['action']['icon'] = 'icon-pencil';

                break;
            case '':
            case 'list':
                $breadcrumbs2['action']['name'] = $this->trans('List');
                $breadcrumbs2['action']['icon'] = 'icon-th-list';

                break;
            case 'details':
            case 'view':
                $breadcrumbs2['action']['name'] = $this->trans('View details');
                $breadcrumbs2['action']['icon'] = 'icon-zoom-in';

                break;
            case 'options':
                $breadcrumbs2['action']['name'] = $this->trans('Options');
                $breadcrumbs2['action']['icon'] = 'icon-cogs';

                break;
            case 'generator':
                $breadcrumbs2['action']['name'] = $this->trans('Generator');
                $breadcrumbs2['action']['icon'] = 'icon-flask';

                break;
        }

        Hook::exec('actionAdminBreadcrumbModifier', ['tabs' => $tabs, 'breadcrumb' => &$breadcrumbs2], null, true);

        $this->context->smarty->assign([
            'breadcrumbs2' => $breadcrumbs2,
            'quick_access_current_link_name' => Tools::safeOutput($breadcrumbs2['tab']['name'] . ' - ' . $breadcrumbs2['action']['name']),
            'quick_access_current_link_icon' => $breadcrumbs2['container']['icon'],
        ]);

        /* BEGIN - Backward compatibility < 1.6.0.3 */
        $this->breadcrumbs[] = $tabs[0]['name'] ?? '';
        $navigation_pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigation_pipe);
        /* END - Backward compatibility < 1.6.0.3 */
    }

    /**
     * Set default toolbar_title to admin breadcrumb.
     */
    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : [$this->breadcrumbs];

        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = $this->trans('Edit', [], 'Admin.Actions');
                $this->addMetaTitle($this->trans('Edit', [], 'Admin.Actions'));

                break;

            case 'add':
                $this->toolbar_title[] = $this->trans('Add new', [], 'Admin.Actions');
                $this->addMetaTitle($this->trans('Add new', [], 'Admin.Actions'));

                break;

            case 'view':
                $this->toolbar_title[] = $this->trans('View', [], 'Admin.Actions');
                $this->addMetaTitle($this->trans('View', [], 'Admin.Actions'));

                break;
        }

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbar_title[] = $filter;
        }
    }

    /**
     * @return string|void
     */
    public function addFiltersToBreadcrumbs()
    {
        if ($this->filter && is_array($this->fields_list)) {
            $filters = [];

            foreach ($this->fields_list as $field => $t) {
                if (isset($t['filter_key'])) {
                    $field = $t['filter_key'];
                }

                if (($val = Tools::getValue($this->table . 'Filter_' . $field))
                    || ($val = $this->context->cookie->{$this->getCookieFilterPrefix() . $this->table . 'Filter_' . $field})) {
                    if (!is_array($val)) {
                        /** @var bool|string $val */
                        $filter_value = '';
                        if (isset($t['type']) && $t['type'] == 'bool') {
                            $filter_value = ((bool) $val) ? $this->trans('Yes', [], 'Admin.Global') : $this->trans('No', [], 'Admin.Global');
                        } elseif (isset($t['type']) && $t['type'] == 'date' || isset($t['type']) && $t['type'] == 'datetime') {
                            $date = json_decode($val, true);
                            if (isset($date[0])) {
                                $filter_value = $date[0];
                                if (isset($date[1]) && !empty($date[1])) {
                                    $filter_value .= ' - ' . $date[1];
                                }
                            }
                        } elseif (is_string($val)) {
                            $filter_value = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        }
                        if (!empty($filter_value)) {
                            $filters[] = $this->trans('%s: %s', [$t['title'], $filter_value]);
                        }
                    } else {
                        $filter_value = '';
                        foreach ($val as $v) {
                            if (is_string($v) && !empty($v)) {
                                $filter_value .= ' - ' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                            }
                        }
                        $filter_value = ltrim($filter_value, ' -');
                        if (!empty($filter_value)) {
                            $filters[] = $this->trans('%s: %s', [$t['title'], $filter_value]);
                        }
                    }
                }
            }

            if (count($filters)) {
                return $this->trans('filter by %s', [implode(', ', $filters)]);
            }
        }
    }

    /**
     * @param string $action
     * @param bool $disable
     */
    public function access($action, $disable = false)
    {
        if (empty($this->tabAccess[$action])) {
            $slugs = [];

            foreach ((array) Access::getAuthorizationFromLegacy($action) as $roleSuffix) {
                $slugs[] = $this->getTabSlug() . $roleSuffix;
            }

            $this->tabAccess[$action] = Access::isGranted(
                $slugs,
                $this->context->employee->id_profile
            );
        }

        return $this->tabAccess[$action];
    }

    /**
     * Check rights to view the current tab.
     *
     * @param bool $disable
     *
     * @return bool
     */
    public function viewAccess($disable = false)
    {
        return $this->access('view', $disable);
    }

    /**
     * Check for security token.
     *
     * @return bool
     */
    public function checkToken()
    {
        if (TokenInUrls::isDisabled() || $this->isAnonymousAllowed()) {
            return true;
        }

        $token = Tools::getValue('token');
        if ($token === $this->token) {
            return true;
        }

        if (count($_POST) || !isset($_GET['controller']) || !Validate::isControllerName($_GET['controller']) || !$token) {
            return false;
        }

        foreach ($_GET as $key => $value) {
            if (is_array($value) || !in_array($key, ['controller', 'controllerUri'])) {
                return false;
            }
        }

        $cookie = Context::getContext()->cookie;
        $whitelist = ['date_add', 'id_lang', 'id_employee', 'email', 'profile', 'passwd', 'remote_addr', 'shopContext', 'collapse_menu', 'checksum'];
        foreach ($cookie->getAll() as $key => $value) {
            if (!in_array($key, $whitelist)) {
                unset($cookie->$key);
            }
        }

        $cookie->write();

        return true;
    }

    /**
     * Set the filters used for the list display.
     */
    protected function getCookieFilterPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }

    public function processFilter()
    {
        Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
            'fields' => &$this->fields_list,
        ]);

        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix . $key});
                } elseif (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $this->list_id . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->list_id . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix . $this->list_id . 'Filter_');
        $definition = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix . $this->list_id . 'Filter_', 7 + Tools::strlen($prefix . $this->list_id))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $this->list_id));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = json_decode($value, true);
                    }
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';

                    // Assignment by reference
                    if (array_key_exists('tmpTableFilter', $field)) {
                        $sql_filter = &$this->_tmpTableFilter;
                    } elseif (array_key_exists('havingFilter', $field)) {
                        $sql_filter = &$this->_filterHaving;
                    } else {
                        $sql_filter = &$this->_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = $this->trans('The \'From\' date format is invalid (YYYY-MM-DD)', [], 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = $this->trans('The \'To\' date format is invalid (YYYY-MM-DD)', [], 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $this->identifier || $key == '`' . $this->identifier . '`');
                        $alias = ($definition && !empty($definition['fields'][$filter]['shop'])) ? 'sa' : 'a';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? $alias . '.' : '') . pSQL($key) . ' = ' . (int) $value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . (float) $value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . pSQL(trim($value)) . ' ';
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                }
            }
        }
    }

    /**
     * @TODO uses redirectAdmin only if !$this->ajax
     *
     * @return ObjectModel|bool|void
     */
    public function postProcess()
    {
        try {
            if ($this->ajax) {
                $action = Tools::getValue('action');
                // no need to use displayConf() here
                if (!empty($action) && method_exists($this, 'ajaxProcess' . Tools::toCamelCase($action))) {
                    Hook::exec('actionAdmin' . ucfirst($action) . 'Before', ['controller' => $this]);
                    Hook::exec('action' . get_class($this) . ucfirst($action) . 'Before', ['controller' => $this]);

                    $return = $this->{'ajaxProcess' . Tools::toCamelCase($action)}();

                    Hook::exec('actionAdmin' . ucfirst($action) . 'After', ['controller' => $this, 'return' => $return]);
                    Hook::exec('action' . get_class($this) . ucfirst($action) . 'After', ['controller' => $this, 'return' => $return]);

                    return $return;
                } elseif (!empty($action) && $this->controller_name == 'AdminModules' && Tools::getIsset('configure')) {
                    $module_obj = Module::getInstanceByName(Tools::getValue('configure'));
                    if (Validate::isLoadedObject($module_obj) && method_exists($module_obj, 'ajaxProcess' . $action)) {
                        return $module_obj->{'ajaxProcess' . $action}();
                    }
                } elseif (method_exists($this, 'ajaxProcess')) {
                    return $this->ajaxProcess();
                }
            } else {
                // Process list filtering
                if ($this->filter && $this->action != 'reset_filters') {
                    $this->processFilter();
                }

                if (!empty($_POST) && is_array($_POST) && (int) Tools::getValue('submitFilter' . $this->list_id) || Tools::isSubmit('submitReset' . $this->list_id)) {
                    $this->setRedirectAfter(self::$currentIndex . '&token=' . $this->token . (Tools::isSubmit('submitFilter' . $this->list_id) ? '&submitFilter' . $this->list_id . '=' . (int) Tools::getValue('submitFilter' . $this->list_id) : ''));
                }

                // If the method named after the action exists, call "before" hooks, then call action method, then call "after" hooks
                if (!empty($this->action) && method_exists($this, 'process' . ucfirst(Tools::toCamelCase($this->action)))) {
                    // Hook before action
                    Hook::exec('actionAdmin' . ucfirst($this->action) . 'Before', ['controller' => $this]);
                    Hook::exec('action' . get_class($this) . ucfirst($this->action) . 'Before', ['controller' => $this]);
                    // Call process
                    $return = $this->{'process' . Tools::toCamelCase($this->action)}();

                    // Hook After Action
                    Hook::exec('actionAdmin' . ucfirst($this->action) . 'After', ['controller' => $this, 'return' => $return]);
                    Hook::exec('action' . get_class($this) . ucfirst($this->action) . 'After', ['controller' => $this, 'return' => $return]);

                    return $return;
                }
            }
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
        }

        return false;
    }

    /**
     * Object Delete images.
     *
     * @return ObjectModel|false
     */
    public function processDeleteImage()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if (($object->deleteImage())) {
                $redirect = self::$currentIndex . '&update' . $this->table . '&' . $this->identifier . '=' . (int) Tools::getValue($this->identifier) . '&conf=7&token=' . $this->token;
                if (!$this->ajax) {
                    $this->redirect_after = $redirect;
                } else {
                    $this->content = 'ok';
                }
            }
        }
        $this->errors[] = $this->trans('An error occurred while attempting to delete the image. (cannot load object).', [], 'Admin.Notifications.Error');

        return $object;
    }

    /**
     * @param string $text_delimiter
     *
     * @throws PrestaShopException
     */
    public function processExport($text_delimiter = '"')
    {
        // clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        $this->getList($this->context->language->id);
        if (!count($this->_list)) {
            return;
        }

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $this->table . '_' . date('Y-m-d_His') . '.csv"');

        $fd = fopen('php://output', 'wb');
        $headers = [];
        foreach ($this->fields_list as $key => $datas) {
            if ('PDF' === $datas['title']) {
                unset($this->fields_list[$key]);
            } else {
                if ('ID' === $datas['title']) {
                    $headers[] = strtolower(Tools::htmlentitiesDecodeUTF8($datas['title']));
                } else {
                    $headers[] = Tools::htmlentitiesDecodeUTF8($datas['title']);
                }
            }
        }
        fputcsv($fd, $headers, ';', $text_delimiter);

        foreach ($this->_list as $i => $row) {
            $content = [];
            foreach ($this->fields_list as $key => $params) {
                $field_value = isset($row[$key]) ? Tools::htmlentitiesDecodeUTF8(Tools::nl2br($row[$key])) : '';
                if ($key == 'image') {
                    if ($params['image'] != 'p') {
                        $path_to_image = Tools::getShopDomain(true) . _PS_IMG_ . $params['image'] . '/' . $row['id_' . $this->table] . (isset($row['id_image']) ? '-' . (int) $row['id_image'] : '') . '.' . $this->imageType;
                    } else {
                        $path_to_image = Tools::getShopDomain(true) . _PS_IMG_ . $params['image'] . '/' . Image::getImgFolderStatic($row['id_image']) . (int) $row['id_image'] . '.' . $this->imageType;
                    }
                    $field_value = $path_to_image;
                }
                if (isset($params['callback'])) {
                    $callback_obj = (isset($params['callback_object'])) ? $params['callback_object'] : $this->context->controller;
                    if (!preg_match('/<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)/ism', call_user_func_array([$callback_obj, $params['callback']], [$field_value, $row]))) {
                        $field_value = call_user_func_array([$callback_obj, $params['callback']], [$field_value, $row]);
                    }
                }
                $content[] = $field_value;
            }
            fputcsv($fd, $content, ';', $text_delimiter);
        }
        @fclose($fd);
        die;
    }

    /**
     * Object Delete.
     *
     * @return ObjectModel|false
     *
     * @throws PrestaShopException
     */
    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $res = true;
            // check if request at least one object with noZeroObject
            if (isset($object->noZeroObject) && count(call_user_func([$this->className, $object->noZeroObject])) <= 1) {
                $this->errors[] = $this->trans('You need at least one object.', [], 'Admin.Notifications.Error') .
                    ' <b>' . $this->table . '</b><br />' .
                    $this->trans('You cannot delete all of the items.', [], 'Admin.Notifications.Error');
            } elseif (array_key_exists('delete', $this->list_skip_actions) && in_array($object->id, $this->list_skip_actions['delete'])) { //check if some ids are in list_skip_actions and forbid deletion
                $this->errors[] = $this->trans('You cannot delete this item.', [], 'Admin.Notifications.Error');
            } else {
                if ($this->deleted) {
                    if (!empty($this->fieldImageSettings)) {
                        $res = $object->deleteImage();
                    }

                    if (!$res) {
                        $this->errors[] = $this->trans('Unable to delete associated images.', [], 'Admin.Notifications.Error');
                    }

                    $object->deleted = true;
                    if ($res = $object->update()) {
                        $this->redirect_after = self::$currentIndex . '&conf=1&token=' . $this->token;
                    }
                } elseif ($res = $object->delete()) {
                    $this->redirect_after = self::$currentIndex . '&conf=1&token=' . $this->token;
                }
                $this->errors[] = $this->trans('An error occurred during deletion.', [], 'Admin.Notifications.Error');
                if ($res) {
                    PrestaShopLogger::addLog(
                        $this->trans('%s deletion', [$this->className]),
                        1,
                        null,
                        $this->className,
                        (int) $this->object->id,
                        true,
                        (int) $this->context->employee->id
                    );
                }
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while deleting the object.', [], 'Admin.Notifications.Error') .
                ' <b>' . $this->table . '</b> ' .
                $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        }

        return $object;
    }

    /**
     * Call the right method for creating or updating object.
     *
     * @return ObjectModel|false|void
     */
    public function processSave()
    {
        if ($this->id_object) {
            $this->object = $this->loadObject();

            return $this->processUpdate();
        } else {
            return $this->processAdd();
        }
    }

    /**
     * Object creation.
     *
     * @return ObjectModel|false
     *
     * @throws PrestaShopException
     */
    public function processAdd()
    {
        if (!isset($this->className) || empty($this->className)) {
            return false;
        }

        $this->validateRules();
        if (count($this->errors) <= 0) {
            $this->object = new $this->className();

            $this->copyFromPost($this->object, $this->table);
            $this->beforeAdd($this->object);
            if (method_exists($this->object, 'add') && !$this->object->add()) {
                $this->errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error') .
                    ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            } elseif (($_POST[$this->identifier] = $this->object->id /* voluntary do affectation here */) && $this->postImage($this->object->id) && count($this->errors) === 0 && $this->_redirect) {
                PrestaShopLogger::addLog(
                    $this->trans('%s addition', [htmlspecialchars($this->className)]),
                    1,
                    null,
                    $this->className,
                    (int) $this->object->id,
                    true,
                    (int) $this->context->employee->id
                );
                $parent_id = (int) Tools::getValue('id_parent', 1);
                $this->afterAdd($this->object);
                $this->updateAssoShop($this->object->id);
                // Save and stay on same form
                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                    $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $this->object->id . '&conf=3&update' . $this->table . '&token=' . $this->token;
                }
                // Save and back to parent
                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd' . $this->table . 'AndBackToParent')) {
                    $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $parent_id . '&conf=3&token=' . $this->token;
                }
                // Default behavior (save and back)
                if (empty($this->redirect_after) && $this->redirect_after !== false) {
                    $this->redirect_after = self::$currentIndex . ($parent_id ? '&' . $this->identifier . '=' . $this->object->id : '') . '&conf=3&token=' . $this->token;
                }
            }
        }

        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';

            return false;
        }

        return $this->object;
    }

    /**
     * Object update.
     *
     * @return ObjectModel|false|void
     *
     * @throws PrestaShopException
     */
    public function processUpdate()
    {
        /* Checking fields validity */
        $this->validateRules();
        if (empty($this->errors)) {
            $id = (int) Tools::getValue($this->identifier);

            /* Object update */
            if (!empty($id)) {
                /** @var ObjectModel $object */
                $object = new $this->className($id);
                if (Validate::isLoadedObject($object)) {
                    $result = false;
                    /* Specific to objects which must not be deleted */
                    if ($this->deleted && $this->beforeDelete($object)) {
                        // Create new one with old objet values
                        /** @var ObjectModel $object_new */
                        $object_new = $object->duplicateObject();
                        if (Validate::isLoadedObject($object_new)) {
                            // Update old object to deleted
                            $object->deleted = true;
                            $object->update();

                            // Update new object with post values
                            $this->copyFromPost($object_new, $this->table);
                            $result = $object_new->update();
                            if (Validate::isLoadedObject($object_new)) {
                                $this->afterDelete($object_new, $object->id);
                            }
                        }
                    } else {
                        $this->copyFromPost($object, $this->table);
                        $result = $object->update();
                        $this->afterUpdate($object);
                    }

                    if ($object->id) {
                        $this->updateAssoShop($object->id);
                    }

                    if (!$result) {
                        $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') .
                            ' <b>' . $this->table . '</b> (' . Db::getInstance()->getMsgError() . ')';
                    } elseif ($this->postImage($object->id) && count($this->errors) === 0 && $this->_redirect) {
                        $parent_id = (int) Tools::getValue('id_parent', 1);
                        // Specific back redirect
                        if ($back = Tools::getValue('back')) {
                            $this->redirect_after = rawurldecode($back) . '&conf=4';
                        }
                        // Save and stay on same form
                        // @todo on the to following if, we may prefer to avoid override redirect_after previous value
                        if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                            $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $object->id . '&conf=4&update' . $this->table . '&token=' . $this->token;
                        }
                        // Save and back to parent
                        if (Tools::isSubmit('submitAdd' . $this->table . 'AndBackToParent')) {
                            $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $parent_id . '&conf=4&token=' . $this->token;
                        }

                        // Default behavior (save and back)
                        if (empty($this->redirect_after) && $this->redirect_after !== false) {
                            $this->redirect_after = self::$currentIndex . ($parent_id ? '&' . $this->identifier . '=' . $object->id : '') . '&conf=4&token=' . $this->token;
                        }
                    }
                    PrestaShopLogger::addLog(
                        $this->trans('%s modification', [$this->className]),
                        1,
                        null,
                        $this->className,
                        (int) $object->id,
                        true,
                        (int) $this->context->employee->id
                    );
                } else {
                    $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') .
                        ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
                }
            }
        }
        $this->errors = array_unique($this->errors);
        if (!empty($this->errors)) {
            // if we have errors, we stay on the form instead of going back to the list
            $this->display = 'edit';

            return false;
        }

        if (isset($object)) {
            return $object;
        }
    }

    /**
     * Change object required fields.
     *
     * @return ObjectModel
     */
    public function processUpdateFields()
    {
        if (!is_array($fields = Tools::getValue('fieldsBox'))) {
            $fields = [];
        }

        /** @var ObjectModel $object */
        $object = new $this->className();

        if (!$object->addFieldsRequiredDatabase($fields)) {
            $this->errors[] = $this->trans('An error occurred when attempting to update the required fields.', [], 'Admin.Notifications.Error');
        } else {
            $this->redirect_after = self::$currentIndex . '&conf=4&token=' . $this->token;
        }

        return $object;
    }

    /**
     * Change object status (active, inactive).
     *
     * @return ObjectModel|false
     *
     * @throws PrestaShopException
     */
    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->toggleStatus()) {
                $matches = [];
                if (preg_match('/[\?|&]controller=([^&]*)/', (string) $_SERVER['HTTP_REFERER'], $matches) !== false
                    && strtolower($matches[1]) != strtolower(preg_replace('/controller/i', '', get_class($this)))) {
                    $this->redirect_after = preg_replace('/[\?|&]conf=([^&]*)/i', '', (string) $_SERVER['HTTP_REFERER']);
                } else {
                    $this->redirect_after = self::$currentIndex . '&token=' . $this->token;
                }

                $id_category = (($id_category = (int) Tools::getValue('id_category')) && Tools::getValue('id_product')) ? '&id_category=' . $id_category : '';

                $page = (int) Tools::getValue('page');
                $page = $page > 1 ? '&submitFilter' . $this->table . '=' . (int) $page : '';
                $this->redirect_after .= '&conf=5' . $id_category . $page;
            } else {
                $this->errors[] = $this->trans('An error occurred while updating the status.', [], 'Admin.Notifications.Error');
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error') .
                ' <b>' . $this->table . '</b> ' .
                $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        }

        return $object;
    }

    /**
     * Change object position.
     *
     * @return ObjectModel|false
     */
    public function processPosition()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', [], 'Admin.Notifications.Error') .
                ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
        } elseif (!method_exists($object, 'updatePosition') || !$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
            $this->errors[] = $this->trans('Failed to update the position.', [], 'Admin.Notifications.Error');
        } else {
            $id_identifier_str = ($id_identifier = (int) Tools::getValue($this->identifier)) ? '&' . $this->identifier . '=' . $id_identifier : '';
            $redirect = self::$currentIndex . '&' . $this->table . 'Orderby=position&' . $this->table . 'Orderway=asc&conf=5' . $id_identifier_str . '&token=' . $this->token;
            $this->redirect_after = $redirect;
        }

        return $object;
    }

    /**
     * Cancel all filters for this tab.
     *
     * @param string|null $list_id
     */
    public function processResetFilters($list_id = null)
    {
        if ($list_id === null) {
            $list_id = isset($this->list_id) ? $this->list_id : $this->table;
        }

        $prefix = $this->getCookieOrderByPrefix();
        $filters = $this->context->cookie->getFamily($prefix . $list_id . 'Filter_');
        foreach ($filters as $cookie_key => $filter) {
            if (strncmp($cookie_key, $prefix . $list_id . 'Filter_', 7 + Tools::strlen($prefix . $list_id)) == 0) {
                $key = substr($cookie_key, 7 + Tools::strlen($prefix . $list_id));
                if (is_array($this->fields_list) && array_key_exists($key, $this->fields_list)) {
                    $this->context->cookie->$cookie_key = null;
                }
                unset($this->context->cookie->$cookie_key);
            }
        }

        if (isset($this->context->cookie->{'submitFilter' . $list_id})) {
            unset($this->context->cookie->{'submitFilter' . $list_id});
        }
        if (isset($this->context->cookie->{$prefix . $list_id . 'Orderby'})) {
            unset($this->context->cookie->{$prefix . $list_id . 'Orderby'});
        }
        if (isset($this->context->cookie->{$prefix . $list_id . 'Orderway'})) {
            unset($this->context->cookie->{$prefix . $list_id . 'Orderway'});
        }

        $_POST = [];
        $this->_filter = false;
        unset(
            $this->_filterHaving,
            $this->_having
        );
    }

    /**
     * Update options and preferences.
     */
    protected function processUpdateOptions()
    {
        $this->beforeUpdateOptions();

        $languages = Language::getLanguages(false);

        $hide_multishop_checkbox = (Shop::getTotalShops(false, null) < 2) ? true : false;
        foreach ($this->fields_options as $category_data) {
            if (!isset($category_data['fields'])) {
                continue;
            }

            $fields = $category_data['fields'];

            foreach ($fields as $field => $values) {
                if (isset($values['type']) && $values['type'] == 'selectLang') {
                    foreach ($languages as $lang) {
                        if (Tools::getValue($field . '_' . strtoupper($lang['iso_code']))) {
                            $fields[$field . '_' . strtoupper($lang['iso_code'])] = [
                                'type' => 'select',
                                'cast' => 'strval',
                                'identifier' => 'mode',
                                'list' => $values['list'],
                            ];
                        }
                    }
                }
            }

            // Validate fields
            foreach ($fields as $field => $values) {
                // We don't validate fields with no visibility
                if (!$hide_multishop_checkbox && Shop::isFeatureActive() && isset($values['visibility']) && $values['visibility'] > Shop::getContext()) {
                    continue;
                }

                // Check if field is required
                if ((!Shop::isFeatureActive() && !empty($values['required']))
                    || (Shop::isFeatureActive() && isset($_POST['multishopOverrideOption'][$field]) && !empty($values['required']))) {
                    if (isset($values['type']) && $values['type'] == 'textLang') {
                        foreach ($languages as $language) {
                            if (($value = Tools::getValue($field . '_' . $language['id_lang'])) == false && (string) $value != '0') {
                                $this->errors[] = $this->trans('field %s is required.', [$values['title']], 'Admin.Notifications.Error');
                            }
                        }
                    } elseif (($value = Tools::getValue($field)) == false && (string) $value != '0') {
                        $this->errors[] = $this->trans('field %s is required.', [$values['title']], 'Admin.Notifications.Error');
                    }
                }
                // Check if field value on type select is valid
                if (isset($values['type']) && $values['type'] === 'select' && isset($values['identifier']) && !empty($values['list'])) {
                    if (false !== ($value = Tools::getValue($field)) && false === in_array($value, array_column($values['list'], $values['identifier']))) {
                        $this->errors[] = $this->trans('The option selected in the %s field is invalid.', [$values['title']], 'Admin.Notifications.Error');
                    }
                }

                // Check field validator
                if (isset($values['type']) && $values['type'] == 'textLang') {
                    foreach ($languages as $language) {
                        if (Tools::getValue($field . '_' . $language['id_lang']) && isset($values['validation'])) {
                            $values_validation = $values['validation'];
                            if (!Validate::$values_validation(Tools::getValue($field . '_' . $language['id_lang']))) {
                                $this->errors[] = $this->trans('The %s field is invalid.', [$values['title']], 'Admin.Notifications.Error');
                            }
                        }
                    }
                } elseif (Tools::getValue($field) && isset($values['validation'])) {
                    $values_validation = $values['validation'];
                    if (!Validate::$values_validation(Tools::getValue($field))) {
                        $this->errors[] = $this->trans('The %s field is invalid.', [$values['title']], 'Admin.Notifications.Error');
                    }
                }

                // Set default value
                if (Tools::getValue($field) === false && isset($values['default'])) {
                    $_POST[$field] = $values['default'];
                }
            }

            if (!count($this->errors)) {
                foreach ($fields as $key => $options) {
                    if (Shop::isFeatureActive() && isset($options['visibility']) && $options['visibility'] > Shop::getContext()) {
                        continue;
                    }

                    if (!$hide_multishop_checkbox && Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && empty($options['no_multishop_checkbox']) && empty($_POST['multishopOverrideOption'][$key])) {
                        Configuration::deleteFromContext($key);

                        continue;
                    }

                    // check if a method updateOptionFieldName is available
                    $method_name = 'updateOption' . Tools::toCamelCase($key, true);
                    if (method_exists($this, $method_name)) {
                        $this->$method_name(Tools::getValue($key));
                    } elseif (isset($options['type']) && in_array($options['type'], ['textLang', 'textareaLang'])) {
                        $list = [];
                        foreach ($languages as $language) {
                            $key_lang = Tools::getValue($key . '_' . $language['id_lang']);
                            $val = (isset($options['cast']) ? $options['cast']($key_lang) : $key_lang);
                            if ($this->validateField($val, $options)) {
                                if (Validate::isCleanHtml($val)) {
                                    $list[$language['id_lang']] = $val;
                                } else {
                                    $this->errors[] = $this->trans('Cannot add configuration %1$s for %2$s language', [$key, Language::getIsoById((int) $language['id_lang'])], 'Admin.International.Notification');
                                }
                            }
                        }
                        Configuration::updateValue($key, $list, isset($options['validation']) && $options['validation'] == 'isCleanHtml' ? true : false);
                    } else {
                        $val = (isset($options['cast']) ? $options['cast'](Tools::getValue($key)) : Tools::getValue($key));
                        if ($this->validateField($val, $options)) {
                            if (Validate::isCleanHtml($val)) {
                                Configuration::updateValue($key, $val);
                            } else {
                                $this->errors[] = $this->trans('Cannot add configuration %s', [$key], 'Admin.Notifications.Error');
                            }
                        }
                    }
                }
            }
        }

        $this->display = 'list';
        if (empty($this->errors)) {
            $this->confirmations[] = $this->_conf[6];
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->toolbar_title)) {
            $this->initToolbarTitle();
        }

        if (!is_array($this->toolbar_title)) {
            $this->toolbar_title = [$this->toolbar_title];
        }

        switch ($this->display) {
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->page_header_toolbar_btn['back'] = [
                        'href' => $back,
                        'desc' => $this->trans('Back to list', [], 'Admin.Actions'),
                    ];
                }
                $obj = $this->loadObject(true);
                if (Validate::isLoadedObject($obj) && isset($obj->{$this->identifier_name}) && !empty($obj->{$this->identifier_name})) {
                    array_pop($this->toolbar_title);
                    array_pop($this->meta_title);
                    $this->toolbar_title[] = is_array($obj->{$this->identifier_name}) ? $obj->{$this->identifier_name}[$this->context->employee->id_lang] : $obj->{$this->identifier_name};
                    $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
                }

                break;
            case 'edit':
                $obj = $this->loadObject(true);
                if (Validate::isLoadedObject($obj) && isset($obj->{$this->identifier_name}) && !empty($obj->{$this->identifier_name})) {
                    array_pop($this->toolbar_title);
                    array_pop($this->meta_title);
                    $this->toolbar_title[] = $this->trans(
                        'Edit: %s',
                        [
                            (is_array($obj->{$this->identifier_name})
                                && isset($obj->{$this->identifier_name}[$this->context->employee->id_lang])
                            )
                                ? htmlspecialchars($obj->{$this->identifier_name}[$this->context->employee->id_lang])
                                : htmlspecialchars($obj->{$this->identifier_name}),
                        ],
                        'Admin.Actions'
                    );
                    $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
                }

                break;
        }

        // Add the extra toolbar buttons provided by hooks
        $this->mergeExtraToolbarButtons();

        if (count($this->toolbar_title)) {
            $this->show_page_header_toolbar = true;
        }

        if (empty($this->page_header_toolbar_title)) {
            $this->page_header_toolbar_title = $this->toolbar_title[count($this->toolbar_title) - 1];
        }

        $this->context->smarty->assign('help_link', 'https://help.prestashop-project.org/' . Language::getIsoById($this->context->employee->id_lang) . '/doc/'
            . Tools::getValue('controller') . '?version=' . _PS_VERSION_ . '&country=' . Language::getIsoById($this->context->employee->id_lang));
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items.
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'add':
            case 'edit':
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save'] = [
                    'href' => '#',
                    'desc' => $this->trans('Save', [], 'Admin.Actions'),
                ];
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['cancel'] = [
                        'href' => $back,
                        'desc' => $this->trans('Cancel', [], 'Admin.Actions'),
                    ];
                }

                break;
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex . '&token=' . $this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['back'] = [
                        'href' => $back,
                        'desc' => $this->trans('Back to list', [], 'Admin.Actions'),
                    ];
                }

                break;
            case 'options':
                $this->toolbar_btn['save'] = [
                    'href' => '#',
                    'desc' => $this->trans('Save', [], 'Admin.Actions'),
                ];

                break;
            default:
                // list
                $this->toolbar_btn['new'] = [
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->trans('Add new', [], 'Admin.Actions'),
                ];
                if ($this->allow_export) {
                    $this->toolbar_btn['export'] = [
                        'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
                        'desc' => $this->trans('Export', [], 'Admin.Actions'),
                    ];
                }
        }
    }

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die.
     *
     * @param bool $opt Return an empty object if load fail
     *
     * @return ObjectModel|bool
     */
    protected function loadObject($opt = false)
    {
        if (!isset($this->className) || empty($this->className)) {
            return true;
        }

        $id = (int) Tools::getValue($this->identifier);
        if ($id && Validate::isUnsignedId($id)) {
            if (!$this->object) {
                $this->object = new $this->className($id);
            }
            if (Validate::isLoadedObject($this->object)) {
                return $this->object;
            }
            // throw exception
            $this->errors[] = $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error');

            return false;
        } elseif ($opt) {
            if (!$this->object) {
                $this->object = new $this->className();
            }

            return $this->object;
        } else {
            $this->errors[] = $this->trans('The object cannot be loaded (the identifier is missing or invalid)', [], 'Admin.Notifications.Error');

            return false;
        }
    }

    /**
     * Check if the token is valid, else display a warning page.
     *
     * @return bool
     */
    public function checkAccess()
    {
        if (!$this->checkToken()) {
            // If this is an XSS attempt, then we should only display a simple, secure page
            // ${1} in the replacement string of the regexp is required,
            // because the token may begin with a number and mix up with it (e.g. $17)
            $url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}' . $this->token . '$2', $_SERVER['REQUEST_URI']);
            if (false === strpos($url, '?token=') && false === strpos($url, '&token=')) {
                $url .= '&token=' . $this->token;
            }
            if (strpos($url, '?') === false) {
                $url = str_replace('&token', '?controller=AdminDashboard&token', $url);
            }

            $this->context->smarty->assign('url', htmlentities($url));

            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @param string $filter
     *
     * @return array|false
     */
    protected function filterToField($key, $filter)
    {
        if (!isset($this->fields_list)) {
            return false;
        }

        foreach ($this->fields_list as $field) {
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key) {
                return $field;
            }
        }
        if (array_key_exists($filter, $this->fields_list)) {
            return $this->fields_list[$filter];
        }

        return false;
    }

    public function displayAjax()
    {
        if ($this->json) {
            $this->context->smarty->assign([
                'json' => true,
                'status' => $this->status,
            ]);
        }
        $this->layout = 'layout-ajax.tpl';
        $this->display_header = false;
        $this->display_header_javascript = false;
        $this->display_footer = false;

        return $this->display();
    }

    protected function redirect()
    {
        Tools::redirectAdmin($this->redirect_after);
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function display()
    {
        $this->context->smarty->assign([
            'display_header' => $this->display_header,
            'display_header_javascript' => $this->display_header_javascript,
            'display_footer' => $this->display_footer,
            'js_def' => Media::getJsDef(),
            'toggle_navigation_url' => $this->context->link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
        ]);

        // Use page title from meta_title if it has been set else from the breadcrumbs array
        if (!$this->meta_title) {
            $this->meta_title = $this->toolbar_title;
        }
        if (is_array($this->meta_title)) {
            $this->meta_title = strip_tags(implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $this->meta_title));
        }
        $this->context->smarty->assign('meta_title', $this->meta_title);

        $template_dirs = $this->context->smarty->getTemplateDir() ?: [];

        // Check if header/footer have been overridden
        $dir = $this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . trim($this->override_folder, '\\/') . DIRECTORY_SEPARATOR;

        $header_tpl = file_exists($dir . 'header.tpl') ? $dir . 'header.tpl' : 'header.tpl';
        $page_header_toolbar = file_exists($dir . 'page_header_toolbar.tpl') ? $dir . 'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
        $footer_tpl = file_exists($dir . 'footer.tpl') ? $dir . 'footer.tpl' : 'footer.tpl';
        $tpl_action = $this->tpl_folder . $this->display . '.tpl';

        // Check if action template has been overridden
        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir . DIRECTORY_SEPARATOR . $tpl_action) && $this->display != 'view' && $this->display != 'options') {
                if (method_exists($this, $this->display . Tools::toCamelCase($this->className))) {
                    $this->{$this->display . Tools::toCamelCase($this->className)}();
                }
                $this->context->smarty->assign('content', $this->context->smarty->fetch($tpl_action));

                break;
            }
        }

        if (!$this->ajax) {
            $template = $this->createTemplate($this->template);
            $page = $template->fetch();
        } else {
            $page = $this->content;
        }

        if ($conf = Tools::getValue('conf')) {
            $this->context->smarty->assign('conf', $this->json ? json_encode($this->_conf[(int) $conf]) : $this->_conf[(int) $conf]);
        }

        if ($error = Tools::getValue('error')) {
            $this->context->smarty->assign('error', $this->json ? json_encode($this->_error[(int) $error]) : $this->_error[(int) $error]);
        }

        foreach (['errors', 'warnings', 'informations', 'confirmations'] as $type) {
            if (!is_array($this->$type)) {
                $this->$type = (array) $this->$type;
            }

            if (
                SymfonyContainer::getInstance()
                    ?->get(FeatureFlagStateCheckerInterface::class)
                    ->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT)
            ) {
                /** @var AddFlashMessage $addFlashMessage */
                $addFlashMessage = SymfonyContainer::getInstance()->get(AddFlashMessage::class);
                foreach ($this->$type as $message) {
                    $addFlashMessage->addMessage($type, $message);
                }
                $this->$type = [];
            }

            $this->context->smarty->assign($type, $this->json ? json_encode(array_unique($this->$type)) : array_unique($this->$type));
        }

        if ($this->show_page_header_toolbar && !$this->lite_display) {
            $this->context->smarty->assign(
                [
                    'page_header_toolbar' => $this->context->smarty->fetch($page_header_toolbar),
                    'page_header_toolbar_template' => $page_header_toolbar,
                ]
            );
        }

        $this->context->smarty->assign('baseAdminUrl', __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/');

        $this->context->smarty->assign(
            [
                'page' => $this->json ? json_encode($page) : $page,
                'header' => $this->context->smarty->fetch($header_tpl),
                'footer' => $this->context->smarty->fetch($footer_tpl),
            ]
        );

        $this->smartyOutputContent($this->layout);
    }

    /**
     * Add a warning message to display at the top of the page.
     *
     * @param string $msg
     */
    protected function displayWarning($msg)
    {
        $this->warnings[] = $msg;
    }

    /**
     * Add a info message to display at the top of the page.
     *
     * @param string $msg
     */
    protected function displayInformation($msg)
    {
        $this->informations[] = $msg;
    }

    /**
     * Assign smarty variables for the header.
     */
    public function initHeader()
    {
        header('Cache-Control: no-store, no-cache');

        $this->context->smarty->assign([
            'table' => $this->table,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'stock_management' => (int) Configuration::get('PS_STOCK_MANAGEMENT'),
            'no_order_tip' => $this->getNotificationTip('order'),
            'no_customer_tip' => $this->getNotificationTip('customer'),
            'no_customer_message_tip' => $this->getNotificationTip('customer_message'),
        ]);

        if ($this->display_header) {
            $this->context->smarty->assign(
                'displayBackOfficeHeader',
                Hook::exec('displayBackOfficeHeader')
            );
        }

        // Fetch Employee Menu
        $menuLinksCollections = new ActionsBarButtonsCollection();
        Hook::exec(
            'displayBackOfficeEmployeeMenu',
            [
                'links' => $menuLinksCollections,
            ],
            null,
            true
        );

        $this->context->smarty->assign([
            'displayBackOfficeTop' => Hook::exec('displayBackOfficeTop'),
            'displayBackOfficeEmployeeMenu' => $menuLinksCollections,
            'submit_form_ajax' => (int) Tools::getValue('submitFormAjax'),
        ]);

        // Multishop
        $is_multishop = Shop::isFeatureActive();

        // Quick access
        if ((int) $this->context->employee->id) {
            $quick_access = QuickAccess::getQuickAccessesWithToken($this->context->language->id, (int) $this->context->employee->id);
        }

        $tabs = $this->getTabs();
        Hook::exec('actionAdminMenuTabsModifier', ['tabs' => &$tabs], null, true);

        $currentTabLevel = 0;
        foreach ($tabs as $tab) {
            $currentTabLevel = isset($tab['current_level']) ? $tab['current_level'] : $currentTabLevel;
        }

        if (Validate::isLoadedObject($this->context->employee)) {
            $accesses = Profile::getProfileAccesses($this->context->employee->id_profile, 'class_name');
            $helperShop = new HelperShop();
            /* Hooks are voluntary out the initialize array (need those variables already assigned) */
            $bo_color = empty($this->context->employee->bo_color) ? '#FFFFFF' : $this->context->employee->bo_color;
            $this->context->smarty->assign([
                'help_box' => Configuration::get('PS_HELPBOX'),
                'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
                'brightness' => (new ColorBrightnessCalculator())->isBright($bo_color) ? '#383838' : 'white',
                'bo_width' => (int) $this->context->employee->bo_width,
                'bo_color' => isset($this->context->employee->bo_color) ? Tools::htmlentitiesUTF8($this->context->employee->bo_color) : null,
                'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS') && isset($accesses['AdminOrders']) && $accesses['AdminOrders']['view'],
                'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS') && isset($accesses['AdminCustomers']) && $accesses['AdminCustomers']['view'],
                'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES') && isset($accesses['AdminCustomerThreads']) && $accesses['AdminCustomerThreads']['view'],
                'employee' => $this->context->employee,
                'search_type' => Tools::getValue('bo_search_type'),
                'bo_query' => Tools::safeOutput(Tools::getValue('bo_query')),
                'quick_access' => empty($quick_access) ? false : $quick_access,
                'multi_shop' => Shop::isFeatureActive(),
                'shop_list' => $helperShop->getRenderedShopList(),
                'current_shop_name' => $helperShop->getCurrentShopName(),
                'shop' => $this->context->shop,
                'shop_group' => new ShopGroup((int) Shop::getContextShopGroupID()),
                'is_multishop' => $is_multishop,
                'multishop_context' => $this->multishop_context,
                'default_tab_link' => $this->context->link->getAdminLink(Tab::getClassNameById((int) Context::getContext()->employee->default_tab)),
                'login_link' => $this->context->link->getAdminLink('AdminLogin'),
                'logout_link' => $this->context->link->getAdminLink('AdminLogin', true, [], ['logout' => 1]),
                'collapse_menu' => isset($this->context->cookie->collapse_menu) ? (int) $this->context->cookie->collapse_menu : 0,
            ]);
        } else {
            $this->context->smarty->assign('default_tab_link', $this->context->link->getAdminLink('AdminDashboard'));
        }

        // Shop::initialize() in config.php may empty $this->context->shop->virtual_uri so using a new shop instance for getBaseUrl()
        $this->context->shop = new Shop((int) $this->context->shop->id);

        $this->context->smarty->assign([
            'img_dir' => _PS_IMG_,
            'iso' => $this->context->language->iso_code,
            'class_name' => $this->className,
            'iso_user' => $this->context->language->iso_code,
            'lang_is_rtl' => $this->context->language->is_rtl,
            'country_iso_code' => $this->context->country->iso_code,
            'version' => _PS_VERSION_,
            'lang_iso' => $this->context->language->iso_code,
            'full_language_code' => $this->context->language->language_code,
            'full_cldr_language_code' => $this->context->getCurrentLocale()->getCode(),
            'link' => $this->context->link,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'base_url' => $this->context->shop->getBaseURL(),
            'current_parent_id' => (int) Tab::getCurrentParentId(),
            'tabs' => $tabs,
            'current_tab_level' => $currentTabLevel,
            'install_dir_exists' => file_exists(_PS_ADMIN_DIR_ . '/../install'),
            'pic_dir' => _THEME_PROD_PIC_DIR_,
            'controller_name' => htmlentities(Tools::getValue('controller')),
            'currentIndex' => self::$currentIndex,
            'bootstrap' => $this->bootstrap,
            'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
        ]);
    }

    private function getNotificationTip($type)
    {
        $tips = [
            'order' => [
                $this->trans(
                    'Have you checked your [1][2]abandoned carts[/2][/1]?[3]Your next order could be hiding there!',
                        [
                            '_raw' => true,
                            '[1]' => '<strong>',
                            '[/1]' => '</strong>',
                            '[2]' => '<a href="' . $this->context->link->getAdminLink('AdminCarts', true, [], ['action' => 'filterOnlyAbandonedCarts']) . '">',
                            '[/2]' => '</a>',
                            '[3]' => '<br>',
                        ],
                        'Admin.Navigation.Notification'
                ),
            ],
            'customer' => [
                $this->trans('Are you active on social media these days?', [], 'Admin.Navigation.Notification'),
            ],
            'customer_message' => [
                $this->trans('Seems like all your customers are happy :)', [], 'Admin.Navigation.Notification'),
            ],
        ];

        if (!isset($tips[$type])) {
            return '';
        }

        return $tips[$type][array_rand($tips[$type])];
    }

    private function getTabs($parentId = 0, $level = 0)
    {
        $tabs = Tab::getTabs($this->context->language->id, $parentId);
        $current_id = Tab::getCurrentParentId();

        foreach ($tabs as $index => $tab) {
            if (!Tab::checkTabRights($tab['id_tab'])
                || !$tab['enabled']
                || $tab['class_name'] == 'AdminStock'
                || $tab['class_name'] == 'AdminCarrierWizard') {
                unset($tabs[$index]);

                continue;
            }

            // tab[class_name] does not contains the "Controller" suffix
            if (($tab['class_name'] . 'Controller' == get_class($this)) || ($current_id == $tab['id_tab']) || $tab['class_name'] == $this->controller_name) {
                $tabs[$index]['current'] = true;
                $tabs[$index]['current_level'] = $level;
            } else {
                $tabs[$index]['current'] = false;
            }
            $tabs[$index]['img'] = null;
            try {
                $tabs[$index]['href'] = $this->context->link->getTabLink($tab);
            } catch (RouteNotFoundException $e) {
                // If the route specified is not accessible we remove the tab (it can happen during module install process
                // the route should be usable in next request/process once the cache has been cleared - on process shutdown).
                // This is not ideal, but clearing the cache during a process and restart the whole kernel is quite a challenge.
                $this->get('logger')->addWarning(
                    sprintf('Route not found in one of the Tab %s', $tab['route_name'] ?? ''),
                    [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ]
                );
                unset($tabs[$index]);
                continue;
            }
            $tabs[$index]['sub_tabs'] = array_values($this->getTabs($tab['id_tab'], $level + 1));

            $subTabHref = $this->getTabLinkFromSubTabs($tabs[$index]['sub_tabs']);
            if (!empty($subTabHref)) {
                $tabs[$index]['href'] = $subTabHref;
            } elseif (0 == $tabs[$index]['id_parent'] && '' == $tabs[$index]['icon']) {
                unset($tabs[$index]);
            } elseif (empty($tabs[$index]['icon'])) {
                $tabs[$index]['icon'] = 'extension';
            }

            if (array_key_exists($index, $tabs) && array_key_exists('sub_tabs', $tabs[$index])) {
                foreach ($tabs[$index]['sub_tabs'] as $sub_tab) {
                    if ((int) $sub_tab['current'] == true) {
                        $tabs[$index]['current'] = true;
                        $tabs[$index]['current_level'] = $sub_tab['current_level'];
                    }
                }
            }
        }

        return $tabs;
    }

    /**
     * Declare an action to use for each row in the list.
     *
     * @param string $action
     */
    public function addRowAction($action)
    {
        $action = strtolower($action);
        $this->actions[] = $action;
    }

    /**
     * Add an action to use for each row in the list.
     *
     * @param string $action
     * @param array $list
     */
    public function addRowActionSkipList($action, $list)
    {
        $action = strtolower($action);
        $list = (array) $list;

        if (array_key_exists($action, $this->list_skip_actions)) {
            $this->list_skip_actions[$action] = array_merge($this->list_skip_actions[$action], $list);
        } else {
            $this->list_skip_actions[$action] = $list;
        }
    }

    /**
     * Assign smarty variables for all default views, list and form, then call other init functions.
     */
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = $this->trans('You do not have permission to view this.', [], 'Admin.Notifications.Error');

            return;
        }

        if ($this->display == 'edit' || $this->display == 'add') {
            if (!$this->loadObject(true)) {
                return;
            }

            $this->content .= $this->renderForm();
        } elseif ($this->display == 'view') {
            // Some controllers use the view action without an object
            if ($this->className) {
                $this->loadObject(true);
            }
            $this->content .= $this->renderView();
        } elseif ($this->display == 'details') {
            $this->content .= $this->renderDetails();
        } elseif (!$this->ajax) {
            $this->content .= $this->renderKpis();
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();

            // if we have to display the required fields form
            if ($this->required_database) {
                $this->content .= $this->displayRequiredFields();
            }
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    public function initToolbarFlags()
    {
        $this->getLanguages();

        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $this->context->smarty->assign([
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
            'maintenance_allow_admins' => (bool) Configuration::get('PS_MAINTENANCE_ALLOW_ADMINS'),
            'debug_mode' => (bool) _PS_MODE_DEV_,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ]);
    }

    protected function getAdminModulesUrl()
    {
        return $this->context->link->getAdminLink('AdminModulesCatalog');
    }

    /**
     * Initialize the invalid doom page of death.
     */
    public function initCursedPage()
    {
        $this->layout = 'invalid_token.tpl';
    }

    /**
     * Assign smarty variables for the footer.
     */
    public function initFooter()
    {
        // We assign js and css files on the last step before display template, because controller can add many js and css files
        $this->context->smarty->assign('css_files', $this->css_files);
        $this->context->smarty->assign('js_files', array_unique($this->js_files));

        $this->context->smarty->assign([
            'ps_version' => _PS_VERSION_,
            'iso_is_fr' => strtoupper($this->context->language->iso_code) == 'FR',
            'modals' => $this->renderModal(),
        ]);
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initModal()
    {
        $this->context->smarty->assign([
            'img_base_path' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/',
            'check_url_fopen' => (ini_get('allow_url_fopen') ? 'ok' : 'ko'),
            'check_openssl' => (extension_loaded('openssl') ? 'ok' : 'ko'),
            'add_permission' => 1,
        ]);
    }

    /**
     * @return string
     *
     * @throws Exception
     * @throws SmartyException
     */
    public function renderModal()
    {
        $modal_render = '';
        if (is_array($this->modals) && count($this->modals)) {
            foreach ($this->modals as $modal) {
                $this->context->smarty->assign($modal);
                $modal_render .= $this->context->smarty->fetch('modal.tpl');
            }
        }

        return $modal_render;
    }

    /**
     * Function used to render the list to display for this controller.
     *
     * @return string|false
     *
     * @throws PrestaShopException
     */
    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        // If list has 'active' field, we automatically create bulk action
        if (array_key_exists('active', $this->fields_list) && $this->fields_list['active'] == true) {
            if (!is_array($this->bulk_actions)) {
                $this->bulk_actions = [];
            }

            $this->bulk_actions = array_merge([
                'enableSelection' => [
                    'text' => $this->trans('Enable selection', [], 'Admin.Actions'),
                    'icon' => 'icon-power-off text-success',
                ],
                'disableSelection' => [
                    'text' => $this->trans('Disable selection', [], 'Admin.Actions'),
                    'icon' => 'icon-power-off text-danger',
                ],
                'divider' => [
                    'text' => 'divider',
                ],
            ], $this->bulk_actions);
        }

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query', [], 'Admin.Notifications.Error') . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->_default_pagination = $this->_default_pagination;
        $helper->_pagination = $this->_pagination;
        $helper->tpl_vars = $this->getTemplateListVars();
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }

        $helper->is_cms = $this->is_cms;
        /* @phpstan-ignore-next-line */
        $helper->sql = $this->_listsql;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    public function getTemplateListVars()
    {
        return $this->tpl_list_vars;
    }

    /**
     * Override to render the view page.
     *
     * @return string
     */
    public function renderView()
    {
        $helper = new HelperView();
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->getTemplateViewVars();
        if (null !== $this->base_tpl_view) {
            $helper->base_tpl = $this->base_tpl_view;
        }

        return $helper->generateView();
    }

    public function getTemplateViewVars()
    {
        return $this->tpl_view_vars;
    }

    /**
     * Override to render the view page.
     *
     * @return string|false
     */
    public function renderDetails()
    {
        return $this->renderList();
    }

    /**
     * Function used to render the form for this controller.
     *
     * @return string
     *
     * @throws Exception
     * @throws SmartyException
     */
    public function renderForm()
    {
        if (!$this->default_form_language) {
            $this->getLanguages();
        }

        if (Tools::getValue('submitFormAjax')) {
            $this->content .= $this->context->smarty->fetch('form_submit_ajax.tpl');
        }

        if ($this->fields_form && is_array($this->fields_form)) {
            if (!$this->multiple_fieldsets) {
                $this->fields_form = [['form' => $this->fields_form]];
            }

            // For add a fields via an override of $fields_form, use $fields_form_override
            if (is_array($this->fields_form_override) && !empty($this->fields_form_override)) {
                $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'], $this->fields_form_override);
            }

            $fields_value = $this->getFieldsValue($this->object);

            Hook::exec('action' . $this->controller_name . 'FormModifier', [
                'object' => &$this->object,
                'fields' => &$this->fields_form,
                'fields_value' => &$fields_value,
                'form_vars' => &$this->tpl_form_vars,
            ]);

            $helper = new HelperForm();
            $this->setHelperDisplay($helper);
            $helper->fields_value = $fields_value;
            $helper->submit_action = $this->submit_action;
            $helper->tpl_vars = $this->getTemplateFormVars();
            $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');

            $back = rawurldecode(Tools::getValue('back', ''));
            if (empty($back)) {
                $back = self::$currentIndex . '&token=' . $this->token;
            }
            if (!Validate::isCleanHtml($back)) {
                die(Tools::displayError());
            }

            $helper->back_url = $back;
            null !== $this->base_tpl_form ? $helper->base_tpl = $this->base_tpl_form : '';
            if ($this->access('view')) {
                if (Tools::getValue('back')) {
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
                } else {
                    $helper->tpl_vars['back'] = Tools::safeOutput(self::$currentIndex . '&token=' . $this->token);
                }
            }
            $form = $helper->generateForm($this->fields_form);

            return $form;
        }

        return '';
    }

    public function getTemplateFormVars()
    {
        return $this->tpl_form_vars;
    }

    public function renderKpis()
    {
    }

    /**
     * Function used to render the options for this controller.
     *
     * @return string
     */
    public function renderOptions()
    {
        Hook::exec('action' . $this->controller_name . 'OptionsModifier', [
            'options' => &$this->fields_options,
            'option_vars' => &$this->tpl_option_vars,
        ]);

        if ($this->fields_options && is_array($this->fields_options)) {
            if (isset($this->display) && $this->display != 'options' && $this->display != 'list') {
                $this->show_toolbar = false;
            } else {
                $this->display = 'options';
            }

            unset($this->toolbar_btn);
            $this->initToolbar();
            $helper = new HelperOptions();
            $this->setHelperDisplay($helper);
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }

        return '';
    }

    /**
     * This function sets various display options for helper list.
     *
     * @param HelperList|HelperView|HelperOptions|HelperForm $helper
     */
    public function setHelperDisplay(Helper $helper)
    {
        if (empty($this->toolbar_title)) {
            $this->initToolbarTitle();
        }
        // tocheck
        if ($this->object && $this->object->id) {
            $helper->id = $this->object->id;
        }

        // @todo : move that in Helper
        $helper->title = is_array($this->toolbar_title) ? implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $this->toolbar_title) : $this->toolbar_title;
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->show_toolbar = $this->show_toolbar;
        $helper->toolbar_scroll = $this->toolbar_scroll;
        $helper->override_folder = $this->tpl_folder;
        $helper->currentIndex = self::$currentIndex;
        $helper->table = $this->table;
        if ($helper->name_controller === null) {
            $helper->name_controller = Tools::getValue('controller');
        }
        $helper->identifier = $this->identifier;
        $helper->token = $this->token;
        if ($helper->allow_employee_form_lang === null) {
            $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        }
        if ($helper->position_group_identifier === null) {
            $helper->position_group_identifier = $this->position_group_identifier;
        }
        $helper->controller_name = $this->controller_name;
        $helper->bootstrap = $this->bootstrap;

        if ($helper instanceof HelperFormCore) {
            $helper->languages = $this->_languages;
            $helper->default_form_language = $this->default_form_language;
        }
        if ($helper instanceof HelperListCore) {
            // For each action, try to add the corresponding skip elements list
            $helper->list_skip_actions = $this->list_skip_actions;
            $helper->orderBy = $this->_orderBy;
            $helper->orderWay = $this->_orderWay;
            $helper->position_identifier = $this->position_identifier;
            $helper->row_hover = $this->row_hover;
            $helper->ajax_params = $this->ajax_params ?? null;
            $helper->no_link = $this->list_no_link;
            $helper->colorOnBackground = $this->colorOnBackground;
            $helper->specificConfirmDelete = $this->specificConfirmDelete;
            $helper->imageType = $this->imageType;
            $helper->list_id = $this->list_id ?? $this->table;
            $helper->shopLinkType = $this->shopLinkType;
            $helper->listTotal = $this->_listTotal;
            $helper->simple_header = $this->list_simple_header;
            $helper->bulk_actions = $this->bulk_actions;
            $helper->actions = $this->actions;
        }

        $this->helper = $helper;
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        if ($isNewTheme) {
            if ($this->context->language->is_rtl) {
                $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/rtl.css?v=' . _PS_VERSION_, 'all', 1);
            }

            $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/theme.css?v=' . _PS_VERSION_, 'all', 0);
            $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/main.bundle.js?v=' . _PS_VERSION_);

            // the multistore dropdown should be called only once, and only if multistore is used
            if ($this->isMultistoreEnabled()) {
                $this->addJs(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/multistore_dropdown.bundle.js?v=' . _PS_VERSION_);
            }
            $this->addJqueryPlugin(['chosen', 'fancybox']);
        } else {
            if ($this->context->language->is_rtl) {
                $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/public/rtl.css?v=' . _PS_VERSION_, 'all', 0);
            }

            //Bootstrap
            $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/css/' . $this->bo_css . '?v=' . _PS_VERSION_, 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/css/vendor/titatoggle-min.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/public/theme.css?v=' . _PS_VERSION_, 'all', 0);

            // add Jquery 3 and its migration script
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery-3.5.1.min.js');
            $this->addJs(_PS_JS_DIR_ . 'jquery/bo-migrate-mute.min.js');
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery-migrate-3.1.0.min.js');
            // implement $.browser object and live method, that has been removed since jquery 1.9
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery.browser-0.1.0.min.js');
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery.live-polyfill-1.1.2.min.js');

            $this->addJqueryPlugin(['scrollTo', 'alerts', 'chosen', 'autosize', 'fancybox']);
            $this->addJqueryPlugin('growl', null, false);
            $this->addJqueryUI(['ui.slider', 'ui.datepicker']);

            $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/vendor/bootstrap.min.js');
            $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/vendor/modernizr.min.js');
            $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/modernizr-loads.js');
            $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/vendor/moment-with-langs.min.js');
            $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/public/theme.bundle.js?v=' . _PS_VERSION_);

            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

            if (!$this->lite_display) {
                $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/help.js?v=' . _PS_VERSION_);
            }

            if (!Tools::getValue('submitFormAjax')) {
                $this->addJS(_PS_JS_DIR_ . 'admin/notifications.js?v=' . _PS_VERSION_);
            }

            $username = $this->get('prestashop.user_provider')->getUsername();
            $token = $this->get('security.csrf.token_manager')
                ->getToken($username)
                ->getValue();

            $this->context->smarty->assign([
                'js_router_metadata' => [
                    'base_url' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
                    'token' => $token,
                ],
            ]);
        }

        // Specific Admin Theme
        $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/css/overrides.css', 'all', PHP_INT_MAX);

        $this->addCSS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/create_product_default_theme.css?v=' . _PS_VERSION_, 'all', 0);
        $this->addJS([
            _PS_JS_DIR_ . 'admin.js?v=' . _PS_VERSION_, // TODO: SEE IF REMOVABLE
            __PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/cldr.bundle.js?v=' . _PS_VERSION_,
            _PS_JS_DIR_ . 'tools.js?v=' . _PS_VERSION_,
            __PS_BASE_URI__ . $this->admin_webpath . '/public/bundle.js?v=' . _PS_VERSION_,
        ]);

        // This is handled as an external common dependency for both themes, but once new-theme is the only one it should be integrated directly into the main.bundle.js file
        $this->addJS(__PS_BASE_URI__ . $this->admin_webpath . '/themes/new-theme/public/create_product.bundle.js?v=' . _PS_VERSION_);

        Media::addJsDef([
            'changeFormLanguageUrl' => $this->context->link->getAdminLink(
                'AdminEmployees',
                true,
                [],
                ['action' => 'formLanguage']
            ),
        ]);
        Media::addJsDef(['baseDir' => __PS_BASE_URI__]);
        Media::addJsDef(['baseAdminDir' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/']);
        Media::addJsDef(['currency' => [
            'iso_code' => Context::getContext()->currency->iso_code,
            'sign' => Context::getContext()->currency->sign,
            'name' => Context::getContext()->currency->name,
            'format' => Context::getContext()->currency->format,
        ]]);
        Media::addJsDef(
            [
                'currency_specifications' => $this->preparePriceSpecifications($this->context),
                'number_specifications' => $this->prepareNumberSpecifications($this->context),
            ]
        );

        Media::addJsDef([
            'prestashop' => [
                'debug' => _PS_MODE_DEV_,
            ],
        ]);

        // Execute Hook AdminController SetMedia
        Hook::exec('actionAdminControllerSetMedia');
    }

    /**
     * Init context and dependencies, handles POST and GET.
     */
    public function init()
    {
        Hook::exec(
            'actionAdminControllerInitBefore',
            [
                'controller' => $this,
            ]
        );
        parent::init();

        if (Tools::getValue('ajax')) {
            $this->ajax = true;
        }

        if (null === $this->context->link) {
            $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $this->context->link = new Link($protocol_link, $protocol_content);
        }

        if (isset($_GET['logout'])) {
            $this->context->employee->logout();
        }
        if (isset(Context::getContext()->cookie->last_activity)) {
            if (((int) $this->context->cookie->last_activity) + self::AUTH_COOKIE_LIFETIME < time()) {
                $this->context->employee->logout();
            } else {
                $this->context->cookie->last_activity = time();
            }
        }

        if (
            !$this->isAnonymousAllowed()
            && (
                $this->controller_name != 'AdminLogin'
                && (
                    !isset($this->context->employee)
                    || !$this->context->employee->isLoggedBack()
                )
            )
        ) {
            if (isset($this->context->employee)) {
                $this->context->employee->logout();
            }
            $email = false;
            if (Tools::getValue('email') && Validate::isEmail(Tools::getValue('email'))) {
                $email = Tools::getValue('email');
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminLogin') . ((!isset($_GET['logout']) && $this->controller_name != 'AdminNotFound' && Tools::getValue('controller')) ? '&redirect=' . $this->controller_name : '') . ($email ? '&email=' . $email : ''));
        }

        // Set current index
        $current_index = 'index.php' . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');
        if ($back = Tools::getValue('back')) {
            $current_index .= '&back=' . urlencode($back);
        }
        self::$currentIndex = $current_index;

        if ((int) Tools::getValue('liteDisplaying')) {
            $this->display_header = false;
            $this->display_header_javascript = true;
            $this->display_footer = false;
            $this->content_only = false;
            $this->lite_display = true;
        }

        if ($this->ajax && method_exists($this, 'ajaxPreprocess')) {
            $this->ajaxPreProcess();
        }

        Employee::setLastConnectionDate($this->context->employee->id);

        $this->initProcess();
        $this->initMultistoreHeader();
        $this->initBreadcrumbs();
        $this->initModal();
        $this->initToolbarFlags();
        $this->initNotifications();
        Hook::exec(
            'actionAdminControllerInitAfter',
            [
                'controller' => $this,
            ]
        );
    }

    /**
     * Sets the smarty variables and js defs used to show / hide some notifications.
     */
    public function initNotifications()
    {
        $notificationsSettings = [
            'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS'),
            'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS'),
            'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES'),
        ];
        $this->context->smarty->assign($notificationsSettings);

        Media::addJsDef($notificationsSettings);
    }

    /**
     * @throws PrestaShopException
     */
    public function initShopContext()
    {
        // Do not initialize context when the shop is not installed
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            return;
        }

        // Change shop context ?
        if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false) {
            $this->context->cookie->shopContext = Tools::getValue('setShopContext');
            $this->redirect_after = UrlCleaner::cleanUrl($_SERVER['REQUEST_URI'], ['setShopContext', 'conf']);
        } elseif (!Shop::isFeatureActive()) {
            $this->context->cookie->shopContext = 's-' . (int) Configuration::get('PS_SHOP_DEFAULT');
        } elseif (Shop::getTotalShops(false, null) < 2 && $this->context->employee->isLoggedBack()) {
            $this->context->cookie->shopContext = 's-' . (int) $this->context->employee->getDefaultShopID();
        }

        $shop_id = null;
        Shop::setContext(Shop::CONTEXT_ALL);
        if ($this->context->cookie->shopContext && $this->context->employee->isLoggedBack()) {
            $split = explode('-', $this->context->cookie->shopContext);
            if (count($split) == 2) {
                if ($split[0] == 'g') {
                    if ($this->context->employee->hasAuthOnShopGroup((int) $split[1])) {
                        Shop::setContext(Shop::CONTEXT_GROUP, (int) $split[1]);
                    } else {
                        $shop_id = (int) $this->context->employee->getDefaultShopID();
                        Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                    }
                } elseif (Shop::getShop((int) $split[1]) && $this->context->employee->hasAuthOnShop((int) $split[1])) {
                    $shop_id = (int) $split[1];
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                } else {
                    $shop_id = (int) $this->context->employee->getDefaultShopID();
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                }
            }
        }

        // Check multishop context and set right context if need
        if (!($this->multishop_context & Shop::getContext())) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP && !($this->multishop_context & Shop::CONTEXT_SHOP)) {
                Shop::setContext(Shop::CONTEXT_GROUP, Shop::getContextShopGroupID());
            }
            if (Shop::getContext() == Shop::CONTEXT_GROUP && !($this->multishop_context & Shop::CONTEXT_GROUP)) {
                Shop::setContext(Shop::CONTEXT_ALL);
            }
        }

        // Replace existing shop if necessary
        if (!$shop_id) {
            $this->context->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
        } elseif ($this->context->shop->id != $shop_id) {
            $this->context->shop = new Shop((int) $shop_id);
        }

        // Replace current default country
        $this->context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $this->context->currency = Currency::getDefaultCurrency();
    }

    /**
     * Retrieve GET and POST value and translate them to actions.
     */
    public function initProcess()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        // Manage list filtering
        if (Tools::isSubmit('submitFilter' . $this->list_id)
            || $this->context->cookie->{'submitFilter' . $this->list_id} !== false
            || Tools::getValue($this->list_id . 'Orderby')
            || Tools::getValue($this->list_id . 'Orderway')) {
            $this->filter = true;
        }

        $this->id_object = (int) Tools::getValue($this->identifier);

        /* Delete object image */
        if (isset($_GET['deleteImage'])) {
            if ($this->access('delete')) {
                $this->action = 'delete_image';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['delete' . $this->table])) {
            /* Delete object */
            if ($this->access('delete')) {
                $this->action = 'delete';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
            }
        } elseif ((isset($_GET['status' . $this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier)) {
            /* Change object status (active, inactive) */
            if ($this->access('edit')) {
                $this->action = 'status';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['position'])) {
            /* Move an object */
            if ($this->access('edit') == '1') {
                $this->action = 'position';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitAdd' . $this->table)
                 || Tools::isSubmit('submitAdd' . $this->table . 'AndStay')
                 || Tools::isSubmit('submitAdd' . $this->table . 'AndPreview')
                 || Tools::isSubmit('submitAdd' . $this->table . 'AndBackToParent')) {
            // case 1: updating existing entry
            if ($this->id_object) {
                if ($this->access('edit')) {
                    $this->action = 'save';
                    if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                        $this->display = 'edit';
                    } else {
                        $this->display = 'list';
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                }
            } else {
                // case 2: creating new entry
                if ($this->access('add')) {
                    $this->action = 'save';
                    if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                        $this->display = 'edit';
                    } else {
                        $this->display = 'list';
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
                }
            }
        } elseif (isset($_GET['add' . $this->table])) {
            if ($this->access('add')) {
                $this->action = 'new';
                $this->display = 'add';
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['update' . $this->table], $_GET[$this->identifier])) {
            $this->display = 'edit';
            if (!$this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['view' . $this->table])) {
            if ($this->access('view')) {
                $this->display = 'view';
                $this->action = 'view';
            } else {
                $this->errors[] = $this->trans('You do not have permission to view this.', [], 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['details' . $this->table])) {
            if ($this->access('view')) {
                $this->display = 'details';
                $this->action = 'details';
            } else {
                $this->errors[] = $this->trans('You do not have permission to view this.', [], 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['export' . $this->table])) {
            if ($this->access('view')) {
                $this->action = 'export';
            }
        } elseif (isset($_POST['submitReset' . $this->list_id])) {
            /* Cancel all filters for this tab */
            $this->action = 'reset_filters';
        } elseif (Tools::isSubmit('submitOptions' . $this->table) || Tools::isSubmit('submitOptions')) {
            /* Submit options list */
            $this->display = 'options';
            if ($this->access('edit')) {
                $this->action = 'update_options';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::getValue('action') && method_exists($this, 'process' . ucfirst(Tools::toCamelCase(Tools::getValue('action'))))) {
            $this->action = Tools::getValue('action');
        } elseif (Tools::isSubmit('submitFields') && $this->required_database && $this->access('add') && $this->access('delete')) {
            $this->action = 'update_fields';
        } elseif (is_array($this->bulk_actions)) {
            $submit_bulk_actions = array_merge([
                'enableSelection' => [
                    'text' => $this->trans('Enable selection', [], 'Admin.Actions'),
                    'icon' => 'icon-power-off text-success',
                ],
                'disableSelection' => [
                    'text' => $this->trans('Disable selection', [], 'Admin.Actions'),
                    'icon' => 'icon-power-off text-danger',
                ],
            ], $this->bulk_actions);
            foreach ($submit_bulk_actions as $bulk_action => $params) {
                if (Tools::isSubmit('submitBulk' . $bulk_action . $this->table) || Tools::isSubmit('submitBulk' . $bulk_action)) {
                    if ($bulk_action === 'delete') {
                        if ($this->access('delete')) {
                            $this->action = 'bulk' . $bulk_action;
                            $this->boxes = Tools::getValue($this->table . 'Box');
                            if (empty($this->boxes) && $this->table == 'attribute') {
                                $this->boxes = Tools::getValue($this->table . '_valuesBox');
                            }
                        } else {
                            $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
                        }

                        break;
                    } elseif ($this->access('edit')) {
                        $this->action = 'bulk' . $bulk_action;
                        $this->boxes = Tools::getValue($this->table . 'Box');
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                    }

                    break;
                } elseif (Tools::isSubmit('submitBulk')) {
                    if ($bulk_action === 'delete') {
                        if ($this->access('delete')) {
                            $this->action = 'bulk' . $bulk_action;
                            $this->boxes = Tools::getValue($this->table . 'Box');
                        } else {
                            $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
                        }

                        break;
                    } elseif ($this->access('edit')) {
                        $this->action = 'bulk' . Tools::getValue('select_submitBulk');
                        $this->boxes = Tools::getValue($this->table . 'Box');
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
                    }

                    break;
                }
            }
        } elseif (!empty($this->fields_options) && empty($this->fields_list)) {
            $this->display = 'options';
        }
    }

    /**
     * Get the current objects' list from the database.
     *
     * @param int $id_lang Language used for display
     * @param string|null $order_by ORDER BY clause
     * @param string|null $order_way Order way (ASC, DESC)
     * @param int $start Offset in LIMIT clause
     * @param int|null $limit Row count in LIMIT clause
     * @param int|bool $id_lang_shop
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
            'select' => &$this->_select,
            'join' => &$this->_join,
            'where' => &$this->_where,
            'group_by' => &$this->_group,
            'order_by' => &$this->_orderBy,
            'order_way' => &$this->_orderWay,
            'fields' => &$this->fields_list,
        ]);

        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        if (!Validate::isTableOrIdentifier($this->table)) {
            throw new PrestaShopException(sprintf('Table name %s is invalid:', $this->table));
        }

        /* Check params validity */
        if (!is_numeric($start) || !Validate::isUnsignedId($id_lang)) {
            throw new PrestaShopException('get list params is not valid');
        }

        $limit = $this->checkSqlLimit($limit);

        /* Determine offset from current page */
        $start = 0;
        if ((int) Tools::getValue('submitFilter' . $this->list_id)) {
            $start = ((int) Tools::getValue('submitFilter' . $this->list_id) - 1) * $limit;
        } elseif (
            isset($this->context->cookie->{$this->list_id . '_start'})
            && Tools::isSubmit('export' . $this->table)
        ) {
            $start = $this->context->cookie->{$this->list_id . '_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->context->cookie->{$this->list_id . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$this->list_id . '_start'})) {
            unset($this->context->cookie->{$this->list_id . '_start'});
        }

        /* Cache */
        $this->_lang = (int) $id_lang;

        // Add SQL shop restriction
        $select_shop = '';
        if ($this->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($this->multishop_context && Shop::isTableAssociated($this->table) && !empty($this->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->context->employee->isSuperAdmin()) {
                // test if multishop is already considered by planned request
                $test_join = (null === $this->_join) || !preg_match('#`?' . preg_quote(_DB_PREFIX_ . $this->table . '_shop') . '`? *sa#', $this->_join);
                if (Shop::isFeatureActive() && $test_join) {
                    $this->_where .= ' AND EXISTS (
                        SELECT 1
                        FROM `' . _DB_PREFIX_ . $this->table . '_shop` sa
                        WHERE a.`' . bqSQL($this->identifier) . '` = sa.`' . bqSQL($this->identifier) . '`
                         AND sa.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')
                    )';
                }
            }
        }

        $fromClause = $this->getFromClause();
        $joinClause = $this->getJoinClause($id_lang, $id_lang_shop);
        $whereClause = $this->getWhereClause();
        $orderByClause = $this->getOrderByClause($order_by, $order_way);

        $shouldLimitSqlResults = $this->shouldLimitSqlResults($limit);

        do {
            $this->_listsql = '';

            if ($this->explicitSelect) {
                foreach ($this->fields_list as $key => $array_value) {
                    // Add it only if it is not already in $this->_select
                    if (isset($this->_select) && preg_match('/[\s]`?' . preg_quote($key, '/') . '`?\s*,/', $this->_select)) {
                        continue;
                    }

                    if (isset($array_value['filter_key'])) {
                        $this->_listsql .= str_replace('!', '.`', $array_value['filter_key']) . '` AS `' . $key . '`, ';
                    } elseif ($key == 'id_' . $this->table) {
                        $this->_listsql .= 'a.`' . bqSQL($key) . '`, ';
                    } elseif ($key != 'image' && !preg_match('/' . preg_quote($key, '/') . '/i', $this->_select)) {
                        $this->_listsql .= '`' . bqSQL($key) . '`, ';
                    }
                }
                $this->_listsql = rtrim(trim($this->_listsql), ',');
            } else {
                $this->_listsql .= ($this->lang ? 'b.*,' : '') . ' a.*';
            }

            $this->_listsql .= "\n" . (isset($this->_select) ? ', ' . rtrim($this->_select, ', ') : '') . $select_shop;

            $limitClause = ' ' . (($shouldLimitSqlResults) ? ' LIMIT ' . (int) $start . ', ' . (int) $limit : '');

            if ($this->_use_found_rows || isset($this->_filterHaving) || isset($this->_having)) {
                $this->_listsql = 'SELECT SQL_CALC_FOUND_ROWS ' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                    $this->_listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $this->table . '`';
            } else {
                $this->_listsql = 'SELECT ' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                    $this->_listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT COUNT(*) AS `' . _DB_PREFIX_ . $this->table . '` ' .
                    $fromClause .
                    $joinClause .
                    $whereClause;
            }

            $this->_list = Db::getInstance()->executeS($this->_listsql, true, false);

            if ($this->_list === false) {
                $this->_list_error = Db::getInstance()->getMsgError();

                break;
            }

            $this->_listTotal = (int) Db::getInstance()->getValue($list_count, false);

            if ($shouldLimitSqlResults) {
                $start = (int) $start - (int) $limit;
                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($this->_list));

        Hook::exec('action' . $this->controller_name . 'ListingResultsModifier', [
            'list' => &$this->_list,
            'list_total' => &$this->_listTotal,
        ]);
    }

    /**
     * @return string
     */
    protected function getFromClause()
    {
        $sql_table = $this->table == 'order' ? 'orders' : $this->table;

        return "\n" . 'FROM `' . _DB_PREFIX_ . $sql_table . '` a ';
    }

    /**
     * @param int $id_lang
     * @param int $id_lang_shop
     *
     * @return string
     */
    protected function getJoinClause($id_lang, $id_lang_shop)
    {
        $shopJoinClause = '';
        if ($this->shopLinkType) {
            $shopJoinClause = ' LEFT JOIN `' . _DB_PREFIX_ . bqSQL($this->shopLinkType) . '` shop
                            ON a.`id_' . bqSQL($this->shopLinkType) . '` = shop.`id_' . bqSQL($this->shopLinkType) . '`';
        }

        return "\n" . $this->getLanguageJoinClause($id_lang, $id_lang_shop) .
            "\n" . (isset($this->_join) ? $this->_join . ' ' : '') .
            "\n" . $shopJoinClause;
    }

    /**
     * @param int $idLang
     * @param int $idLangShop
     *
     * @return string
     */
    protected function getLanguageJoinClause($idLang, $idLangShop)
    {
        $languageJoinClause = '';
        if ($this->lang) {
            $languageJoinClause = 'LEFT JOIN `' . _DB_PREFIX_ . bqSQL($this->table) . '_lang` b
                ON (b.`' . bqSQL($this->identifier) . '` = a.`' . bqSQL($this->identifier) . '` AND b.`id_lang` = ' . (int) $idLang;

            if ($idLangShop) {
                if (!Shop::isFeatureActive()) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) Configuration::get('PS_SHOP_DEFAULT');
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) $idLangShop;
                } else {
                    $languageJoinClause .= ' AND b.`id_shop` = a.id_shop_default';
                }
            }
            $languageJoinClause .= ')';
        }

        return $languageJoinClause;
    }

    /**
     * @return string
     */
    protected function getWhereClause()
    {
        $whereShop = '';
        if ($this->shopLinkType) {
            $whereShop = Shop::addSqlRestriction($this->shopShareDatas, 'a');
        }
        $whereClause = ' WHERE 1 ' . (isset($this->_where) ? $this->_where . ' ' : '') .
            ($this->deleted ? 'AND a.`deleted` = 0 ' : '') .
            (isset($this->_filter) ? $this->_filter : '') . $whereShop . "\n" .
            (isset($this->_group) ? $this->_group . ' ' : '') . "\n" .
            $this->getHavingClause();

        return $whereClause;
    }

    /**
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return string
     */
    protected function getOrderByClause($orderBy, $orderDirection)
    {
        $this->_orderBy = $this->checkOrderBy($orderBy);
        $this->_orderWay = $this->checkOrderDirection($orderDirection);

        return ' ORDER BY '
            . ((str_replace('`', '', $this->_orderBy) == $this->identifier) ? 'a.' : '')
            . $this->_orderBy
            . ' '
            . $this->_orderWay
            . ($this->_tmpTableFilter ? ') tmpTable WHERE 1' . $this->_tmpTableFilter : '');
    }

    /**
     * @param string|null $orderBy
     *
     * @return false|string
     */
    protected function checkOrderBy($orderBy)
    {
        if (empty($orderBy)) {
            $prefix = $this->getCookieFilterPrefix();

            if ($this->context->cookie->{$prefix . $this->list_id . 'Orderby'}) {
                $orderBy = $this->context->cookie->{$prefix . $this->list_id . 'Orderby'};
            } elseif ($this->_orderBy) {
                $orderBy = $this->_orderBy;
            } else {
                $orderBy = $this->_defaultOrderBy;
            }
        }

        /* Check params validity */
        if (!Validate::isOrderBy($orderBy)) {
            throw new PrestaShopException('Invalid "order by" clause.');
        }

        if (!isset($this->fields_list[$orderBy]['order_key']) && isset($this->fields_list[$orderBy]['filter_key'])) {
            $this->fields_list[$orderBy]['order_key'] = $this->fields_list[$orderBy]['filter_key'];
        }

        if (isset($this->fields_list[$orderBy]['order_key'])) {
            $orderBy = $this->fields_list[$orderBy]['order_key'];
        }

        if (preg_match('/[.!]/', $orderBy)) {
            $orderBySplit = preg_split('/[.!]/', $orderBy);
            $orderBy = bqSQL($orderBySplit[0]) . '.`' . bqSQL($orderBySplit[1]) . '`';
        } elseif ($orderBy) {
            $orderBy = bqSQL($orderBy);
        }

        return $orderBy;
    }

    /**
     * @param string|null $orderDirection
     *
     * @return string
     */
    protected function checkOrderDirection($orderDirection)
    {
        $prefix = $this->getCookieOrderByPrefix();
        if (empty($orderDirection)) {
            if ($this->context->cookie->{$prefix . $this->list_id . 'Orderway'}) {
                $orderDirection = $this->context->cookie->{$prefix . $this->list_id . 'Orderway'};
            } elseif ($this->_orderWay) {
                $orderDirection = $this->_orderWay;
            } else {
                $orderDirection = $this->_defaultOrderWay;
            }
        }

        if (!Validate::isOrderWay($orderDirection)) {
            throw new PrestaShopException('Invalid order direction.');
        }

        return pSQL(Tools::strtoupper($orderDirection));
    }

    /**
     * @return mixed
     */
    protected function getCookieOrderByPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }

    /**
     * @return string
     */
    protected function getHavingClause()
    {
        $havingClause = '';
        if (isset($this->_filterHaving) || isset($this->_having)) {
            $havingClause = ' HAVING ';
            if (isset($this->_filterHaving)) {
                $havingClause .= ltrim($this->_filterHaving, ' AND ');
            }
            if (isset($this->_having)) {
                $havingClause .= $this->_having . ' ';
            }
        }

        return $havingClause;
    }

    /**
     * @param int $limit
     *
     * @return bool
     */
    protected function shouldLimitSqlResults($limit)
    {
        return $limit !== false;
    }

    /**
     * @param int $limit
     *
     * @return int
     */
    protected function checkSqlLimit($limit)
    {
        if (empty($limit)) {
            if (
                isset($this->context->cookie->{$this->list_id . '_pagination'}) &&
                $this->context->cookie->{$this->list_id . '_pagination'}
            ) {
                $limit = $this->context->cookie->{$this->list_id . '_pagination'};
            } else {
                $limit = $this->_default_pagination;
            }
        }

        $limit = (int) Tools::getValue($this->list_id . '_pagination', $limit);
        if (in_array($limit, $this->_pagination) && $limit != $this->_default_pagination) {
            $this->context->cookie->{$this->list_id . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$this->list_id . '_pagination'});
        }

        if (!is_numeric($limit)) {
            throw new PrestaShopException('Invalid limit. It should be a numeric.');
        }

        return $limit;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        $cookie = $this->context->cookie;
        $this->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        if ($this->allow_employee_form_lang && !$cookie->employee_form_lang) {
            $cookie->employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        $lang_exists = false;
        $this->_languages = Language::getLanguages(false);
        foreach ($this->_languages as $lang) {
            if (isset($cookie->employee_form_lang) && $cookie->employee_form_lang == $lang['id_lang']) {
                $lang_exists = true;
            }
        }

        $this->default_form_language = $lang_exists ? (int) $cookie->employee_form_lang : (int) Configuration::get('PS_LANG_DEFAULT');

        foreach ($this->_languages as $k => $language) {
            $this->_languages[$k]['is_default'] = (int) ($language['id_lang'] == $this->default_form_language);
        }

        return $this->_languages;
    }

    /**
     * Return the list of fields value.
     *
     * @param ObjectModel $obj Object
     *
     * @return array
     */
    public function getFieldsValue($obj)
    {
        foreach ($this->fields_form as $fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $input) {
                    if (!isset($this->fields_value[$input['name']])) {
                        if (isset($input['type']) && $input['type'] == 'shop') {
                            if ($obj->id) {
                                $result = Shop::getShopById((int) $obj->id, $this->identifier, $this->table);
                                foreach ($result as $row) {
                                    $this->fields_value['shop'][$row['id_' . $input['type']]][] = $row['id_shop'];
                                }
                            }
                        } elseif (isset($input['lang']) && $input['lang']) {
                            foreach ($this->_languages as $language) {
                                $field_value = $this->getFieldValue($obj, $input['name'], $language['id_lang']);
                                if (empty($field_value)) {
                                    if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']])) {
                                        $field_value = $input['default_value'][$language['id_lang']];
                                    } elseif (isset($input['default_value'])) {
                                        $field_value = $input['default_value'];
                                    }
                                }
                                $this->fields_value[$input['name']][$language['id_lang']] = $field_value ?: '';
                            }
                        } else {
                            $field_value = $this->getFieldValue($obj, $input['name']);
                            if ($field_value === false && isset($input['default_value'])) {
                                $field_value = $input['default_value'];
                            }
                            $this->fields_value[$input['name']] = $field_value;
                        }
                    }
                }
            }
        }

        return $this->fields_value;
    }

    /**
     * Return field value if possible (both classical and multilingual fields).
     *
     * Case 1 : Return value if present in $_POST / $_GET
     * Case 2 : Return object value
     *
     * @param ObjectModel $obj Object
     * @param string $key Field name
     * @param int|null $id_lang Language id (optional)
     *
     * @return false|mixed
     */
    public function getFieldValue($obj, $key, $id_lang = null)
    {
        if ($id_lang) {
            $default_value = (isset($obj->id) && $obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : false;
        } else {
            $default_value = isset($obj->{$key}) ? $obj->{$key} : false;
        }

        return Tools::getValue($key . ($id_lang ? '_' . $id_lang : ''), $default_value);
    }

    /**
     * Manage page display (form, list...).
     *
     * @param string|bool $class_name Allow to validate a different class than the current one
     *
     * @throws PrestaShopException
     */
    public function validateRules($class_name = false)
    {
        if (!$class_name) {
            $class_name = $this->className;
        }

        /** @var ObjectModel $object */
        $object = new $class_name();

        if (method_exists($this, 'getValidationRules')) {
            $definition = $this->getValidationRules();
        } else {
            $definition = ObjectModel::getDefinition($class_name);
        }

        $default_language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($definition['fields'] as $field => $def) {
            $skip = [];
            if (in_array($field, ['passwd', 'no-picture'])) {
                $skip = ['required'];
            }

            if (isset($def['lang']) && $def['lang']) {
                if (isset($def['required']) && $def['required']) {
                    $value = Tools::getValue($field . '_' . $default_language->id);
                    // !isset => not exist || "" == $value can be === 0 (before, empty $value === 0 returned true)
                    if (!isset($value) || '' == $value) {
                        $this->errors[$field . '_' . $default_language->id] = $this->trans(
                            'The field %field_name% is required at least in %lang%.',
                            ['%field_name%' => htmlspecialchars($object->displayFieldName($field, $class_name)), '%lang%' => htmlspecialchars($default_language->name)],
                            'Admin.Notifications.Error'
                        );
                    }
                }

                foreach ($languages as $language) {
                    $value = Tools::getValue($field . '_' . $language['id_lang']);
                    if (!empty($value)) {
                        if (($error = $object->validateField($field, $value, $language['id_lang'], $skip, true)) !== true) {
                            $this->errors[$field . '_' . $language['id_lang']] = $error;
                        }
                    }
                }
            } elseif (($error = $object->validateField($field, Tools::getValue($field), null, $skip, true)) !== true) {
                $this->errors[$field] = $error;
            }
        }

        /* Overload this method for custom checking */
        $this->_childValidation();
    }

    /**
     * Overload this method for custom checking.
     */
    protected function _childValidation()
    {
    }

    /**
     * Display object details.
     */
    public function viewDetails()
    {
    }

    /**
     * Called before deletion.
     *
     * @param ObjectModel $object Object
     *
     * @return bool
     */
    protected function beforeDelete($object)
    {
        return false;
    }

    /**
     * Called before deletion.
     *
     * @param ObjectModel $object Object
     * @param int $old_id
     *
     * @return bool
     */
    protected function afterDelete($object, $old_id)
    {
        return true;
    }

    /**
     * @param ObjectModel $object
     *
     * @return bool
     */
    protected function afterAdd($object)
    {
        return true;
    }

    /**
     * @param ObjectModel $object
     *
     * @return bool
     */
    protected function afterUpdate($object)
    {
        return true;
    }

    /**
     * Check rights to view the current tab.
     *
     * @return bool
     */
    protected function afterImageUpload()
    {
        return true;
    }

    /**
     * Copy data values from $_POST to object.
     *
     * @param ObjectModel $object Object
     * @param string $table Object table
     */
    protected function copyFromPost(&$object, $table)
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, get_object_vars($object)) && $key != 'id_' . $table) {
                /* Do not take care of password field if empty */
                if ($key == 'passwd' && Tools::getValue('id_' . $table) && empty($value)) {
                    continue;
                }
                /* Automatically hash password in MD5 */
                if ($key == 'passwd' && !empty($value)) {
                    $value = $this->get('hashing')->hash($value, _COOKIE_KEY_);
                }
                $object->{$key} = $value;
            }
        }

        /* Multilingual fields */
        $class_vars = get_class_vars(get_class($object));
        $fields = [];
        if (isset($class_vars['definition']['fields'])) {
            $fields = $class_vars['definition']['fields'];
        }

        foreach ($fields as $field => $params) {
            if (array_key_exists('lang', $params) && $params['lang']) {
                foreach (Language::getIDs(false) as $id_lang) {
                    if (Tools::isSubmit($field . '_' . (int) $id_lang)) {
                        $object->{$field}[(int) $id_lang] = Tools::getValue($field . '_' . (int) $id_lang);
                    }
                }
            }
        }
    }

    /**
     * Returns an array with selected shops and type (group or boutique shop).
     *
     * @param string $table
     *
     * @return array
     */
    protected function getSelectedAssoShop($table)
    {
        if (!Shop::isFeatureActive() || !Shop::isTableAssociated($table)) {
            return [];
        }

        $shops = Shop::getShops(true, null, true);
        if (count($shops) == 1 && isset($shops[0])) {
            return [$shops[0], 'shop'];
        }

        $assos = [];
        if (Tools::isSubmit('checkBoxShopAsso_' . $table)) {
            foreach (Tools::getValue('checkBoxShopAsso_' . $table) as $id_shop => $value) {
                $assos[] = (int) $id_shop;
            }
        } elseif (Shop::getTotalShops(false) == 1) {
            // if we do not have the checkBox multishop, we can have an admin with only one shop and being in multishop
            $assos[] = (int) Shop::getContextShopID();
        }

        return $assos;
    }

    /**
     * Update the associations of shops.
     *
     * @param int $id_object
     *
     * @return bool|void
     *
     * @throws PrestaShopDatabaseException
     */
    protected function updateAssoShop($id_object)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        if (!Shop::isTableAssociated($this->table)) {
            return;
        }

        $assos_data = $this->getSelectedAssoShop($this->table);

        // Get list of shop id we want to exclude from asso deletion
        $exclude_ids = $assos_data;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!$this->context->employee->hasAuthOnShop($row['id_shop'])) {
                $exclude_ids[] = $row['id_shop'];
            }
        }
        Db::getInstance()->delete($this->table . '_shop', '`' . bqSQL($this->identifier) . '` = ' . (int) $id_object . ($exclude_ids ? ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $exclude_ids)) . ')' : ''));

        $insert = [];
        foreach ($assos_data as $id_shop) {
            $insert[] = [
                $this->identifier => (int) $id_object,
                'id_shop' => (int) $id_shop,
            ];
        }

        return Db::getInstance()->insert($this->table . '_shop', $insert, false, true, Db::INSERT_IGNORE);
    }

    /**
     * @param mixed $value
     * @param array $field
     *
     * @return bool
     */
    protected function validateField($value, $field)
    {
        if (isset($field['validation'])) {
            $valid_method_exists = method_exists('Validate', $field['validation']);
            if ((!isset($field['empty']) || !$field['empty'] || $value) && $valid_method_exists) {
                $field_validation = $field['validation'];
                if (!Validate::$field_validation($value)) {
                    $this->errors[] = $this->trans('%s: Incorrect value', [$field['title']], 'Admin.Notifications.Error');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Can be overridden.
     */
    public function beforeUpdateOptions()
    {
    }

    /**
     * Overload this method for custom checking.
     *
     * @param int $id Object id used for deleting images
     *
     * @return bool
     */
    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name'], $this->fieldImageSettings['dir'])) {
            return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'] . '/');
        } elseif (!empty($this->fieldImageSettings)) {
            foreach ($this->fieldImageSettings as $image) {
                if (isset($image['name'], $image['dir'])) {
                    $this->uploadImage($id, $image['name'], $image['dir'] . '/');
                }
            }
        }

        return !count($this->errors) ? true : false;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $dir
     * @param string|bool $ext
     * @param int|null $width
     * @param int|null $height
     *
     * @return bool
     */
    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object->deleteImage();
            } else {
                return false;
            }

            // Check image validity
            $max_size = $this->max_image_size ?: 0;
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size))) {
                $this->errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
                return false;
            }

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $this->errors[] = $this->trans('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.', [], 'Admin.Notifications.Error');
            }

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_ . $dir . $id . '.' . $this->imageType, (int) $width, (int) $height, ($ext ? $ext : $this->imageType))) {
                $this->errors[] = $this->trans('An error occurred while uploading the image.', [], 'Admin.Notifications.Error');
            }

            if (count($this->errors)) {
                return false;
            }
            if ($this->afterImageUpload()) {
                unlink($tmp_name);

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Delete multiple items.
     *
     * @return bool true if success
     */
    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $object = new $this->className();

            if (isset($object->noZeroObject)) {
                $objects_count = count(call_user_func([$this->className, $object->noZeroObject]));

                // Check if all object will be deleted
                if ($objects_count <= 1 || count($this->boxes) == $objects_count) {
                    $this->errors[] = $this->trans('You need at least one object.', [], 'Admin.Notifications.Error') .
                        ' <b>' . $this->table . '</b><br />' .
                        $this->trans('You cannot delete all of the items.', [], 'Admin.Notifications.Error');
                }
            } else {
                $result = true;
                foreach ($this->boxes as $id) {
                    /** @var ObjectModel $to_delete */
                    $to_delete = new $this->className((int) $id);
                    $delete_ok = true;
                    if ($this->deleted) {
                        $to_delete->deleted = true;
                        if (!$to_delete->update()) {
                            $result = false;
                            $delete_ok = false;
                        }
                    } elseif (!$to_delete->delete()) {
                        $result = false;
                        $delete_ok = false;
                    }

                    if ($delete_ok) {
                        PrestaShopLogger::addLog(
                            $this->trans('%s deletion', [htmlspecialchars($this->className)]),
                            1,
                            null,
                            $this->className,
                            (int) $to_delete->id,
                            true,
                            (int) $this->context->employee->id
                        );
                    } else {
                        $this->errors[] = $this->trans('Can\'t delete #%id%', ['%id%' => (int) $id], 'Admin.Notifications.Error');
                    }
                }
                if ($result) {
                    $this->redirect_after = self::$currentIndex . '&conf=2&token=' . $this->token;
                }
                $this->errors[] = $this->trans('An error occurred while deleting this selection.', [], 'Admin.Notifications.Error');
            }
        } else {
            $this->errors[] = $this->trans('You must select at least one element to delete.', [], 'Admin.Notifications.Error');
        }

        if (isset($result)) {
            return $result;
        } else {
            return false;
        }
    }

    protected function ajaxProcessOpenHelp()
    {
        $help_class_name = $_GET['controller'];
        $popup_content = "<!doctype html>
        <html>
            <head>
                <meta charset='UTF-8'>
                <title>PrestaShop Help</title>
                <link href='//help.prestashop.com/css/help.css' rel='stylesheet'>
                <link href='//fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet'>
                <script src='" . _PS_JS_DIR_ . "jquery/jquery-1.11.0.min.js'></script>
                <script src='" . _PS_JS_DIR_ . "admin.js'></script>
                <script src='" . _PS_JS_DIR_ . "tools.js'></script>
                <script>
                    help_class_name='" . addslashes($help_class_name) . "';
                    iso_user = '" . addslashes($this->context->language->iso_code) . "'
                </script>
                <script src='themes/default/js/help.js'></script>
                <script>
                    $(function(){
                        initHelp();
                    });
                </script>
            </head>
            <body><div id='help-container' class='help-popup'></div></body>
        </html>";
        die($popup_content);
    }

    /**
     * Enable multiple items.
     *
     * @return bool true if success
     */
    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(true);
    }

    /**
     * Disable multiple items.
     *
     * @return bool true if success
     */
    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(false);
    }

    /**
     * Toggle status of multiple items.
     *
     * @param bool $status
     *
     * @return bool true if success
     *
     * @throws PrestaShopException
     */
    protected function processBulkStatusSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                /** @var ObjectModel $object */
                $object = new $this->className((int) $id);
                $object->active = (int) $status;
                $isUpdated = (bool) $object->update();
                $result &= $isUpdated;

                if (!$isUpdated) {
                    $this->errors[] = $this->trans('Can\'t update #%id% status', ['%id%' => (int) $id], 'Admin.Notifications.Error');
                }
            }

            if ($result) {
                $this->redirect_after = self::$currentIndex . '&conf=5&token=' . $this->token;
            } else {
                $this->errors[] = $this->trans('An error occurred while updating the status.', [], 'Admin.Notifications.Error');
            }
        } else {
            $this->errors[] = $this->trans('You must select at least one item to perform a bulk action.', [], 'Admin.Notifications.Error');
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function processBulkAffectZone()
    {
        $result = false;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            /** @var Country|State $object */
            $object = new $this->className();
            $result = $object->affectZoneToSelection(Tools::getValue($this->table . 'Box'), Tools::getValue('zone_to_affect'));

            if ($result) {
                $this->redirect_after = self::$currentIndex . '&conf=28&token=' . $this->token;
            }
            $this->errors[] = $this->trans('An error occurred while assigning a zone to the selection.', [], 'Admin.Notifications.Error');
        } else {
            $this->errors[] = $this->trans('You must select at least one element to assign a new zone.', [], 'Admin.Notifications.Error');
        }

        return $result;
    }

    /**
     * Called before Add.
     *
     * @param ObjectModel $object Object
     *
     * @return bool
     */
    protected function beforeAdd($object)
    {
        return true;
    }

    /**
     * Prepare the view to display the required fields form.
     *
     * @return string|void
     */
    public function displayRequiredFields()
    {
        if (!$this->access('add') || !$this->access('delete') || !$this->required_database) {
            return;
        }

        $helper = new Helper();
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->override_folder = $this->override_folder;

        return $helper->renderRequiredFields($this->className, $this->identifier, $this->required_fields);
    }

    /**
     * Create a template from the override file, else from the base file.
     *
     * @param string $tpl_name filename
     *
     * @return Smarty_Internal_Template
     */
    public function createTemplate($tpl_name)
    {
        // Use override tpl if it exists
        // If view access is denied, we want to use the default template that will be used to display an error
        if ($this->viewAccess() && $this->override_folder) {
            if (!Configuration::get('PS_DISABLE_OVERRIDES') && file_exists($this->context->smarty->getTemplateDir(1) . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name)) {
                return $this->context->smarty->createTemplate($this->override_folder . $tpl_name, $this->context->smarty);
            } elseif (file_exists($this->context->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name)) {
                return $this->context->smarty->createTemplate('controllers' . DIRECTORY_SEPARATOR . $this->override_folder . $tpl_name, $this->context->smarty);
            }
        }

        return $this->context->smarty->createTemplate($this->context->smarty->getTemplateDir(0) . $tpl_name, $this->context->smarty);
    }

    /**
     * Shortcut to set up a json success payload.
     *
     * @param string $message Success message
     */
    public function jsonConfirmation($message)
    {
        $this->json = true;
        $this->confirmations[] = $message;
        if ($this->status === '') {
            $this->status = 'ok';
        }
    }

    /**
     * Shortcut to set up a json error payload.
     *
     * @param string $message Error message
     */
    public function jsonError($message)
    {
        $this->json = true;
        $this->errors[] = $message;
        if ($this->status === '') {
            $this->status = 'error';
        }
    }

    /**
     * Add an entry to the meta title.
     *
     * @param string $entry new entry
     */
    public function addMetaTitle($entry)
    {
        // Only add entry if the meta title was not forced.
        if (is_array($this->meta_title)) {
            $this->meta_title[] = $entry;
        }
    }

    /**
     * Set action.
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Set IdObject.
     *
     * @param int $id_object
     */
    public function setIdObject($id_object)
    {
        $this->id_object = (int) $id_object;
    }

    /**
     * @return string
     */
    public function getTabSlug()
    {
        if (empty($this->tabSlug)) {
            $this->tabSlug = Access::findSlugByIdTab($this->id);
        }

        return $this->tabSlug;
    }

    /**
     * @return ContainerInterface
     */
    protected function buildContainer(): ContainerInterface
    {
        return SymfonyContainer::getInstance();
    }

    /**
     * Return the type of authorization on module page.
     *
     * @return int
     */
    public function authorizationLevel()
    {
        if (
            Access::isGranted(
                Permission::PREFIX_TAB . strtoupper($this->controller_name) . '_DELETE',
                $this->context->employee->id_profile
            )
        ) {
            return Permission::LEVEL_DELETE;
        } elseif (
            Access::isGranted(
                Permission::PREFIX_TAB . strtoupper($this->controller_name) . '_CREATE',
                $this->context->employee->id_profile
            )
        ) {
            return Permission::LEVEL_CREATE;
        } elseif (
            Access::isGranted(
                Permission::PREFIX_TAB . strtoupper($this->controller_name) . '_UPDATE',
                $this->context->employee->id_profile
            )
        ) {
            return Permission::LEVEL_UPDATE;
        } elseif (
            Access::isGranted(
                Permission::PREFIX_TAB . strtoupper($this->controller_name) . '_READ',
                $this->context->employee->id_profile
            )
        ) {
            return Permission::LEVEL_READ;
        } else {
            return 0;
        }
    }

    /**
     * Get the url of the first active sub-tab.
     *
     * @param array[] $subtabs
     *
     * @return string Url, or empty if no active sub-tab
     */
    private function getTabLinkFromSubTabs(array $subtabs)
    {
        foreach ($subtabs as $tab) {
            if ($tab['active'] && $tab['enabled']) {
                return $tab['href'];
            }
        }

        return '';
    }

    /**
     * Prepare price specifications to display cldr prices in javascript context.
     *
     * @param Context $context
     *
     * @return array
     */
    private function preparePriceSpecifications(Context $context)
    {
        /* @var Currency */
        $currency = $context->currency;
        /* @var PriceSpecification */
        $priceSpecification = $context->getCurrentLocale()->getPriceSpecification($currency->iso_code);

        return array_merge(
            ['symbol' => $priceSpecification->getSymbolsByNumberingSystem(Locale::NUMBERING_SYSTEM_LATIN)->toArray()],
            $priceSpecification->toArray()
        );
    }

    /**
     * Prepare number specifications to display cldr numbers in javascript context.
     *
     * @param Context $context
     *
     * @return array
     */
    private function prepareNumberSpecifications(Context $context)
    {
        /* @var NumberSpecification */
        $numberSpecification = $context->getCurrentLocale()->getNumberSpecification();

        return array_merge(
            ['symbol' => $numberSpecification->getSymbolsByNumberingSystem(Locale::NUMBERING_SYSTEM_LATIN)->toArray()],
            $numberSpecification->toArray()
        );
    }

    /**
     * Set if anonymous is allowed to run this controller
     *
     * @param bool $value
     */
    protected function setAllowAnonymous($value)
    {
        $this->allowAnonymous = (bool) $value;
    }

    /**
     * Return if an anonymous is allowed to run this controller
     *
     * @return bool
     */
    protected function isAnonymousAllowed()
    {
        return $this->allowAnonymous;
    }

    /**
     * Get the buttons provided by hooks and merge them into the already defined page toolbar buttons
     *
     * @throws TypeException
     */
    protected function mergeExtraToolbarButtons(): void
    {
        $toolbarButtonsCollection = new ActionsBarButtonsCollection();

        // Get previously assigned toolbar buttons of the controller
        $controllerButtons = $this->page_header_toolbar_btn;
        if (!empty($controllerButtons)) {
            // Build ActionsBarButton based on array setting from the controller and add it to collection
            foreach ($controllerButtons as $controllerButtonIndex => $controllerButton) {
                $toolbarButtonsCollection->add(
                    new ActionsBarButton(
                        $controllerButton['class'] ?? $controllerButtonIndex,
                        $controllerButton,
                        $controllerButton['desc'] ?? ''
                    )
                );
            }
        }

        // Get the toolbar buttons defined by hooks and add them to the main collection
        try {
            Hook::exec('actionGetAdminToolbarButtons', [
                'controller' => $this,
                'toolbar_extra_buttons_collection' => &$toolbarButtonsCollection,
            ]);
        } catch (Exception $exception) {
            $this->get('logger')->addWarning(
                'There was an error retrieving toolbar buttons from Hooks. Toolbar buttons are probably not complete',
                [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]
            );

            return; // In case of any error, the buttons from hooks are simply ignored
        }

        // Transform the collection of buttons into array and reassign it to the page header toolbar buttons
        $toolbarButtons = [];

        foreach ($toolbarButtonsCollection as $toolbarActionButton) {
            $toolbarActionButtonProperties = $this->transformActionBarButtonToToolbarButton($toolbarActionButton);
            $toolbarButtons[$toolbarActionButtonProperties['name']] = $toolbarActionButtonProperties;
        }

        $this->page_header_toolbar_btn = $toolbarButtons;
    }

    /**
     * @param ActionsBarButtonInterface $actionBarButton
     *
     * @return array
     */
    private function transformActionBarButtonToToolbarButton(ActionsBarButtonInterface $actionBarButton): array
    {
        $buttonProperties = $actionBarButton->getProperties();

        return array_merge(
            $buttonProperties,
            [
                'name' => str_replace(' ', '-', $buttonProperties['class'] ?? $actionBarButton->getClass()),
                'desc' => $buttonProperties['desc'] ?? $actionBarButton->getContent(),
                'class' => ($buttonProperties['class'] ?? $actionBarButton->getClass()) . ' btn-primary',
                'imgclass' => $buttonProperties['class'] ?? $actionBarButton->getClass(),
            ]
        );
    }
}
