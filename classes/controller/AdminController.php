<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Cldr;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;

class AdminControllerCore extends Controller
{
    /** @var string */
    public $path;

    /** @var string */
    public static $currentIndex;

    /** @var string */
    public $content;

    /** @var array */
    public $warnings = array();

    /** @var array */
    public $informations = array();

    /** @var array */
    public $confirmations = array();

    /** @var string|false */
    public $shopShareDatas = false;

    /** @var array */
    public $_languages = array();

    /** @var int */
    public $default_form_language;

    /** @var bool */
    public $allow_employee_form_lang;

    /** @var string */
    public $layout = 'layout.tpl';

    /** @var bool */
    public $bootstrap = false;

    /** @var string|array */
    protected $meta_title = array();

    /** @var string */
    public $template = 'content.tpl';

    /** @var string Associated table name */
    public $table = 'configuration';

    /** @var string */
    public $list_id;

    /** @var string|false Object identifier inside the associated table */
    protected $identifier = false;

    /** @var string */
    protected $identifier_name = 'name';

    /** @var string Associated object class name */
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

    /** @var string Default ORDER BY clause when $_orderBy is not defined */
    protected $_defaultOrderBy = false;

    /** @var string */
    protected $_defaultOrderWay = 'ASC';

    /** @var array */
    public $tpl_form_vars = array();

    /** @var array */
    public $tpl_list_vars = array();

    /** @var array */
    public $tpl_delete_link_vars = array();

    /** @var array */
    public $tpl_option_vars = array();

    /** @var array */
    public $tpl_view_vars = array();

    /** @var array */
    public $tpl_required_fields_vars = array();

    /** @var string|null */
    public $base_tpl_view = null;

    /** @var string|null */
    public $base_tpl_form = null;

    /** @var bool If you want more fieldsets in the form */
    public $multiple_fieldsets = false;

    /** @var array|false */
    public $fields_value = false;

    /** @var array Errors displayed after post processing */
    public $errors = array();

    /** @var bool Define if the header of the list contains filter and sorting links or not */
    protected $list_simple_header;

    /** @var array List to be generated */
    protected $fields_list;

    /** @var array Modules list filters */
    protected $filter_modules_list = null;

    /** @var array Modules list filters */
    protected $modules_list = array();

    /** @var array Edit form to be generated */
    protected $fields_form;

    /** @var array Override of $fields_form */
    protected $fields_form_override;

    /** @var string Override form action */
    protected $submit_action;

    /** @var array List of option forms to be generated */
    protected $fields_options = array();

    /** @var string */
    protected $shopLink;

    /** @var string SQL query */
    protected $_listsql = '';

    /** @var array Cache for query results */
    protected $_list = array();

    /** @var string MySQL error */
    protected $_list_error;

    /** @var string|array Toolbar title */
    protected $toolbar_title;

    /** @var array List of toolbar buttons */
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

    /** @var array WHERE clause determined by filter fields */
    protected $_filter;

    /** @var string */
    protected $_filterHaving;

    /** @var array Temporary SQL table WHERE clause determined by filter fields */
    protected $_tmpTableFilter = '';

    /** @var array Number of results in list per page (used in select field) */
    protected $_pagination = array(20, 50, 100, 300, 1000);

    /** @var int Default number of results in list per page */
    protected $_default_pagination = 50;

    /** @var string ORDER BY clause determined by field/arrows in list header */
    protected $_orderBy;

    /** @var string Order way (ASC, DESC) determined by arrows in list header */
    protected $_orderWay;

    /** @var array List of available actions for each list row - default actions are view, edit, delete, duplicate */
    protected $actions_available = array('view', 'edit', 'duplicate', 'delete');

    /** @var array List of required actions for each list row */
    protected $actions = array();

    /** @var array List of row ids associated with a given action for witch this action have to not be available */
    protected $list_skip_actions = array();

    /* @var bool Don't show header & footer */
    protected $lite_display = false;

    /** @var bool List content lines are clickable if true */
    protected $list_no_link = false;

    /** @var bool */
    protected $allow_export = false;

    /** @var array Cache for translations */
    public static $cache_lang = array();

    /** @var array Required_fields to display in the Required Fields form */
    public $required_fields = array();

    /** @var HelperList */
    protected $helper;

    /** @var int DELETE access level */
    const LEVEL_DELETE = 4;

    /** @var int ADD access level */
    const LEVEL_ADD = 3;

    /** @var int EDIT access level */
    const LEVEL_EDIT = 2;

    /** @var int VIEW access level */
    const LEVEL_VIEW = 1;

    /**
     * Actions to execute on multiple selections.
     *
     * Usage:
     *
     * array(
     *      'actionName' => array(
     *      'text' => $this->l('Message displayed on the submit button (mandatory)'),
     *      'confirm' => $this->l('If set, this confirmation message will pop-up (optional)')),
     *      'anotherAction' => array(...)
     * );
     *
     * If your action is named 'actionName', you need to have a method named bulkactionName() that will be executed when the button is clicked.
     *
     * @var array
     */
    protected $bulk_actions;

    /* @var array Ids of the rows selected */
    protected $boxes;

    /** @var string Do not automatically select * anymore but select only what is necessary */
    protected $explicitSelect = false;

    /** @var string Add fields into data query to display list */
    protected $_select;

    /** @var string Join tables into data query to display list */
    protected $_join;

    /** @var string Add conditions into data query to display list */
    protected $_where;

    /** @var string Group rows into data query to display list */
    protected $_group;

    /** @var string Having rows into data query to display list */
    protected $_having;

    /** @var string Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records */
    protected $_use_found_rows = true;

    /** @var bool */
    protected $is_cms = false;

    /** @var string Identifier to use for changing positions in lists (can be omitted if positions cannot be changed) */
    protected $position_identifier;

    /** @var string|int */
    protected $position_group_identifier;

    /** @var bool Table records are not deleted but marked as deleted if set to true */
    protected $deleted = false;

    /**  @var bool Is a list filter set */
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

    /** @var string */
    protected $display;

    /** @var array */
    protected $tab_modules_list = array('default_list' => array(), 'slider_list' => array());

    /** @var string */
    public $tpl_folder;

    /** @var string */
    protected $bo_theme;

    /** @var bool Redirect or not after a creation */
    protected $_redirect = true;

    /** @var array Name and directory where class image are located */
    public $fieldImageSettings = array();

    /** @var string Image type */
    public $imageType = 'jpg';

    /** @var ObjectModel Instantiation of the class associated with the AdminController */
    protected $object;

    /** @var int Current object ID */
    protected $id_object;

    /** @var string Current controller name without suffix */
    public $controller_name;

    /** @var int */
    public $multishop_context = -1;

    /** @var false */
    public $multishop_context_group = true;

    /** @var array Current breadcrumb position as an array of tab names */
    protected $breadcrumbs;

    /** @var bool Bootstrap variable */
    public $show_page_header_toolbar = false;

    /** @var string Bootstrap variable */
    public $page_header_toolbar_title;

    /** @var array|Traversable Bootstrap variable */
    public $page_header_toolbar_btn = array();

    /** @var bool Bootstrap variable */
    public $show_form_cancel_button;

    /** @var string */
    public $admin_webpath;

    /** @var array */
    protected $list_natives_modules = array();

    /** @var array */
    protected $list_partners_modules = array();

    /** @var array */
    public $modals = array();

    /** @var bool */
    protected $logged_on_addons = false;

    /** @var bool if logged employee has access to AdminImport */
    protected $can_import = false;

    /** @var string */
    protected $tabSlug;

    public function __construct($forceControllerName = '', $default_theme_name = 'default')
    {
        global $timer_start;
        $this->timer_start = $timer_start;

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

        if (defined('_PS_BO_DEFAULT_THEME_') && _PS_BO_DEFAULT_THEME_
            && @filemtime(_PS_BO_ALL_THEMES_DIR_._PS_BO_DEFAULT_THEME_.DIRECTORY_SEPARATOR.'template')) {
            $default_theme_name = _PS_BO_DEFAULT_THEME_;
        }

        $this->bo_theme = $default_theme_name;

        if (!@filemtime(_PS_BO_ALL_THEMES_DIR_.$this->bo_theme.DIRECTORY_SEPARATOR.'template')) {
            $this->bo_theme = 'default';
        }

        $this->bo_css = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_css) ? $this->context->employee->bo_css : 'admin-theme.css');

        $adminThemeCSSFile = _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.$this->bo_css;

        if (file_exists($adminThemeCSSFile)) {
            $this->bo_css = 'admin-theme.css';
        }

        if (defined('_PS_BO_ALL_THEMES_DIR_')) {
            $this->context->smarty->setTemplateDir(array(
                _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.DIRECTORY_SEPARATOR.'template',
                _PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'
            ));
        }

        $this->id = Tab::getIdFromClassName($this->controller_name);
        $this->token = Tools::getAdminToken($this->controller_name.(int)$this->id.(int)$this->context->employee->id);

        $this->_conf = array(
            1 => $this->trans('Successful deletion.', array(), 'Admin.Notifications.Success'),
            2 => $this->trans('The selection has been successfully deleted.', array(), 'Admin.Notifications.Success'),
            3 => $this->trans('Successful creation.', array(), 'Admin.Notifications.Success'),
            4 => $this->trans('Successful update.', array(), 'Admin.Notifications.Success'),
            5 => $this->trans('The status has been successfully updated.', array(), 'Admin.Notifications.Success'),
            6 => $this->trans('The settings have been successfully updated.', array(), 'Admin.Notifications.Success'),
            7 => $this->trans('The image was successfully deleted.', array(), 'Admin.Notifications.Success'),
            8 => $this->trans('The module was successfully downloaded.', array(), 'Admin.Modules.Notification'),
            9 => $this->trans('The thumbnails were successfully regenerated.', array(), 'Admin.Notifications.Success'),
            10 => $this->trans('The message was successfully sent to the customer.', array(), 'Admin.Orderscustomers.Notification'),
            11 => $this->trans('Comment successfully added.', array(), 'Admin.Notifications.Success'),
            12 => $this->trans('Module(s) installed successfully.', array(), 'Admin.Modules.Notification'),
            13 => $this->trans('Module(s) uninstalled successfully.', array(), 'Admin.Modules.Notification'),
            14 => $this->trans('The translation was successfully copied.', array(), 'Admin.International.Notification'),
            15 => $this->trans('The translations have been successfully added.', array(), 'Admin.International.Notification'),
            16 => $this->trans('The module transplanted successfully to the hook.', array(), 'Admin.Modules.Notification'),
            17 => $this->trans('The module was successfully removed from the hook.', array(), 'Admin.Modules.Notification'),
            18 => $this->trans('Successful upload.', array(), 'Admin.Notifications.Success'),
            19 => $this->trans('Duplication was completed successfully.', array(), 'Admin.Notifications.Success'),
            20 => $this->trans('The translation was added successfully, but the language has not been created.', array(), 'Admin.International.Notification'),
            21 => $this->trans('Module reset successfully.', array(), 'Admin.Modules.Notification'),
            22 => $this->trans('Module deleted successfully.', array(), 'Admin.Modules.Notification'),
            23 => $this->trans('Localization pack imported successfully.', array(), 'Admin.International.Notification'),
            24 => $this->trans('Localization pack imported successfully.', array(), 'Admin.International.Notification'),
            25 => $this->trans('The selected images have successfully been moved.', array(), 'Admin.Notifications.Success'),
            26 => $this->trans('Your cover image selection has been saved.', array(), 'Admin.Notifications.Success'),
            27 => $this->trans('The image\'s shop association has been modified.', array(), 'Admin.Notifications.Success'),
            28 => $this->trans('A zone has been assigned to the selection successfully.', array(), 'Admin.Notifications.Success'),
            29 => $this->trans('Successful upgrade.', array(), 'Admin.Notifications.Success'),
            30 => $this->trans('A partial refund was successfully created.', array(), 'Admin.Orderscustomers.Notification'),
            31 => $this->trans('The discount was successfully generated.', array(), 'Admin.Catalog.Notification'),
            32 => $this->trans('Successfully signed in to PrestaShop Addons.', array(), 'Admin.Modules.Notification'),
        );

        $this->_error = array(
            1 => $this->trans(
                'The root category of the shop %shop% is not associated with the current shop. You can\'t access this page. Please change the root category of the shop.',
                array(
                    '%shop%' => $this->context->shop->name,
                ),
                'Admin.Catalog.Notification'
            ),
        );

        if (!$this->identifier) {
            $this->identifier = 'id_'.$this->table;
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

        //$this->base_template_folder = _PS_BO_ALL_THEMES_DIR_.$this->bo_theme.'/template';
        $this->override_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)).'/';
        // Get the name of the folder containing the custom tpl files
        $this->tpl_folder = Tools::toUnderscoreCase(substr($this->controller_name, 5)).'/';

        $this->initShopContext();

        if (defined('_PS_ADMIN_DIR_')) {
            $this->admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
            $this->admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $this->admin_webpath);
        }


        // Check if logged on Addons
        $this->logged_on_addons = false;
        if (isset($this->context->cookie->username_addons) && isset($this->context->cookie->password_addons) && !empty($this->context->cookie->username_addons) && !empty($this->context->cookie->password_addons)) {
            $this->logged_on_addons = true;
        }

        // Set context mode
        if (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_) {
            if (isset($this->context->cookie->is_contributor) && (int)$this->context->cookie->is_contributor === 1) {
                $this->context->mode = Context::MODE_HOST_CONTRIB;
            } else {
                $this->context->mode = Context::MODE_HOST;
            }
        } elseif (isset($this->context->cookie->is_contributor) && (int)$this->context->cookie->is_contributor === 1) {
            $this->context->mode = Context::MODE_STD_CONTRIB;
        } else {
            $this->context->mode = Context::MODE_STD;
        }

        //* Check if logged employee has access to AdminImport controller */
        $import_access = Profile::getProfileAccess($this->context->employee->id_profile, Tab::getIdFromClassName('AdminImport'));
        if (is_array($import_access) && isset($import_access['view']) && $import_access['view'] == 1) {
            $this->can_import = true;
        }

        $this->context->smarty->assign(array(
            'context_mode' => $this->context->mode,
            'logged_on_addons' => $this->logged_on_addons,
            'can_import' => $this->can_import,
        ));
    }

    /**
     * Set breadcrumbs array for the controller page
     *
     * @param int|null $tab_id
     * @param array|null $tabs
     */
    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        if (is_array($tabs) || count($tabs)) {
            $tabs = array();
        }

        if (is_null($tab_id)) {
            $tab_id = $this->id;
        }

        $tabs = Tab::recursiveTab($tab_id, $tabs);

        $dummy = array('name' => '', 'href' => '', 'icon' => '');
        $breadcrumbs2 = array(
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy
        );
        if (isset($tabs[0])) {
            $this->addMetaTitle($tabs[0]['name']);
            $breadcrumbs2['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs2['tab']['href'] = $this->context->link->getAdminLink($tabs[0]['class_name']);
            if (!isset($tabs[1])) {
                $breadcrumbs2['tab']['icon'] = 'icon-'.$tabs[0]['class_name'];
            }
        }
        if (isset($tabs[1])) {
            $breadcrumbs2['container']['name'] = $tabs[1]['name'];
            $breadcrumbs2['container']['href'] = $this->context->link->getAdminLink($tabs[1]['class_name']);
            $breadcrumbs2['container']['icon'] = 'icon-'.$tabs[1]['class_name'];
        }

        /* content, edit, list, add, details, options, view */
        switch ($this->display) {
            case 'add':
                $breadcrumbs2['action']['name'] = $this->l('Add', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-plus';
                break;
            case 'edit':
                $breadcrumbs2['action']['name'] = $this->l('Edit', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-pencil';
                break;
            case '':
            case 'list':
                $breadcrumbs2['action']['name'] = $this->l('List', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-th-list';
                break;
            case 'details':
            case 'view':
                $breadcrumbs2['action']['name'] = $this->l('View details', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-zoom-in';
                break;
            case 'options':
                $breadcrumbs2['action']['name'] = $this->l('Options', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-cogs';
                break;
            case 'generator':
                $breadcrumbs2['action']['name'] = $this->l('Generator', null, null, false);
                $breadcrumbs2['action']['icon'] = 'icon-flask';
                break;
        }

        $this->context->smarty->assign(array(
            'breadcrumbs2' => $breadcrumbs2,
            'quick_access_current_link_name' => Tools::safeOutput($breadcrumbs2['tab']['name'].(isset($breadcrumbs2['action']) ? ' - '.$breadcrumbs2['action']['name'] : '')),
            'quick_access_current_link_icon' => $breadcrumbs2['container']['icon']
        ));

        /* BEGIN - Backward compatibility < 1.6.0.3 */
        $this->breadcrumbs[] = $tabs[0]['name'];
        $navigation_pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->context->smarty->assign('navigationPipe', $navigation_pipe);
        /* END - Backward compatibility < 1.6.0.3 */
    }

    /**
     * Set default toolbar_title to admin breadcrumb
     *
     * @return void
     */
    public function initToolbarTitle()
    {
        $this->toolbar_title = is_array($this->breadcrumbs) ? array_unique($this->breadcrumbs) : array($this->breadcrumbs);

        switch ($this->display) {
            case 'edit':
                $this->toolbar_title[] = $this->l('Edit', null, null, false);
                $this->addMetaTitle($this->l('Edit', null, null, false));
                break;

            case 'add':
                $this->toolbar_title[] = $this->l('Add new', null, null, false);
                $this->addMetaTitle($this->l('Add new', null, null, false));
                break;

            case 'view':
                $this->toolbar_title[] = $this->l('View', null, null, false);
                $this->addMetaTitle($this->l('View', null, null, false));
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
            $filters = array();

            foreach ($this->fields_list as $field => $t) {
                if (isset($t['filter_key'])) {
                    $field = $t['filter_key'];
                }

                if (($val = Tools::getValue($this->table.'Filter_'.$field)) || $val = $this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_'.$field}) {
                    if (!is_array($val)) {
                        $filter_value = '';
                        if (isset($t['type']) && $t['type'] == 'bool') {
                            $filter_value = ((bool)$val) ? $this->l('yes') : $this->l('no');
                        } elseif (isset($t['type']) && $t['type'] == 'date' || isset($t['type']) && $t['type'] == 'datetime') {
                            $date = json_decode($val, true);
                            if (isset($date[0])) {
                                $filter_value = $date[0];
                                if (isset($date[1]) && !empty($date[1])) {
                                    $filter_value .= ' - '.$date[1];
                                }
                            }
                        } elseif (is_string($val)) {
                            $filter_value = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        }
                        if (!empty($filter_value)) {
                            $filters[] = sprintf($this->l('%s: %s'), $t['title'], $filter_value);
                        }
                    } else {
                        $filter_value = '';
                        foreach ($val as $v) {
                            if (is_string($v) && !empty($v)) {
                                $filter_value .= ' - '.htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                            }
                        }
                        $filter_value = ltrim($filter_value, ' -');
                        if (!empty($filter_value)) {
                            $filters[] = sprintf($this->l('%s: %s'), $t['title'], $filter_value);
                        }
                    }
                }
            }

            if (count($filters)) {
                return sprintf($this->l('filter by %s'), implode(', ', $filters));
            }
        }
    }

    /**
     *
     * @param string $action
     * @param bool $disable
     */
    public function access($action, $disable = false)
    {
        if (empty($this->tabAccess[$action])) {
            $slugs = array();

            foreach ((array) Access::getAuthorizationFromLegacy($action) as $roleSuffix) {
                $slugs[] = $this->getTabSlug().$roleSuffix;
            }

            $this->tabAccess[$action] = Access::isGranted(
                $slugs,
                $this->context->employee->id_profile
            );
        }

        return $this->tabAccess[$action];
    }

    /**
     * Check rights to view the current tab
     *
     * @param bool $disable
     * @return bool
     */
    public function viewAccess($disable = false)
    {
        return $this->access('view', $disable);
    }

    /**
     * Check for security token
     *
     * @return bool
     */
    public function checkToken()
    {
        if (TokenInUrls::isDisabled()) {
            return true;
        }

        $token = Tools::getValue('token');
        if (!empty($token) && $token === $this->token) {
            return true;
        }

        if (count($_POST) || !isset($_GET['controller']) || !Validate::isControllerName($_GET['controller']) || $token) {
            return false;
        }

        foreach ($_GET as $key => $value) {
            if (is_array($value) || !in_array($key, array('controller', 'controllerUri'))) {
                return false;
            }
        }

        $cookie = Context::getContext()->cookie;
        $whitelist = array('date_add', 'id_lang', 'id_employee', 'email', 'profile', 'passwd', 'remote_addr', 'shopContext', 'collapse_menu', 'checksum');
        foreach ($cookie->getAll() as $key => $value) {
            if (!in_array($key, $whitelist)) {
                unset($cookie->$key);
            }
        }

        $cookie->write();

        return true;
    }

    /**
     * Set the filters used for the list display
     */
    protected function getCookieFilterPrefix()
    {
        return str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));
    }

    public function processFilter()
    {
        Hook::exec('action'.$this->controller_name.'ListingFieldsModifier', array(
            'fields' => &$this->fields_list,
        ));

        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix.$key});
                } elseif (stripos($key, $this->list_id.'Filter_') === 0) {
                    $this->context->cookie->{$prefix.$key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id.'Filter_') === 0) {
                    $this->context->cookie->{$prefix.$key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $this->list_id.'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->context->cookie->{$prefix.$key});
                    } else {
                        $this->context->cookie->{$prefix.$key} = $value;
                    }
                } elseif (stripos($key, $this->list_id.'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->context->cookie->{$prefix.$key});
                    } else {
                        $this->context->cookie->{$prefix.$key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix.$this->list_id.'Filter_');
        $definition = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix.$this->list_id.'Filter_', 7 + Tools::strlen($prefix.$this->list_id))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix.$this->list_id));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = json_decode($value, true);
                    }
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0].'.`'.$tmp_tab[1].'`' : '`'.$tmp_tab[0].'`';

                    // Assignment by reference
                    if (array_key_exists('tmpTableFilter', $field)) {
                        $sql_filter = & $this->_tmpTableFilter;
                    } elseif (array_key_exists('havingFilter', $field)) {
                        $sql_filter = & $this->_filterHaving;
                    } else {
                        $sql_filter = & $this->_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = $this->trans('The \'From\' date format is invalid (YYYY-MM-DD)', array(), 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND '.pSQL($key).' >= \''.pSQL(Tools::dateFrom($value[0])).'\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = $this->trans('The \'To\' date format is invalid (YYYY-MM-DD)', array(), 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND '.pSQL($key).' <= \''.pSQL(Tools::dateTo($value[1])).'\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $this->identifier || $key == '`'.$this->identifier.'`');
                        $alias = ($definition && !empty($definition['fields'][$filter]['shop'])) ? 'sa' : 'a';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ?  $alias.'.' : '').pSQL($key).' = '.(int)$value.' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ?  $alias.'.' : '').pSQL($key).' = '.(float)$value.' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ?  $alias.'.' : '').pSQL($key).' = \''.pSQL($value).'\' ';
                        } elseif ($type == 'price') {
                            $value = (float)str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ?  $alias.'.' : '').pSQL($key).' = '.pSQL(trim($value)).' ';
                        } else {
                            $sql_filter .= ($check_key ?  $alias.'.' : '').pSQL($key).' LIKE \'%'.pSQL(trim($value)).'%\' ';
                        }
                    }
                }
            }
        }
    }

    /**
     * @TODO uses redirectAdmin only if !$this->ajax
     * @return ObjectModel|bool
     */
    public function postProcess()
    {
        try {
            if ($this->ajax) {
                // from ajax-tab.php
                $action = Tools::getValue('action');
                // no need to use displayConf() here
                if (!empty($action) && method_exists($this, 'ajaxProcess'.Tools::toCamelCase($action))) {
                    Hook::exec('actionAdmin'.ucfirst($this->action).'Before', array('controller' => $this));
                    Hook::exec('action'.get_class($this).ucfirst($this->action).'Before', array('controller' => $this));

                    $return = $this->{'ajaxProcess'.Tools::toCamelCase($action)}();

                    Hook::exec('actionAdmin'.ucfirst($this->action).'After', array('controller' => $this, 'return' => $return));
                    Hook::exec('action'.get_class($this).ucfirst($this->action).'After', array('controller' => $this, 'return' => $return));

                    return $return;
                } elseif (!empty($action) && $this->controller_name == 'AdminModules' && Tools::getIsset('configure')) {
                    $module_obj = Module::getInstanceByName(Tools::getValue('configure'));
                    if (Validate::isLoadedObject($module_obj) && method_exists($module_obj, 'ajaxProcess'.$action)) {
                        return $module_obj->{'ajaxProcess'.$action}();
                    }
                } elseif (method_exists($this, 'ajaxProcess')) {
                    return $this->ajaxProcess();
                }
            } else {
                // Process list filtering
                if ($this->filter && $this->action != 'reset_filters') {
                    $this->processFilter();
                }

                if (isset($_POST) && count($_POST) && (int)Tools::getValue('submitFilter'.$this->list_id) || Tools::isSubmit('submitReset'.$this->list_id)) {
                    $this->setRedirectAfter(self::$currentIndex.'&token='.$this->token.(Tools::isSubmit('submitFilter'.$this->list_id) ? '&submitFilter'.$this->list_id.'='.(int)Tools::getValue('submitFilter'.$this->list_id) : ''));
                }

                // If the method named after the action exists, call "before" hooks, then call action method, then call "after" hooks
                if (!empty($this->action) && method_exists($this, 'process'.ucfirst(Tools::toCamelCase($this->action)))) {
                    // Hook before action
                    Hook::exec('actionAdmin'.ucfirst($this->action).'Before', array('controller' => $this));
                    Hook::exec('action'.get_class($this).ucfirst($this->action).'Before', array('controller' => $this));
                    // Call process
                    $return = $this->{'process'.Tools::toCamelCase($this->action)}();
                    // Hook After Action
                    Hook::exec('actionAdmin'.ucfirst($this->action).'After', array('controller' => $this, 'return' => $return));
                    Hook::exec('action'.get_class($this).ucfirst($this->action).'After', array('controller' => $this, 'return' => $return));
                    return $return;
                }
            }
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
        }
        return false;
    }

    /**
     * Object Delete images
     *
     * @return ObjectModel|false
     */
    public function processDeleteImage()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if (($object->deleteImage())) {
                $redirect = self::$currentIndex.'&update'.$this->table.'&'.$this->identifier.'='.Tools::getValue($this->identifier).'&conf=7&token='.$this->token;
                if (!$this->ajax) {
                    $this->redirect_after = $redirect;
                } else {
                    $this->content = 'ok';
                }
            }
        }
        $this->errors[] = $this->trans('An error occurred while attempting to delete the image. (cannot load object).', array(), 'Admin.Notifications.Error');
        return $object;
    }

    /**
     * @param string $text_delimiter
     * @throws PrestaShopException
     */
    public function processExport($text_delimiter = '"')
    {
        // clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        $this->getList($this->context->language->id, null, null, 0, false);
        if (!count($this->_list)) {
            return;
        }

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="'.$this->table.'_'.date('Y-m-d_His').'.csv"');

        $fd = fopen('php://output', 'wb');
        $headers = array();
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
            $content = array();
            $path_to_image = false;
            foreach ($this->fields_list as $key => $params) {
                $field_value = isset($row[$key]) ? Tools::htmlentitiesDecodeUTF8(Tools::nl2br($row[$key])) : '';
                if ($key == 'image') {
                    if ($params['image'] != 'p' || Configuration::get('PS_LEGACY_IMAGES')) {
                        $path_to_image = Tools::getShopDomain(true)._PS_IMG_.$params['image'].'/'.$row['id_'.$this->table].(isset($row['id_image']) ? '-'.(int)$row['id_image'] : '').'.'.$this->imageType;
                    } else {
                        $path_to_image = Tools::getShopDomain(true)._PS_IMG_.$params['image'].'/'.Image::getImgFolderStatic($row['id_image']).(int)$row['id_image'].'.'.$this->imageType;
                    }
                    if ($path_to_image) {
                        $field_value = $path_to_image;
                    }
                }
                if (isset($params['callback'])) {
                    $callback_obj = (isset($params['callback_object'])) ? $params['callback_object'] : $this->context->controller;
                    if (!preg_match('/<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)/ism', call_user_func_array(array($callback_obj, $params['callback']), array($field_value, $row)))) {
                        $field_value = call_user_func_array(array($callback_obj, $params['callback']), array($field_value, $row));
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
     * Object Delete
     *
     * @return ObjectModel|false
     * @throws PrestaShopException
     */
    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $res = true;
            // check if request at least one object with noZeroObject
            if (isset($object->noZeroObject) && count(call_user_func(array($this->className, $object->noZeroObject))) <= 1) {
                $this->errors[] = $this->trans('You need at least one object.', array(), 'Admin.Notifications.Error').
                    ' <b>'.$this->table.'</b><br />'.
                    $this->trans('You cannot delete all of the items.', array(), 'Admin.Notifications.Error');
            } elseif (array_key_exists('delete', $this->list_skip_actions) && in_array($object->id, $this->list_skip_actions['delete'])) { //check if some ids are in list_skip_actions and forbid deletion
                    $this->errors[] = $this->trans('You cannot delete this item.', array(), 'Admin.Notifications.Error');
            } else {
                if ($this->deleted) {
                    if (!empty($this->fieldImageSettings)) {
                        $res = $object->deleteImage();
                    }

                    if (!$res) {
                        $this->errors[] = $this->trans('Unable to delete associated images.', array(), 'Admin.Notifications.Error');
                    }

                    $object->deleted = 1;
                    if ($res = $object->update()) {
                        $this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;
                    }
                } elseif ($res = $object->delete()) {
                    $this->redirect_after = self::$currentIndex.'&conf=1&token='.$this->token;
                }
                $this->errors[] = $this->trans('An error occurred during deletion.', array(), 'Admin.Notifications.Error');
                if ($res) {
                    PrestaShopLogger::addLog(sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
                }
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while deleting the object.', array(), 'Admin.Notifications.Error').
                ' <b>'.$this->table.'</b> '.
                $this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
        }
        return $object;
    }

    /**
     * Call the right method for creating or updating object
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
     * Object creation
     *
     * @return ObjectModel|false
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
                $this->errors[] = $this->trans('An error occurred while creating an object.', array(), 'Admin.Notifications.Error').
                    ' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
            } elseif (($_POST[$this->identifier] = $this->object->id /* voluntary do affectation here */) && $this->postImage($this->object->id) && !count($this->errors) && $this->_redirect) {
                PrestaShopLogger::addLog(sprintf($this->l('%s addition', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$this->object->id, true, (int)$this->context->employee->id);
                $parent_id = (int)Tools::getValue('id_parent', 1);
                $this->afterAdd($this->object);
                $this->updateAssoShop($this->object->id);
                // Save and stay on same form
                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    $this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$this->object->id.'&conf=3&update'.$this->table.'&token='.$this->token;
                }
                // Save and back to parent
                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent')) {
                    $this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$this->token;
                }
                // Default behavior (save and back)
                if (empty($this->redirect_after) && $this->redirect_after !== false) {
                    $this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$this->object->id : '').'&conf=3&token='.$this->token;
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
     * Object update
     *
     * @return ObjectModel|false|void
     * @throws PrestaShopException
     */
    public function processUpdate()
    {
        /* Checking fields validity */
        $this->validateRules();
        if (empty($this->errors)) {
            $id = (int)Tools::getValue($this->identifier);

            /* Object update */
            if (isset($id) && !empty($id)) {
                /** @var ObjectModel $object */
                $object = new $this->className($id);
                if (Validate::isLoadedObject($object)) {
                    /* Specific to objects which must not be deleted */
                    if ($this->deleted && $this->beforeDelete($object)) {
                        // Create new one with old objet values
                        /** @var ObjectModel $object_new */
                        $object_new = $object->duplicateObject();
                        if (Validate::isLoadedObject($object_new)) {
                            // Update old object to deleted
                            $object->deleted = 1;
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
                        $this->errors[] = $this->trans('An error occurred while updating an object.', array(), 'Admin.Notifications.Error').
                            ' <b>'.$this->table.'</b> ('.Db::getInstance()->getMsgError().')';
                    } elseif ($this->postImage($object->id) && !count($this->errors) && $this->_redirect) {
                        $parent_id = (int)Tools::getValue('id_parent', 1);
                        // Specific back redirect
                        if ($back = Tools::getValue('back')) {
                            $this->redirect_after = urldecode($back).'&conf=4';
                        }
                        // Save and stay on same form
                        // @todo on the to following if, we may prefer to avoid override redirect_after previous value
                        if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                            $this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=4&update'.$this->table.'&token='.$this->token;
                        }
                        // Save and back to parent
                        if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent')) {
                            $this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=4&token='.$this->token;
                        }

                        // Default behavior (save and back)
                        if (empty($this->redirect_after) && $this->redirect_after !== false) {
                            $this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=4&token='.$this->token;
                        }
                    }
                    PrestaShopLogger::addLog(sprintf($this->l('%s modification', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$object->id, true, (int)$this->context->employee->id);
                } else {
                    $this->errors[] = $this->trans('An error occurred while updating an object.', array(), 'Admin.Notifications.Error').
                        ' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
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
        return;
    }

    /**
     * Change object required fields
     *
     * @return ObjectModel
     */
    public function processUpdateFields()
    {
        if (!is_array($fields = Tools::getValue('fieldsBox'))) {
            $fields = array();
        }

        /** @var $object ObjectModel */
        $object = new $this->className();

        if (!$object->addFieldsRequiredDatabase($fields)) {
            $this->errors[] = $this->trans('An error occurred when attempting to update the required fields.', array(), 'Admin.Notifications.Error');
        } else {
            $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
        }

        return $object;
    }

    /**
     * Change object status (active, inactive)
     *
     * @return ObjectModel|false
     * @throws PrestaShopException
     */
    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->toggleStatus()) {
                $matches = array();
                if (preg_match('/[\?|&]controller=([^&]*)/', (string)$_SERVER['HTTP_REFERER'], $matches) !== false
                    && strtolower($matches[1]) != strtolower(preg_replace('/controller/i', '', get_class($this)))) {
                    $this->redirect_after = preg_replace('/[\?|&]conf=([^&]*)/i', '', (string)$_SERVER['HTTP_REFERER']);
                } else {
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                }

                $id_category = (($id_category = (int)Tools::getValue('id_category')) && Tools::getValue('id_product')) ? '&id_category='.$id_category : '';

                $page = (int)Tools::getValue('page');
                $page = $page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '';
                $this->redirect_after .= '&conf=5'.$id_category.$page;
            } else {
                $this->errors[] = $this->trans('An error occurred while updating the status.', array(), 'Admin.Notifications.Error');
            }
        } else {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error').
                ' <b>'.$this->table.'</b> '.
                $this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
        }

        return $object;
    }

    /**
     * Change object position
     *
     * @return ObjectModel|false
     */
    public function processPosition()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = $this->trans('An error occurred while updating the status for an object.', array(), 'Admin.Notifications.Error').
                ' <b>'.$this->table.'</b> '.$this->trans('(cannot load object)', array(), 'Admin.Notifications.Error');
        } elseif (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
            $this->errors[] = $this->trans('Failed to update the position.', array(), 'Admin.Notifications.Error');
        } else {
            $id_identifier_str = ($id_identifier = (int)Tools::getValue($this->identifier)) ? '&'.$this->identifier.'='.$id_identifier : '';
            $redirect = self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.$id_identifier_str.'&token='.$this->token;
            $this->redirect_after = $redirect;
        }
        return $object;
    }

    /**
     * Cancel all filters for this tab
     *
     * @param int|null $list_id
     */
    public function processResetFilters($list_id = null)
    {
        if ($list_id === null) {
            $list_id = isset($this->list_id) ? $this->list_id : $this->table;
        }

        $prefix = $this->getCookieOrderByPrefix();
        $filters = $this->context->cookie->getFamily($prefix.$list_id.'Filter_');
        foreach ($filters as $cookie_key => $filter) {
            if (strncmp($cookie_key, $prefix.$list_id.'Filter_', 7 + Tools::strlen($prefix.$list_id)) == 0) {
                $key = substr($cookie_key, 7 + Tools::strlen($prefix.$list_id));
                if (is_array($this->fields_list) && array_key_exists($key, $this->fields_list)) {
                    $this->context->cookie->$cookie_key = null;
                }
                unset($this->context->cookie->$cookie_key);
            }
        }

        if (isset($this->context->cookie->{'submitFilter'.$list_id})) {
            unset($this->context->cookie->{'submitFilter'.$list_id});
        }
        if (isset($this->context->cookie->{$prefix.$list_id.'Orderby'})) {
            unset($this->context->cookie->{$prefix.$list_id.'Orderby'});
        }
        if (isset($this->context->cookie->{$prefix.$list_id.'Orderway'})) {
            unset($this->context->cookie->{$prefix.$list_id.'Orderway'});
        }

        $_POST = array();
        $this->_filter = false;
        unset($this->_filterHaving);
        unset($this->_having);
    }

    /**
     * Update options and preferences
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
                        if (Tools::getValue($field.'_'.strtoupper($lang['iso_code']))) {
                            $fields[$field.'_'.strtoupper($lang['iso_code'])] = array(
                                'type' => 'select',
                                'cast' => 'strval',
                                'identifier' => 'mode',
                                'list' => $values['list']
                            );
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
                if ((!Shop::isFeatureActive() && isset($values['required']) && $values['required'])
                    || (Shop::isFeatureActive() && isset($_POST['multishopOverrideOption'][$field]) && isset($values['required']) && $values['required'])) {
                    if (isset($values['type']) && $values['type'] == 'textLang') {
                        foreach ($languages as $language) {
                            if (($value = Tools::getValue($field.'_'.$language['id_lang'])) == false && (string)$value != '0') {
                                $this->errors[] = $this->trans('field %s is required.', array($values['title']), 'Admin.Notifications.Error');
                            }
                        }
                    } elseif (($value = Tools::getValue($field)) == false && (string)$value != '0') {
                        $this->errors[] = $this->trans('field %s is required.', array($values['title']), 'Admin.Notifications.Error');
                    }
                }

                // Check field validator
                if (isset($values['type']) && $values['type'] == 'textLang') {
                    foreach ($languages as $language) {
                        if (Tools::getValue($field.'_'.$language['id_lang']) && isset($values['validation'])) {
                            $values_validation = $values['validation'];
                            if (!Validate::$values_validation(Tools::getValue($field.'_'.$language['id_lang']))) {
                                $this->errors[] = $this->trans('The %s field is invalid.', array($values['title']), 'Admin.Notifications.Error');
                            }
                        }
                    }
                } elseif (Tools::getValue($field) && isset($values['validation'])) {
                    $values_validation = $values['validation'];
                    if (!Validate::$values_validation(Tools::getValue($field))) {
                        $this->errors[] = $this->trans('The %s field is invalid.', array($values['title']), 'Admin.Notifications.Error');
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
                    $method_name = 'updateOption'.Tools::toCamelCase($key, true);
                    if (method_exists($this, $method_name)) {
                        $this->$method_name(Tools::getValue($key));
                    } elseif (isset($options['type']) && in_array($options['type'], array('textLang', 'textareaLang'))) {
                        $list = array();
                        foreach ($languages as $language) {
                            $key_lang = Tools::getValue($key.'_'.$language['id_lang']);
                            $val = (isset($options['cast']) ? $options['cast']($key_lang) : $key_lang);
                            if ($this->validateField($val, $options)) {
                                if (Validate::isCleanHtml($val)) {
                                    $list[$language['id_lang']] = $val;
                                } else {
                                    $this->errors[] = $this->trans('Cannot add configuration %1$s for %2$s language', array($key, Language::getIsoById((int)$language['id_lang'])), 'Admin.International.Notification');
                                }
                            }
                        }
                        Configuration::updateValue($key, $list, isset($values['validation']) && isset($options['validation']) && $options['validation'] == 'isCleanHtml' ? true : false);
                    } else {
                        $val = (isset($options['cast']) ? $options['cast'](Tools::getValue($key)) : Tools::getValue($key));
                        if ($this->validateField($val, $options)) {
                            if (Validate::isCleanHtml($val)) {
                                Configuration::updateValue($key, $val);
                            } else {
                                $this->errors[] = $this->trans('Cannot add configuration %s', array($key), 'Admin.Notifications.Error');
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
            $this->toolbar_title = array($this->toolbar_title);
        }

        switch ($this->display) {
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->page_header_toolbar_btn['back'] = array(
                        'href' => $back,
                        'desc' => $this->l('Back to list')
                    );
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
                    $this->toolbar_title[] = sprintf($this->l('Edit: %s'), (is_array($obj->{$this->identifier_name}) && isset($obj->{$this->identifier_name}[$this->context->employee->id_lang])) ? $obj->{$this->identifier_name}[$this->context->employee->id_lang] : $obj->{$this->identifier_name});
                    $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);
                }
                break;
        }

        if (is_array($this->page_header_toolbar_btn)
            && $this->page_header_toolbar_btn instanceof Traversable
            || count($this->toolbar_title)) {
            $this->show_page_header_toolbar = true;
        }

        if (empty($this->page_header_toolbar_title)) {
            $this->page_header_toolbar_title = $this->toolbar_title[count($this->toolbar_title) - 1];
        }

        $this->addPageHeaderToolBarModulesListButton();

        $this->context->smarty->assign('help_link', 'https://help.prestashop.com/'.Language::getIsoById($this->context->employee->id_lang).'/doc/'
            .Tools::getValue('controller').'?version='._PS_VERSION_.'&country='.Language::getIsoById($this->context->employee->id_lang));
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     *
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'add':
            case 'edit':
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['cancel'] = array(
                        'href' => $back,
                        'desc' => $this->l('Cancel')
                    );
                }
                break;
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->lite_display) {
                    $this->toolbar_btn['back'] = array(
                        'href' => $back,
                        'desc' => $this->l('Back to list')
                    );
                }
                break;
            case 'options':
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );
                break;
            default:
                // list
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                    'desc' => $this->l('Add new')
                );
                if ($this->allow_export) {
                    $this->toolbar_btn['export'] = array(
                        'href' => self::$currentIndex.'&export'.$this->table.'&token='.$this->token,
                        'desc' => $this->l('Export')
                    );
                }
        }
        $this->addToolBarModulesListButton();
    }

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die
     *
     * @param bool $opt Return an empty object if load fail
     * @return ObjectModel|false
     */
    protected function loadObject($opt = false)
    {
        if (!isset($this->className) || empty($this->className)) {
            return true;
        }

        $id = (int)Tools::getValue($this->identifier);
        if ($id && Validate::isUnsignedId($id)) {
            if (!$this->object) {
                $this->object = new $this->className($id);
            }
            if (Validate::isLoadedObject($this->object)) {
                return $this->object;
            }
            // throw exception
            $this->errors[] = $this->trans('The object cannot be loaded (or found)', array(), 'Admin.Notifications.Error');
            return false;
        } elseif ($opt) {
            if (!$this->object) {
                $this->object = new $this->className();
            }
            return $this->object;
        } else {
            $this->errors[] = $this->trans('The object cannot be loaded (the identifier is missing or invalid)', array(), 'Admin.Notifications.Error');
            return false;
        }
    }

    /**
     * Check if the token is valid, else display a warning page
     *
     * @return bool
     */
    public function checkAccess()
    {
        if (!$this->checkToken()) {
            // If this is an XSS attempt, then we should only display a simple, secure page
            // ${1} in the replacement string of the regexp is required,
            // because the token may begin with a number and mix up with it (e.g. $17)
            $url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$this->token.'$2', $_SERVER['REQUEST_URI']);
            if (false === strpos($url, '?token=') && false === strpos($url, '&token=')) {
                $url .= '&token='.$this->token;
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

    /**
     * @return void
     */
    public function displayAjax()
    {
        if ($this->json) {
            $this->context->smarty->assign(array(
                'json' => true,
                'status' => $this->status,
            ));
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
     * @return void
     * @throws Exception
     * @throws SmartyException
     */
    public function display()
    {
        $this->context->smarty->assign(array(
            'display_header' => $this->display_header,
            'display_header_javascript'=> $this->display_header_javascript,
            'display_footer' => $this->display_footer,
            'js_def' => Media::getJsDef(),
        ));

        // Use page title from meta_title if it has been set else from the breadcrumbs array
        if (!$this->meta_title) {
            $this->meta_title = $this->toolbar_title;
        }
        if (is_array($this->meta_title)) {
            $this->meta_title = strip_tags(implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->meta_title));
        }
        $this->context->smarty->assign('meta_title', $this->meta_title);

        $template_dirs = $this->context->smarty->getTemplateDir();

        // Check if header/footer have been overriden
        $dir = $this->context->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.trim($this->override_folder, '\\/').DIRECTORY_SEPARATOR;
        $module_list_dir = $this->context->smarty->getTemplateDir(0).'helpers'.DIRECTORY_SEPARATOR.'modules_list'.DIRECTORY_SEPARATOR;

        $header_tpl = file_exists($dir.'header.tpl') ? $dir.'header.tpl' : 'header.tpl';
        $page_header_toolbar = file_exists($dir.'page_header_toolbar.tpl') ? $dir.'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
        $footer_tpl = file_exists($dir.'footer.tpl') ? $dir.'footer.tpl' : 'footer.tpl';
        $modal_module_list = file_exists($module_list_dir.'modal.tpl') ? $module_list_dir.'modal.tpl' : 'modal.tpl';
        $tpl_action = $this->tpl_folder.$this->display.'.tpl';

        // Check if action template has been overriden
        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir.DIRECTORY_SEPARATOR.$tpl_action) && $this->display != 'view' && $this->display != 'options') {
                if (method_exists($this, $this->display.Tools::toCamelCase($this->className))) {
                    $this->{$this->display.Tools::toCamelCase($this->className)}();
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
            $this->context->smarty->assign('conf', $this->json ? json_encode($this->_conf[(int)$conf]) : $this->_conf[(int)$conf]);
        }

        if ($error = Tools::getValue('error')) {
            $this->context->smarty->assign('error', $this->json ? json_encode($this->_error[(int)$error]) : $this->_error[(int)$error]);
        }

        foreach (array('errors', 'warnings', 'informations', 'confirmations') as $type) {
            if (!is_array($this->$type)) {
                $this->$type = (array)$this->$type;
            }
            $this->context->smarty->assign($type, $this->json ? json_encode(array_unique($this->$type)) : array_unique($this->$type));
        }

        if ($this->show_page_header_toolbar && !$this->lite_display) {
            $this->context->smarty->assign(
                array(
                    'page_header_toolbar' => $this->context->smarty->fetch($page_header_toolbar),
                    'modal_module_list' => $this->context->smarty->fetch($modal_module_list),
                )
            );
        }

        $this->context->smarty->assign('baseAdminUrl', __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/');

        $this->context->smarty->assign(
            array(
                'page' =>  $this->json ? json_encode($page) : $page,
                'header' => $this->context->smarty->fetch($header_tpl),
                'footer' => $this->context->smarty->fetch($footer_tpl),
        ));

        $this->smartyOutputContent($this->layout);
    }

    /**
     * Add a warning message to display at the top of the page
     *
     * @param string $msg
     */
    protected function displayWarning($msg)
    {
        $this->warnings[] = $msg;
    }

    /**
     * Add a info message to display at the top of the page
     *
     * @param string $msg
     */
    protected function displayInformation($msg)
    {
        $this->informations[] = $msg;
    }

    /**
     * Assign smarty variables for the header
     */
    public function initHeader()
    {
        header('Cache-Control: no-store, no-cache');

        // Multishop
        $is_multishop = Shop::isFeatureActive();

        // Quick access
        if ((int) $this->context->employee->id) {
            $quick_access = QuickAccess::getQuickAccessesWithToken($this->context->language->id, (int) $this->context->employee->id);
        }

        $tabs = $this->getTabs();
        $currentTabLevel = 0;
        foreach ($tabs as &$tab) {
            $currentTabLevel = isset($tab['current_level']) ? $tab['current_level'] : $currentTabLevel;
        }


        if (Validate::isLoadedObject($this->context->employee)) {
            $accesses = Profile::getProfileAccesses($this->context->employee->id_profile, 'class_name');
            $helperShop = new HelperShop();
            /* Hooks are voluntary out the initialize array (need those variables already assigned) */
            $bo_color = empty($this->context->employee->bo_color) ? '#FFFFFF' : $this->context->employee->bo_color;
            $this->context->smarty->assign(array(
                'help_box' => Configuration::get('PS_HELPBOX'),
                'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
                'brightness' => Tools::getBrightness($bo_color) < 128 ? 'white' : '#383838',
                'bo_width' => (int)$this->context->employee->bo_width,
                'bo_color' => isset($this->context->employee->bo_color) ? Tools::htmlentitiesUTF8($this->context->employee->bo_color) : null,
                'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS') && isset($accesses['AdminOrders']) && $accesses['AdminOrders']['view'],
                'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS') && isset($accesses['AdminCustomers']) && $accesses['AdminCustomers']['view'],
                'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES') && isset($accesses['AdminCustomerThreads']) && $accesses['AdminCustomerThreads']['view'],
                'employee' => $this->context->employee,
                'search_type' => Tools::getValue('bo_search_type'),
                'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
                'quick_access' => empty($quick_access) ? false : $quick_access,
                'multi_shop' => Shop::isFeatureActive(),
                'shop_list' => $helperShop->getRenderedShopList(),
                'current_shop_name' => $helperShop->getCurrentShopName(),
                'shop' => $this->context->shop,
                'shop_group' => new ShopGroup((int)Shop::getContextShopGroupID()),
                'is_multishop' => $is_multishop,
                'multishop_context' => $this->multishop_context,
                'default_tab_link' => $this->context->link->getAdminLink(Tab::getClassNameById((int)Context::getContext()->employee->default_tab)),
                'login_link' => $this->context->link->getAdminLink('AdminLogin'),
                'collapse_menu' => isset($this->context->cookie->collapse_menu) ? (int)$this->context->cookie->collapse_menu : 0,
            ));
        } else {
            $this->context->smarty->assign('default_tab_link', $this->context->link->getAdminLink('AdminDashboard'));
        }

        // Shop::initialize() in config.php may empty $this->context->shop->virtual_uri so using a new shop instance for getBaseUrl()
        $this->context->shop = new Shop((int)$this->context->shop->id);

        $cldrRepository = new Cldr\Repository($this->context->language->language_code);

        $this->context->smarty->assign(array(
            'img_dir' => _PS_IMG_,
            'iso' => $this->context->language->iso_code,
            'class_name' => $this->className,
            'iso_user' => $this->context->language->iso_code,
            'lang_is_rtl' => $this->context->language->is_rtl,
            'country_iso_code' => $this->context->country->iso_code,
            'version' => _PS_VERSION_,
            'lang_iso' => $this->context->language->iso_code,
            'full_language_code' => $this->context->language->language_code,
            'full_cldr_language_code' => $cldrRepository->getCulture(),
            'link' => $this->context->link,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'base_url' => $this->context->shop->getBaseURL(),
            'current_parent_id' => (int)Tab::getCurrentParentId(),
            'tabs' => $tabs,
            'current_tab_level' => $currentTabLevel,
            'install_dir_exists' => file_exists(_PS_ADMIN_DIR_.'/../install'),
            'pic_dir' => _THEME_PROD_PIC_DIR_,
            'controller_name' => htmlentities(Tools::getValue('controller')),
            'currentIndex' => self::$currentIndex,
            'bootstrap' => $this->bootstrap,
            'default_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
            'display_addons_connection' => Tab::checkTabRights(Tab::getIdFromClassName('AdminModulesController'))
        ));
    }

    private function getNotificationTip($type)
    {
        $tips = array(
            'order' => array(
                $this->trans('Did you check your conversion rate lately?', array(), 'Admin.Navigation.Notification'),
                $this->trans('How about some seasonal discounts?', array(), 'Admin.Navigation.Notification'),
                $this->trans(
                    'Have you checked your [1][2]abandoned carts[/2][/1]?[3]Your next order could be hiding there!',
                        array(
                            '[1]' => '<strong>',
                            '[/1]' => '</strong>',
                            '[2]' => '<a href="'.$this->context->link->getAdminLink('AdminCarts').'&action=filterOnlyAbandonedCarts">',
                             '[/2]' => '</a>',
                            '[3]' => '<br>',
                        ),
                        'Admin.Navigation.Notification'
                ),
            ),
            'customer' => array(
                $this->trans('Have you sent any acquisition email lately?', array(), 'Admin.Navigation.Notification'),
                $this->trans('Are you active on social media these days?', array(), 'Admin.Navigation.Notification'),
                $this->trans('Have you considered selling on marketplaces?', array(), 'Admin.Navigation.Notification'),
            ),
            'customer_message' => array(
                $this->trans('That\'s more time for something else!', array(), 'Admin.Navigation.Notification'),
                $this->trans('No news is good news, isn\'t it?', array(), 'Admin.Navigation.Notification'),
                $this->trans('Seems like all your customers are happy :)', array(), 'Admin.Navigation.Notification'),
            ),
        );

        if (!isset($tips[$type])) {
            return '';
        }

        return $tips[$type][array_rand($tips[$type])];
    }

    private function getTabs($parentId = 0, $level = 0)
    {
        $tabs = Tab::getTabs($this->context->language->id, $parentId);
        $current_id = Tab::getCurrentParentId($this->controller_name ? $this->controller_name : '');

        foreach ($tabs as $index => $tab) {
            if (!Tab::checkTabRights($tab['id_tab'])
                || ($tab['class_name'] == 'AdminStock' && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
                || $tab['class_name'] == 'AdminCarrierWizard') {
                unset($tabs[$index]);

                continue;
            }

            // tab[class_name] does not contains the "Controller" suffix
            if (($tab['class_name'].'Controller' == get_class($this)) || ($current_id == $tab['id_tab']) || $tab['class_name'] == $this->controller_name) {
                $tabs[$index]['current'] = true;
                $tabs[$index]['current_level'] = $level;
            } else {
                $tabs[$index]['current'] = false;
            }
            $tabs[$index]['img'] = null;
            $tabs[$index]['href'] = $this->context->link->getAdminLink($tab['class_name']);
            $tabs[$index]['sub_tabs'] = array_values($this->getTabs($tab['id_tab'], $level + 1));

            if (empty($tabs[$index]['icon'])) {
                $tabs[$index]['icon'] = 'extension';
            }

            if (isset($tabs[$index]['sub_tabs'][0])) {
                $tabs[$index]['href'] = $tabs[$index]['sub_tabs'][0]['href'];
            } elseif (0 == $tabs[$index]['id_parent'] && '' == $tabs[$index]['icon']) {
                unset($tabs[$index]);
            }

            if (array_key_exists($index, $tabs) && array_key_exists('sub_tabs', $tabs[$index])) {
                foreach ($tabs[$index]['sub_tabs'] as $sub_tab) {
                    if ((int)$sub_tab['current'] == true) {
                        $tabs[$index]['current'] = true;
                        $tabs[$index]['current_level'] = $sub_tab['current_level'];
                    }
                }
            }
        }

        return $tabs;
    }

    /**
     * Declare an action to use for each row in the list
     *
     * @param string $action
     */
    public function addRowAction($action)
    {
        $action = strtolower($action);
        $this->actions[] = $action;
    }

    /**
     * Add an action to use for each row in the list
     *
     * @param string $action
     * @param array $list
     */
    public function addRowActionSkipList($action, $list)
    {
        $action = strtolower($action);
        $list = (array)$list;

        if (array_key_exists($action, $this->list_skip_actions)) {
            $this->list_skip_actions[$action] = array_merge($this->list_skip_actions[$action], $list);
        } else {
            $this->list_skip_actions[$action] = $list;
        }
    }

    /**
     * Assign smarty variables for all default views, list and form, then call other init functions
     */
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = $this->trans('You do not have permission to view this.', array(), 'Admin.Notifications.Error');
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
            // FIXME: Sorry. I'm not very proud of this, but no choice... Please wait sf refactoring to solve this.
            if (get_class($this) != 'AdminCarriersController') {
                $this->content .= $this->renderModulesList();
            }
            $this->content .= $this->renderKpis();
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();

            // if we have to display the required fields form
            if ($this->required_database) {
                $this->content .= $this->displayRequiredFields();
            }
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function initToolbarFlags()
    {
        $this->getLanguages();

        $this->initToolbar();
        $this->initTabModuleList();
        $this->initPageHeaderToolbar();

        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
            'debug_mode' => (bool)_PS_MODE_DEV_,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }
    /**
     * Init tab modules list and add button in toolbar
     */
    protected function initTabModuleList()
    {
        if (!$this->isFresh(Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, 86400)) {
            @file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, Tools::addonsRequest('must-have'));
        }
        if (!$this->isFresh(Module::CACHE_FILE_TAB_MODULES_LIST, 604800)) {
            $this->refresh(Module::CACHE_FILE_TAB_MODULES_LIST, _PS_TAB_MODULE_LIST_URL_);
        }

        $this->tab_modules_list = Tab::getTabModulesList($this->id);

        if (is_array($this->tab_modules_list['default_list']) && count($this->tab_modules_list['default_list'])) {
            $this->filter_modules_list = $this->tab_modules_list['default_list'];
        } elseif (is_array($this->tab_modules_list['slider_list']) && count($this->tab_modules_list['slider_list'])) {
            $this->addToolBarModulesListButton();
            $this->addPageHeaderToolBarModulesListButton();
            $this->context->smarty->assign(array(
                'tab_modules_list' => implode(',', $this->tab_modules_list['slider_list']),
                'admin_module_ajax_url' => $this->context->link->getAdminLink('AdminModules'),
                'back_tab_modules_list' => $this->context->link->getAdminLink(Tools::getValue('controller')),
                'tab_modules_open' => (int)Tools::getValue('tab_modules_open')
            ));
        }
    }

    protected function addPageHeaderToolBarModulesListButton()
    {
        $this->filterTabModuleList();

        if (is_array($this->tab_modules_list['slider_list']) && count($this->tab_modules_list['slider_list'])) {
            $this->page_header_toolbar_btn['modules-list'] = array(
                'href' => $this->getAdminModulesUrl(),
                'desc' => $this->l('Recommended Modules and Services')
            );
        }
    }

    protected function addToolBarModulesListButton()
    {
        $this->filterTabModuleList();

        if (is_array($this->tab_modules_list['slider_list']) && count($this->tab_modules_list['slider_list'])) {
            $this->toolbar_btn['modules-list'] = array(
                'href' => $this->getAdminModulesUrl(),
                'desc' => $this->l('Recommended Modules and Services')
            );
        }
    }

    protected function getAdminModulesUrl()
    {
        return $this->context->link->getAdminLink('AdminModulesSf');
    }

    protected function filterTabModuleList()
    {
        static $list_is_filtered = null;

        if ($list_is_filtered !== null) {
            return;
        }

        if (!$this->isFresh(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, 86400)) {
            file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, Tools::addonsRequest('native'));
        }

        if (!$this->isFresh(Module::CACHE_FILE_ALL_COUNTRY_MODULES_LIST, 86400)) {
            file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_ALL_COUNTRY_MODULES_LIST, Tools::addonsRequest('native_all'));
        }

        if (!$this->isFresh(Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, 86400)) {
            @file_put_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST, Tools::addonsRequest('must-have'));
        }

        libxml_use_internal_errors(true);

        $country_module_list = file_get_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST);
        $must_have_module_list = file_get_contents(_PS_ROOT_DIR_.Module::CACHE_FILE_MUST_HAVE_MODULES_LIST);
        $all_module_list = array();

        if (!empty($country_module_list) && $country_module_list_xml = @simplexml_load_string($country_module_list)) {
            $country_module_list_array = array();
            if (is_object($country_module_list_xml->module)) {
                foreach ($country_module_list_xml->module as $k => $m) {
                    $all_module_list[] = (string)$m->name;
                }
            }
        } else {
            foreach (libxml_get_errors() as $error) {
                $this->errors[] = $this->trans('Error found : %1$s in country_module_list.xml file.', array($error->message), 'Admin.Modules.Notification');
            }
        }

        libxml_clear_errors();



        if (!empty($must_have_module_list) && $must_have_module_list_xml = @simplexml_load_string($must_have_module_list)) {
            $must_have_module_list_array = array();
            if (is_object($country_module_list_xml->module)) {
                foreach ($must_have_module_list_xml->module as $l => $mo) {
                    $all_module_list[] = (string)$mo->name;
                }
            }
        } else {
            foreach (libxml_get_errors() as $error) {
                $this->errors[] = $this->trans('Error found : %1$s in must_have_module_list.xml file.', array($error->message), 'Admin.Modules.Notification');
            }
        }

        libxml_clear_errors();

        $this->tab_modules_list['slider_list'] = array_intersect($this->tab_modules_list['slider_list'], $all_module_list);

        $list_is_filtered = true;
    }

    /**
     * Initialize the invalid doom page of death
     *
     * @return void
     */
    public function initCursedPage()
    {
        $this->layout = 'invalid_token.tpl';
    }

    /**
     * Assign smarty variables for the footer
     */
    public function initFooter()
    {
        //RTL Support
        //rtl.js overrides inline styles
        //iso_code.css overrides default fonts for every language (optional)
        if ($this->context->language->is_rtl) {
            $this->addJS(_PS_JS_DIR_.'rtl.js');
            $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/'.$this->context->language->iso_code.'.css', 'all', false);
        }

        // We assign js and css files on the last step before display template, because controller can add many js and css files
        $this->context->smarty->assign('css_files', $this->css_files);
        $this->context->smarty->assign('js_files', array_unique($this->js_files));

        $this->context->smarty->assign(array(
            'ps_version' => _PS_VERSION_,
            'timer_start' => $this->timer_start,
            'iso_is_fr' => strtoupper($this->context->language->iso_code) == 'FR',
            'modals' => $this->renderModal(),
        ));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function initModal()
    {
        if ($this->logged_on_addons) {
            $this->context->smarty->assign(array(
                'logged_on_addons' => 1,
                'username_addons' => $this->context->cookie->username_addons
            ));
        }

        // Iso needed to generate Addons login
        $iso_code_caps = strtoupper($this->context->language->iso_code);

        $this->context->smarty->assign(array(
            'img_base_path' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/',
            'check_url_fopen' => (ini_get('allow_url_fopen') ? 'ok' : 'ko'),
            'check_openssl' => (extension_loaded('openssl') ? 'ok' : 'ko'),
            'add_permission' => 1,
            'addons_register_link' => 'https://addons.prestashop.com/'.$this->context->language->iso_code.'/login?'
                .'email='.urlencode($this->context->employee->email)
                .'&firstname='.urlencode($this->context->employee->firstname)
                .'&lastname='.urlencode($this->context->employee->lastname)
                .'&website='.urlencode($this->context->shop->getBaseURL())
                .'&utm_source=back-office&utm_medium=connect-to-addons'
                .'&utm_campaign=back-office-'.Tools::strtoupper($this->context->language->iso_code)
                .'&utm_content='.(defined('_PS_HOST_MODE_') ? 'cloud' : 'download').'#createnow',
            'addons_forgot_password_link' => '//addons.prestashop.com/'.$this->context->language->iso_code.'/forgot-your-password'
        ));

        $this->modals[] = array(
            'modal_id' => 'modal_addons_connect',
            'modal_class' => 'modal-md',
            'modal_title' => '<i class="icon-puzzle-piece"></i> <a target="_blank" href="https://addons.prestashop.com/'
            .'?utm_source=back-office&utm_medium=modules'
            .'&utm_campaign=back-office-'.Tools::strtoupper($this->context->language->iso_code)
            .'&utm_content='.(defined('_PS_HOST_MODE_') ? 'cloud' : 'download').'">PrestaShop Addons</a>',
            'modal_content' => $this->context->smarty->fetch('controllers/modules/login_addons.tpl'),
        );
    }

    /**
     * @return string
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
     * @param string|bool $tracking_source Source information for URL used by "Install" button
     * @return string
     */
    public function renderModulesList($tracking_source = false)
    {
        // Load cache file modules list (natives and partners modules)
        $xml_modules = false;
        if (file_exists(_PS_ROOT_DIR_.Module::CACHE_FILE_MODULES_LIST)) {
            $xml_modules = @simplexml_load_file(_PS_ROOT_DIR_.Module::CACHE_FILE_MODULES_LIST);
        }
        if ($xml_modules) {
            foreach ($xml_modules->children() as $xml_module) {
                /** @var SimpleXMLElement $xml_module */
                foreach ($xml_module->children() as $module) {
                    /** @var SimpleXMLElement $module */
                    foreach ($module->attributes() as $key => $value) {
                        if ($xml_module->attributes() == 'native' && $key == 'name') {
                            $this->list_natives_modules[] = (string)$value;
                        }
                        if ($xml_module->attributes() == 'partner' && $key == 'name') {
                            $this->list_partners_modules[] = (string)$value;
                        }
                    }
                }
            }
        }

        if ($this->getModulesList($this->filter_modules_list, $tracking_source)) {
            $tmp = array();
            foreach ($this->modules_list as $key => $module) {
                if ($module->active) {
                    $tmp[] = $module;
                    unset($this->modules_list[$key]);
                }
            }

            $this->modules_list = array_merge($tmp, $this->modules_list);

            foreach ($this->modules_list as $key => $module) {
                if (in_array($module->name, $this->list_partners_modules)) {
                    $this->modules_list[$key]->type = 'addonsPartner';
                }
                if (isset($module->description_full) && trim($module->description_full) != '') {
                    $module->show_quick_view = true;
                    $module->optionsHtml = array($module->optionsHtml[0]);
                }
            }
            $helper = new Helper();
            return $helper->renderModulesList($this->modules_list);
        }
    }

    /**
     * Function used to render the list to display for this controller
     *
     * @return string|false
     * @throws PrestaShopException
     */
    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        // If list has 'active' field, we automatically create bulk action
        if (isset($this->fields_list) && is_array($this->fields_list) && array_key_exists('active', $this->fields_list)
            && !empty($this->fields_list['active'])) {
            if (!is_array($this->bulk_actions)) {
                $this->bulk_actions = array();
            }

            $this->bulk_actions = array_merge(array(
                'enableSelection' => array(
                    'text' => $this->l('Enable selection'),
                    'icon' => 'icon-power-off text-success'
                ),
                'disableSelection' => array(
                    'text' => $this->l('Disable selection'),
                    'icon' => 'icon-power-off text-danger'
                ),
                'divider' => array(
                    'text' => 'divider'
                )
            ), $this->bulk_actions);
        }

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->l('Bad SQL query', 'Helper').'<br />'.htmlspecialchars($this->_list_error));
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
        $helper->sql = $this->_listsql;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    public function getTemplateListVars()
    {
        return $this->tpl_list_vars;
    }

    /**
     * Override to render the view page
     *
     * @return string
     */
    public function renderView()
    {
        $helper = new HelperView($this);
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->getTemplateViewVars();
        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }
        $view = $helper->generateView();

        return $view;
    }

    public function getTemplateViewVars()
    {
        return $this->tpl_view_vars;
    }

    /**
     * Override to render the view page
     *
     * @return string|false
     */
    public function renderDetails()
    {
        return $this->renderList();
    }

    /**
     * Function used to render the form for this controller
     *
     * @return string
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
                $this->fields_form = array(array('form' => $this->fields_form));
            }

            // For add a fields via an override of $fields_form, use $fields_form_override
            if (is_array($this->fields_form_override) && !empty($this->fields_form_override)) {
                $this->fields_form[0]['form']['input'] = array_merge($this->fields_form[0]['form']['input'], $this->fields_form_override);
            }

            $fields_value = $this->getFieldsValue($this->object);

            Hook::exec('action'.$this->controller_name.'FormModifier', array(
                'object' => &$this->object,
                'fields' => &$this->fields_form,
                'fields_value' => &$fields_value,
                'form_vars' => &$this->tpl_form_vars,
            ));

            $helper = new HelperForm($this);
            $this->setHelperDisplay($helper);
            $helper->fields_value = $fields_value;
            $helper->submit_action = $this->submit_action;
            $helper->tpl_vars = $this->getTemplateFormVars();
            $helper->show_cancel_button = (isset($this->show_form_cancel_button)) ? $this->show_form_cancel_button : ($this->display == 'add' || $this->display == 'edit');

            $back = urldecode(Tools::getValue('back', ''));
            if (empty($back)) {
                $back = self::$currentIndex.'&token='.$this->token;
            }
            if (!Validate::isCleanHtml($back)) {
                die(Tools::displayError());
            }

            $helper->back_url = $back;
            !is_null($this->base_tpl_form) ? $helper->base_tpl = $this->base_tpl_form : '';
            if ($this->access('view')) {
                if (Tools::getValue('back')) {
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue('back'));
                } else {
                    $helper->tpl_vars['back'] = Tools::safeOutput(Tools::getValue(self::$currentIndex.'&token='.$this->token));
                }
            }
            $form = $helper->generateForm($this->fields_form);

            return $form;
        }
    }

    public function getTemplateFormVars()
    {
        return $this->tpl_form_vars;
    }

    public function renderKpis()
    {
    }

    /**
     * Function used to render the options for this controller
     *
     * @return string
     */
    public function renderOptions()
    {
        Hook::exec('action'.$this->controller_name.'OptionsModifier', array(
            'options' => &$this->fields_options,
            'option_vars' => &$this->tpl_option_vars,
        ));

        if ($this->fields_options && is_array($this->fields_options)) {
            if (isset($this->display) && $this->display != 'options' && $this->display != 'list') {
                $this->show_toolbar = false;
            } else {
                $this->display = 'options';
            }

            unset($this->toolbar_btn);
            $this->initToolbar();
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }

    /**
     * This function sets various display options for helper list
     *
     * @param Helper $helper
     * @return void
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
        $helper->title = is_array($this->toolbar_title) ? implode(' '.Configuration::get('PS_NAVIGATION_PIPE').' ', $this->toolbar_title) : $this->toolbar_title;
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->show_toolbar = $this->show_toolbar;
        $helper->toolbar_scroll = $this->toolbar_scroll;
        $helper->override_folder = $this->tpl_folder;
        $helper->actions = $this->actions;
        $helper->simple_header = $this->list_simple_header;
        $helper->bulk_actions = $this->bulk_actions;
        $helper->currentIndex = self::$currentIndex;
        $helper->className = $this->className;
        $helper->table = $this->table;
        $helper->name_controller = Tools::getValue('controller');
        $helper->orderBy = $this->_orderBy;
        $helper->orderWay = $this->_orderWay;
        $helper->listTotal = $this->_listTotal;
        $helper->shopLink = $this->shopLink;
        $helper->shopLinkType = $this->shopLinkType;
        $helper->identifier = $this->identifier;
        $helper->token = $this->token;
        $helper->languages = $this->_languages;
        $helper->specificConfirmDelete = $this->specificConfirmDelete;
        $helper->imageType = $this->imageType;
        $helper->no_link = $this->list_no_link;
        $helper->colorOnBackground = $this->colorOnBackground;
        $helper->ajax_params = (isset($this->ajax_params) ? $this->ajax_params : null);
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->multiple_fieldsets = $this->multiple_fieldsets;
        $helper->row_hover = $this->row_hover;
        $helper->position_identifier = $this->position_identifier;
        $helper->position_group_identifier = $this->position_group_identifier;
        $helper->controller_name = $this->controller_name;
        $helper->list_id = isset($this->list_id) ? $this->list_id : $this->table;
        $helper->bootstrap = $this->bootstrap;

        // For each action, try to add the corresponding skip elements list
        $helper->list_skip_actions = $this->list_skip_actions;

        $this->helper = $helper;
    }

    /**
     * @deprecated 1.6.0
     */
    public function setDeprecatedMedia()
    {
    }

    public function setMedia($isNewTheme = false)
    {
        if ($isNewTheme) {
            $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/new-theme/public/theme.css', 'all', 1);
            $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/new-theme/public/main.bundle.js');
            $this->addjQueryPlugin(array('chosen'));
        } else {

            //Bootstrap
            $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/'.$this->bo_css, 'all', 0);
            $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/vendor/titatoggle-min.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/public/theme.css', 'all', 0);

            $this->addJquery();
            $this->addjQueryPlugin(array('scrollTo', 'alerts', 'chosen', 'autosize', 'fancybox' ));
            $this->addjQueryPlugin('growl', null, false);
            $this->addJqueryUI(array('ui.slider', 'ui.datepicker'));

            $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/bootstrap.min.js');
            $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/modernizr.min.js');
            $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/modernizr-loads.js');
            $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/moment-with-langs.min.js');
            $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/public/bundle.js');

            $this->addJS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

            if (!$this->lite_display) {
                $this->addJS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/js/help.js');
            }

            if (!Tools::getValue('submitFormAjax')) {
                $this->addJS(_PS_JS_DIR_.'admin/notifications.js');
            }

            if (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_) {
                $this->addJS('https://cdn.statuspage.io/se-v2.js');

                Media::addJsDefL('status_operational', $this->l('Operational', null, true, false));
                Media::addJsDefL('status_degraded_performance', $this->l('Degraded Performance', null, true, false));
                Media::addJsDefL('status_partial_outage', $this->l('Partial Outage', null, true, false));
                Media::addJsDefL('status_major_outage', $this->l('Major Outage', null, true, false));
                Media::addJsDef(array('host_cluster' => defined('_PS_HOST_CLUSTER_') ? _PS_HOST_CLUSTER_ : 'fr1'));
            }

            // Specific Admin Theme
            $this->addCSS(__PS_BASE_URI__.$this->admin_webpath.'/themes/'.$this->bo_theme.'/css/overrides.css', 'all', PHP_INT_MAX);
        }

        $this->addJS(array(
            _PS_JS_DIR_.'admin.js?v='._PS_VERSION_, // TODO: SEE IF REMOVABLE
            _PS_JS_DIR_.'cldr.js',
            _PS_JS_DIR_.'tools.js?v='._PS_VERSION_,
            __PS_BASE_URI__.$this->admin_webpath.'/public/bundle.js',
        ));

        Media::addJsDef(array('host_mode' => (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_)));
        Media::addJsDef(array('baseDir' => __PS_BASE_URI__));
        Media::addJsDef(array('baseAdminDir' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/'));
        Media::addJsDef(array('currency' => array(
            'iso_code' => Context::getContext()->currency->iso_code,
            'sign' => Context::getContext()->currency->sign,
            'name' => Context::getContext()->currency->name,
            'format' => Context::getContext()->currency->format,
        )));

        // Execute Hook AdminController SetMedia
        Hook::exec('actionAdminControllerSetMedia');
    }

    /**
     * Non-static method which uses AdminController::translate()
     *
     * @deprecated use Context::getContext()->getTranslator()->trans($id, $parameters, $domain, $locale); instead
     * @param string  $string Term or expression in english
     * @param string|null $class Name of the class
     * @param bool $addslashes If set to true, the return value will pass through addslashes(). Otherwise, stripslashes().
     * @param bool $htmlentities If set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
     * @return string The translation if available, or the english default text.
     */
    protected function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        $translated = $this->translator->trans($string);
        if ($translated !== $string) {
            return $translated;
        }

        if ($class === null || $class == 'AdminTab') {
            $class = substr(get_class($this), 0, -10);
        } elseif (strtolower(substr($class, -10)) == 'controller') {
            /* classname has changed, from AdminXXX to AdminXXXController, so we remove 10 characters and we keep same keys */
            $class = substr($class, 0, -10);
        }
        return Translate::getAdminTranslation($string, $class, $addslashes, $htmlentities);
    }

    /**
     * Init context and dependencies, handles POST and GET
     */
    public function init()
    {
        parent::init();

        if (Tools::getValue('ajax')) {
            $this->ajax = '1';
        }

        if (is_null($this->context->link)) {
            $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
            $this->context->link = new Link($protocol_link, $protocol_content);
        }


        if (isset($_GET['logout'])) {
            $this->context->employee->logout();
        }
        if (isset(Context::getContext()->cookie->last_activity)) {
            if ($this->context->cookie->last_activity + 900 < time()) {
                $this->context->employee->logout();
            } else {
                $this->context->cookie->last_activity = time();
            }
        }
        if ($this->controller_name != 'AdminLogin' && (!isset($this->context->employee) || !$this->context->employee->isLoggedBack())) {
            if (isset($this->context->employee)) {
                $this->context->employee->logout();
            }
            $email = false;
            if (Tools::getValue('email') && Validate::isEmail(Tools::getValue('email'))) {
                $email = Tools::getValue('email');
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminLogin').((!isset($_GET['logout']) && $this->controller_name != 'AdminNotFound' && Tools::getValue('controller')) ? '&redirect='.$this->controller_name : '').($email ? '&email='.$email : ''));
        }
        // Set current index
        $current_index = 'index.php'.(($controller = Tools::getValue('controller')) ? '?controller='.$controller : '');
        if ($back = Tools::getValue('back')) {
            $current_index .= '&back='.urlencode($back);
        }
        self::$currentIndex = $current_index;

        if ((int)Tools::getValue('liteDisplaying')) {
            $this->display_header = false;
            $this->display_header_javascript = true;
            $this->display_footer = false;
            $this->content_only = false;
            $this->lite_display = true;
        }

        if ($this->ajax && method_exists($this, 'ajaxPreprocess')) {
            $this->ajaxPreProcess();
        }

        $this->context->smarty->assign(array(
            'table' => $this->table,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'host_mode' => defined('_PS_HOST_MODE_') ? 1 : 0,
            'stock_management' => (int)Configuration::get('PS_STOCK_MANAGEMENT'),
            'no_order_tip' => $this->getNotificationTip('order'),
            'no_customer_tip' => $this->getNotificationTip('customer'),
            'no_customer_message_tip' => $this->getNotificationTip('customer_message'),
        ));

        if ($this->display_header) {
            $this->context->smarty->assign('displayBackOfficeHeader', Hook::exec('displayBackOfficeHeader', array()));
        }

        $this->context->smarty->assign(array(
            'displayBackOfficeTop' => Hook::exec('displayBackOfficeTop', array()),
            'submit_form_ajax' => (int)Tools::getValue('submitFormAjax')
        ));

        Employee::setLastConnectionDate($this->context->employee->id);

        $this->initProcess();
        $this->initBreadcrumbs();
        $this->initModal();
        $this->initToolbarFlags();
        $this->initNotifications();
    }

    /**
     * Sets the smarty variables and js defs used to show / hide some notifications
     */
    public function initNotifications()
    {
        $notificationsSettings = array(
            'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS'),
            'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS'),
            'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES '),
        );

        $this->context->smarty->assign($notificationsSettings);

        Media::addJsDef($notificationsSettings);
    }

    /**
     * @throws PrestaShopException
     */
    public function initShopContext()
    {
        if (!$this->context->employee->isLoggedBack()) {
            return;
        }

        // Change shop context ?
        if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false) {
            $this->context->cookie->shopContext = Tools::getValue('setShopContext');
            $url = parse_url($_SERVER['REQUEST_URI']);
            $query = (isset($url['query'])) ? $url['query'] : '';
            parse_str($query, $parse_query);
            unset($parse_query['setShopContext'], $parse_query['conf']);
            $http_build_query = http_build_query($parse_query, '', '&');
            $this->redirect_after = $url['path'].($http_build_query ? '?'.$http_build_query : '');
        } elseif (!Shop::isFeatureActive()) {
            $this->context->cookie->shopContext = 's-'.(int)Configuration::get('PS_SHOP_DEFAULT');
        } elseif (Shop::getTotalShops(false, null) < 2) {
            $this->context->cookie->shopContext = 's-'.(int)$this->context->employee->getDefaultShopID();
        }

        $shop_id = '';
        Shop::setContext(Shop::CONTEXT_ALL);
        if ($this->context->cookie->shopContext) {
            $split = explode('-', $this->context->cookie->shopContext);
            if (count($split) == 2) {
                if ($split[0] == 'g') {
                    if ($this->context->employee->hasAuthOnShopGroup((int)$split[1])) {
                        Shop::setContext(Shop::CONTEXT_GROUP, (int)$split[1]);
                    } else {
                        $shop_id = (int)$this->context->employee->getDefaultShopID();
                        Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                    }
                } elseif (Shop::getShop($split[1]) && $this->context->employee->hasAuthOnShop($split[1])) {
                    $shop_id = (int)$split[1];
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                } else {
                    $shop_id = (int)$this->context->employee->getDefaultShopID();
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
            $this->context->shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
        } elseif ($this->context->shop->id != $shop_id) {
            $this->context->shop = new Shop((int)$shop_id);
        }

        // Replace current default country
        $this->context->country = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT'));
        $this->context->currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
    }

    /**
     * Retrieve GET and POST value and translate them to actions
     */
    public function initProcess()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        // Manage list filtering
        if (Tools::isSubmit('submitFilter'.$this->list_id)
            || $this->context->cookie->{'submitFilter'.$this->list_id} !== false
            || Tools::getValue($this->list_id.'Orderby')
            || Tools::getValue($this->list_id.'Orderway')) {
            $this->filter = true;
        }

        $this->id_object = (int)Tools::getValue($this->identifier);

        /* Delete object image */
        if (isset($_GET['deleteImage'])) {
            if ($this->access('delete')) {
                $this->action = 'delete_image';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['delete'.$this->table])) {
            /* Delete object */
            if ($this->access('delete')) {
                $this->action = 'delete';
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        } elseif ((isset($_GET['status'.$this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier)) {
            /* Change object statuts (active, inactive) */
            if ($this->access('edit')) {
                $this->action = 'status';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['position'])) {
            /* Move an object */
            if ($this->access('edit') == '1') {
                $this->action = 'position';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitAdd'.$this->table)
                 || Tools::isSubmit('submitAdd'.$this->table.'AndStay')
                 || Tools::isSubmit('submitAdd'.$this->table.'AndPreview')
                 || Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent')) {
            // case 1: updating existing entry
            if ($this->id_object) {
                if ($this->access('edit')) {
                    $this->action = 'save';
                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        $this->display = 'edit';
                    } else {
                        $this->display = 'list';
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                }
            } else {
                // case 2: creating new entry
                if ($this->access('add')) {
                    $this->action = 'save';
                    if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                        $this->display = 'edit';
                    } else {
                        $this->display = 'list';
                    }
                } else {
                    $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
                }
            }
        } elseif (isset($_GET['add'.$this->table])) {
            if ($this->access('add')) {
                $this->action = 'new';
                $this->display = 'add';
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['update'.$this->table]) && isset($_GET[$this->identifier])) {
            $this->display = 'edit';
            if (!$this->access('edit')) {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['view'.$this->table])) {
            if ($this->access('view')) {
                $this->display = 'view';
                $this->action = 'view';
            } else {
                $this->errors[] = $this->trans('You do not have permission to view this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['details'.$this->table])) {
            if ($this->access('view')) {
                $this->display = 'details';
                $this->action = 'details';
            } else {
                $this->errors[] = $this->trans('You do not have permission to view this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (isset($_GET['export'.$this->table])) {
            if ($this->access('view')) {
                $this->action = 'export';
            }
        } elseif (isset($_POST['submitReset'.$this->list_id])) {
            /* Cancel all filters for this tab */
            $this->action = 'reset_filters';
        } elseif (Tools::isSubmit('submitOptions'.$this->table) || Tools::isSubmit('submitOptions')) {
            /* Submit options list */
            $this->display = 'options';
            if ($this->access('edit')) {
                $this->action = 'update_options';
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        } elseif (Tools::getValue('action') && method_exists($this, 'process'.ucfirst(Tools::toCamelCase(Tools::getValue('action'))))) {
            $this->action = Tools::getValue('action');
        } elseif (Tools::isSubmit('submitFields') && $this->required_database && $this->access('add') && $this->access('delete')) {
            $this->action = 'update_fields';
        } elseif (is_array($this->bulk_actions)) {
            $submit_bulk_actions = array_merge(array(
                'enableSelection' => array(
                    'text' => $this->l('Enable selection'),
                    'icon' => 'icon-power-off text-success'
                ),
                'disableSelection' => array(
                    'text' => $this->l('Disable selection'),
                    'icon' => 'icon-power-off text-danger'
                )
            ), $this->bulk_actions);
            foreach ($submit_bulk_actions as $bulk_action => $params) {
                if (Tools::isSubmit('submitBulk'.$bulk_action.$this->table) || Tools::isSubmit('submitBulk'.$bulk_action)) {
                    if ($bulk_action === 'delete') {
                        if ($this->access('delete')) {
                            $this->action = 'bulk'.$bulk_action;
                            $this->boxes = Tools::getValue($this->table.'Box');
                            if (empty($this->boxes) && $this->table == 'attribute') {
                                $this->boxes = Tools::getValue($this->table.'_valuesBox');
                            }
                        } else {
                            $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
                        }
                        break;
                    } elseif ($this->access('edit')) {
                        $this->action = 'bulk'.$bulk_action;
                        $this->boxes = Tools::getValue($this->table.'Box');
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                    }
                    break;
                } elseif (Tools::isSubmit('submitBulk')) {
                    if ($bulk_action === 'delete') {
                        if ($this->access('delete')) {
                            $this->action = 'bulk'.$bulk_action;
                            $this->boxes = Tools::getValue($this->table.'Box');
                        } else {
                            $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
                        }
                        break;
                    } elseif ($this->access('edit')) {
                        $this->action = 'bulk'.Tools::getValue('select_submitBulk');
                        $this->boxes = Tools::getValue($this->table.'Box');
                    } else {
                        $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
                    }
                    break;
                }
            }
        } elseif (!empty($this->fields_options) && empty($this->fields_list)) {
            $this->display = 'options';
        }
    }

    /**
     * Get the current objects' list form the database
     *
     * @param int $id_lang Language used for display
     * @param string|null $order_by ORDER BY clause
     * @param string|null $order_way Order way (ASC, DESC)
     * @param int $start Offset in LIMIT clause
     * @param int|null $limit Row count in LIMIT clause
     * @param int|bool $id_lang_shop
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
        Hook::exec('action'.$this->controller_name.'ListingFieldsModifier', array(
            'select' => &$this->_select,
            'join' => &$this->_join,
            'where' => &$this->_where,
            'group_by' => &$this->_group,
            'order_by' => &$this->_orderBy,
            'order_way' => &$this->_orderWay,
            'fields' => &$this->fields_list,
        ));

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
        if ((int)Tools::getValue('submitFilter'.$this->list_id)) {
            $start = ((int)Tools::getValue('submitFilter'.$this->list_id) - 1) * $limit;
        } elseif (
            empty($start)
            && isset($this->context->cookie->{$this->list_id.'_start'})
            && Tools::isSubmit('export'.$this->table)
        ) {
            $start = $this->context->cookie->{$this->list_id.'_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->context->cookie->{$this->list_id.'_start'} = $start;
        } elseif (isset($this->context->cookie->{$this->list_id.'_start'})) {
            unset($this->context->cookie->{$this->list_id.'_start'});
        }

        /* Cache */
        $this->_lang = (int)$id_lang;

        // Add SQL shop restriction
        $select_shop = '';
        if ($this->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($this->multishop_context && Shop::isTableAssociated($this->table) && !empty($this->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->context->employee->isSuperAdmin()) {
                $test_join = !preg_match('#`?'.preg_quote(_DB_PREFIX_.$this->table.'_shop').'`? *sa#', $this->_join);
                if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($this->table)) {
                    $this->_where .= ' AND EXISTS (
                        SELECT 1
                        FROM `'._DB_PREFIX_.$this->table.'_shop` sa
                        WHERE a.`'.bqSQL($this->identifier).'` = sa.`'.bqSQL($this->identifier).'`
                         AND sa.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')
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
                    if (isset($this->_select) && preg_match('/[\s]`?'.preg_quote($key, '/').'`?\s*,/', $this->_select)) {
                        continue;
                    }

                    if (isset($array_value['filter_key'])) {
                        $this->_listsql .= str_replace('!', '.`', $array_value['filter_key']).'` AS `'.$key.'`, ';
                    } elseif ($key == 'id_'.$this->table) {
                        $this->_listsql .= 'a.`'.bqSQL($key).'`, ';
                    } elseif ($key != 'image' && !preg_match('/'.preg_quote($key, '/').'/i', $this->_select)) {
                        $this->_listsql .= '`'.bqSQL($key).'`, ';
                    }
                }
                $this->_listsql = rtrim(trim($this->_listsql), ',');
            } else {
                $this->_listsql .= ($this->lang ? 'b.*,' : '').' a.*';
            }

            $this->_listsql .= "\n".(isset($this->_select) ? ', '.rtrim($this->_select, ', ') : '').$select_shop;

            $limitClause = ' '.(($shouldLimitSqlResults) ? ' LIMIT '.(int)$start.', '.(int)$limit : '');

            if ($this->_use_found_rows || isset($this->_filterHaving) || isset($this->_having)) {
                $this->_listsql = 'SELECT SQL_CALC_FOUND_ROWS '.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').
                    $this->_listsql.
                    $fromClause.
                    $joinClause.
                    $whereClause.
                    $orderByClause.
                    $limitClause;

                $list_count = 'SELECT FOUND_ROWS() AS `'._DB_PREFIX_.$this->table.'`';
            } else {
                $this->_listsql = 'SELECT '.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').
                    $this->_listsql.
                    $fromClause.
                    $joinClause.
                    $whereClause.
                    $orderByClause.
                    $limitClause;

                $list_count = 'SELECT COUNT(*) AS `'._DB_PREFIX_.$this->table.'` '.
                    $fromClause.
                    $joinClause.
                    $whereClause;
            }

            $this->_list = Db::getInstance()->executeS($this->_listsql, true, false);

            if ($this->_list === false) {
                $this->_list_error = Db::getInstance()->getMsgError();
                break;
            }

            $this->_listTotal = Db::getInstance()->getValue($list_count, false);

            if ($shouldLimitSqlResults) {
                $start = (int)$start - (int)$limit;
                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($this->_list));

        Hook::exec('action'.$this->controller_name.'ListingResultsModifier', array(
            'list' => &$this->_list,
            'list_total' => &$this->_listTotal,
        ));
    }

    /**
     * @return string
     */
    protected function getFromClause()
    {
        $sql_table = $this->table == 'order' ? 'orders' : $this->table;

        return "\n".'FROM `'._DB_PREFIX_.$sql_table.'` a ';
    }

    /**
     * @param $id_lang
     * @param $id_lang_shop
     * @return string
     */
    protected function getJoinClause($id_lang, $id_lang_shop)
    {
        $shopJoinClause = '';
        if ($this->shopLinkType) {
            $shopJoinClause = ' LEFT JOIN `'._DB_PREFIX_.bqSQL($this->shopLinkType).'` shop
                            ON a.`id_'.bqSQL($this->shopLinkType).'` = shop.`id_'.bqSQL($this->shopLinkType).'`';
        }

        return "\n".$this->getLanguageJoinClause($id_lang, $id_lang_shop).
            "\n".(isset($this->_join) ? $this->_join . ' ' : '').
            "\n".$shopJoinClause;
    }

    /**
     * @param $idLang
     * @param $idLangShop
     * @return string
     */
    protected function getLanguageJoinClause($idLang, $idLangShop)
    {
        $languageJoinClause = '';
        if ($this->lang) {
            $languageJoinClause = 'LEFT JOIN `' . _DB_PREFIX_ . bqSQL($this->table). '_lang` b
                ON (b.`' . bqSQL($this->identifier) . '` = a.`' . bqSQL($this->identifier) . '` AND b.`id_lang` = ' . (int)$idLang;

            if ($idLangShop) {
                if (!Shop::isFeatureActive()) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int)Configuration::get('PS_SHOP_DEFAULT');
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int)$idLangShop;
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
            $whereShop = Shop::addSqlRestriction($this->shopShareDatas, 'a', $this->shopLinkType);
        }
        $whereClause = ' WHERE 1 ' . (isset($this->_where) ? $this->_where . ' ' : '') .
            ($this->deleted ? 'AND a.`deleted` = 0 ' : '') .
            (isset($this->_filter) ? $this->_filter : '') . $whereShop . "\n" .
            (isset($this->_group) ? $this->_group . ' ' : '') . "\n" .
            $this->getHavingClause();

        return $whereClause;
    }

    /**
     * @param $orderBy
     * @param $orderDirection
     * @return string
     */
    protected function getOrderByClause($orderBy, $orderDirection)
    {
        $this->_orderBy = $this->checkOrderBy($orderBy);
        $this->_orderWay = $this->checkOrderDirection($orderDirection);

        return ' ORDER BY ' . ((str_replace('`', '', $this->_orderBy) == $this->identifier) ? 'a.' : '') .
            $this->_orderBy . ' ' . $this->_orderWay .
            ($this->_tmpTableFilter ? ') tmpTable WHERE 1' . $this->_tmpTableFilter : '');
    }

    /**
     * @param $orderBy
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

        if (isset($this->fields_list[$orderBy]) && isset($this->fields_list[$orderBy]['order_key'])) {
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
     * @param $orderDirection
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
        return str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));
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
     * @param $limit
     * @return bool
     */
    protected function shouldLimitSqlResults($limit)
    {
        return $limit !== false;
    }

    /**
     * @param $limit
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

        $limit = (int)Tools::getValue($this->list_id.'_pagination', $limit);
        if (in_array($limit, $this->_pagination) && $limit != $this->_default_pagination) {
            $this->context->cookie->{$this->list_id.'_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$this->list_id.'_pagination'});
        }

        if (!is_numeric($limit)) {
            throw new PrestaShopException('Invalid limit. It should be a numeric.');
        }

        return $limit;
    }

    /**
     * @param array|string $filter_modules_list
     * @param string|bool $tracking_source
     * @return bool
     * @throws PrestaShopException
     */
    public function getModulesList($filter_modules_list, $tracking_source = false)
    {
        if (!is_array($filter_modules_list) && !is_null($filter_modules_list)) {
            $filter_modules_list = array($filter_modules_list);
        }

        if (!count($filter_modules_list)) {
            return false;
        } //if there is no modules to display just return false;

        $all_modules = Module::getModulesOnDisk(true);
        $this->modules_list = array();
        foreach ($all_modules as $module) {
            $perm = true;
            if ($module->id) {
                $perm &= Module::getPermissionStatic($module->id, 'configure');
            } else {
                $id_admin_module = Tab::getIdFromClassName('AdminModules');
                $access = Profile::getProfileAccess($this->context->employee->id_profile, $id_admin_module);
                if (!$access['edit']) {
                    $perm &= false;
                }
            }

            if (in_array($module->name, $filter_modules_list) && $perm) {
                $this->fillModuleData($module, 'array', null, $tracking_source);
                $this->modules_list[array_search($module->name, $filter_modules_list)] = $module;
            }
        }
        ksort($this->modules_list);

        if (count($this->modules_list)) {
            return true;
        }

        return false; //no module found on disk just return false;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        $cookie = $this->context->cookie;
        $this->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        if ($this->allow_employee_form_lang && !$cookie->employee_form_lang) {
            $cookie->employee_form_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        $lang_exists = false;
        $this->_languages = Language::getLanguages(false);
        foreach ($this->_languages as $lang) {
            if (isset($cookie->employee_form_lang) && $cookie->employee_form_lang == $lang['id_lang']) {
                $lang_exists = true;
            }
        }

        $this->default_form_language = $lang_exists ? (int)$cookie->employee_form_lang : (int)Configuration::get('PS_LANG_DEFAULT');

        foreach ($this->_languages as $k => $language) {
            $this->_languages[$k]['is_default'] = (int)($language['id_lang'] == $this->default_form_language);
        }

        return $this->_languages;
    }


    /**
     * Return the list of fields value
     *
     * @param ObjectModel $obj Object
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
                                $result = Shop::getShopById((int)$obj->id, $this->identifier, $this->table);
                                foreach ($result as $row) {
                                    $this->fields_value['shop'][$row['id_'.$input['type']]][] = $row['id_shop'];
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
                                $this->fields_value[$input['name']][$language['id_lang']] = $field_value;
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
     * Return field value if possible (both classical and multilingual fields)
     *
     * Case 1 : Return value if present in $_POST / $_GET
     * Case 2 : Return object value
     *
     * @param ObjectModel $obj Object
     * @param string $key Field name
     * @param int|null $id_lang Language id (optional)
     * @return string
     */
    public function getFieldValue($obj, $key, $id_lang = null)
    {
        if ($id_lang) {
            $default_value = (isset($obj->id) && $obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : false;
        } else {
            $default_value = isset($obj->{$key}) ? $obj->{$key} : false;
        }

        return Tools::getValue($key.($id_lang ? '_'.$id_lang : ''), $default_value);
    }

    /**
     * Manage page display (form, list...)
     *
     * @param string|bool $class_name Allow to validate a different class than the current one
     * @throws PrestaShopException
     */
    public function validateRules($class_name = false)
    {
        if (!$class_name) {
            $class_name = $this->className;
        }

        /** @var $object ObjectModel */
        $object = new $class_name();

        if (method_exists($this, 'getValidationRules')) {
            $definition = $this->getValidationRules();
        } else {
            $definition = ObjectModel::getDefinition($class_name);
        }

        $default_language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($definition['fields'] as $field => $def) {
            $skip = array();
            if (in_array($field, array('passwd', 'no-picture'))) {
                $skip = array('required');
            }

            if (isset($def['lang']) && $def['lang']) {
                if (isset($def['required']) && $def['required']) {
                    $value = Tools::getValue($field.'_'.$default_language->id);
                    // !isset => not exist || "" == $value can be === 0 (before, empty $value === 0 returned true)
                    if (!isset($value) || "" == $value) {
                        $this->errors[$field.'_'.$default_language->id] = $this->trans('The field %field_name% is required at least in %lang%.',
                            array('%field_name%' => $object->displayFieldName($field, $class_name), '%lang%' => $default_language->name),
                            'Admin.Notifications.Error');
                    }
                }

                foreach ($languages as $language) {
                    $value = Tools::getValue($field.'_'.$language['id_lang']);
                    if (!empty($value)) {
                        if (($error = $object->validateField($field, $value, $language['id_lang'], $skip, true)) !== true) {
                            $this->errors[$field.'_'.$language['id_lang']] = $error;
                        }
                    }
                }
            } elseif (($error = $object->validateField($field, Tools::getValue($field), null, $skip, true)) !== true) {
                $this->errors[$field] = $error;
            }
        }

        /* Overload this method for custom checking */
        $this->_childValidation();

        /* Checking for multilingual fields validity */
        if (isset($rules['validateLang']) && is_array($rules['validateLang'])) {
            foreach ($rules['validateLang'] as $field_lang => $function) {
                foreach ($languages as $language) {
                    if (($value = Tools::getValue($field_lang.'_'.$language['id_lang'])) !== false && !empty($value)) {
                        if (Tools::strtolower($function) == 'iscleanhtml' && Configuration::get('PS_ALLOW_HTML_IFRAME')) {
                            $res = Validate::$function($value, true);
                        } else {
                            $res = Validate::$function($value);
                        }
                        if (!$res) {
                            $this->errors[$field_lang.'_'.$language['id_lang']] = $this->trans('The %field_name% field (%lang%) is invalid.',
                                array('%field_name%' => call_user_func(array($class_name, 'displayFieldName'), $field_lang, $class_name), '%lang%' => $language['name']),
                                'Admin.Notifications.Error');
                        }
                    }
                }
            }
        }
    }

    /**
     * Overload this method for custom checking
     */
    protected function _childValidation()
    {
    }

    /**
     * Display object details
     */
    public function viewDetails()
    {
    }

    /**
     * Called before deletion
     *
     * @param ObjectModel $object Object
     * @return bool
     */
    protected function beforeDelete($object)
    {
        return false;
    }

    /**
     * Called before deletion
     *
     * @param ObjectModel $object Object
     * @param int $old_id
     * @return bool
     */
    protected function afterDelete($object, $old_id)
    {
        return true;
    }

    /**
     * @param ObjectModel $object
     * @return bool
     */
    protected function afterAdd($object)
    {
        return true;
    }

    /**
     * @param ObjectModel $object
     * @return bool
     */
    protected function afterUpdate($object)
    {
        return true;
    }

    /**
     * Check rights to view the current tab
     *
     * @return bool
     */
    protected function afterImageUpload()
    {
        return true;
    }

    /**
     * Copy data values from $_POST to object
     *
     * @param ObjectModel &$object Object
     * @param string $table Object table
     */
    protected function copyFromPost(&$object, $table)
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $object) && $key != 'id_'.$table) {
                /* Do not take care of password field if empty */
                if ($key == 'passwd' && Tools::getValue('id_'.$table) && empty($value)) {
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
        $fields = array();
        if (isset($class_vars['definition']['fields'])) {
            $fields = $class_vars['definition']['fields'];
        }

        foreach ($fields as $field => $params) {
            if (array_key_exists('lang', $params) && $params['lang']) {
                foreach (Language::getIDs(false) as $id_lang) {
                    if (Tools::isSubmit($field.'_'.(int)$id_lang)) {
                        $object->{$field}[(int)$id_lang] = Tools::getValue($field.'_'.(int)$id_lang);
                    }
                }
            }
        }
    }

    /**
     * Returns an array with selected shops and type (group or boutique shop)
     *
     * @param string $table
     * @return array
     */
    protected function getSelectedAssoShop($table)
    {
        if (!Shop::isFeatureActive() || !Shop::isTableAssociated($table)) {
            return array();
        }

        $shops = Shop::getShops(true, null, true);
        if (count($shops) == 1 && isset($shops[0])) {
            return array($shops[0], 'shop');
        }

        $assos = array();
        if (Tools::isSubmit('checkBoxShopAsso_'.$table)) {
            foreach (Tools::getValue('checkBoxShopAsso_'.$table) as $id_shop => $value) {
                $assos[] = (int)$id_shop;
            }
        } elseif (Shop::getTotalShops(false) == 1) {
            // if we do not have the checkBox multishop, we can have an admin with only one shop and being in multishop
            $assos[] = (int)Shop::getContextShopID();
        }
        return $assos;
    }

    /**
     * Update the associations of shops
     *
     * @param int $id_object
     * @return bool|void
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
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM '._DB_PREFIX_.'shop') as $row) {
            if (!$this->context->employee->hasAuthOnShop($row['id_shop'])) {
                $exclude_ids[] = $row['id_shop'];
            }
        }
        Db::getInstance()->delete($this->table.'_shop', '`'.bqSQL($this->identifier).'` = '.(int)$id_object.($exclude_ids ? ' AND id_shop NOT IN ('.implode(', ', array_map('intval', $exclude_ids)).')' : ''));

        $insert = array();
        foreach ($assos_data as $id_shop) {
            $insert[] = array(
                $this->identifier => (int)$id_object,
                'id_shop' => (int)$id_shop,
            );
        }
        return Db::getInstance()->insert($this->table.'_shop', $insert, false, true, Db::INSERT_IGNORE);
    }

    /**
     * @param mixed $value
     * @param array $field
     * @return bool
     */
    protected function validateField($value, $field)
    {
        if (isset($field['validation'])) {
            $valid_method_exists = method_exists('Validate', $field['validation']);
            if ((!isset($field['empty']) || !$field['empty'] || (isset($field['empty']) && $field['empty'] && $value)) && $valid_method_exists) {
                $field_validation = $field['validation'];
                if (!Validate::$field_validation($value)) {
                    $this->errors[] = $this->trans('%s: Incorrect value', array($field['title']), 'Admin.Notifications.Error');
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Can be overridden
     */
    public function beforeUpdateOptions()
    {
    }

    /**
     * Overload this method for custom checking
     *
     * @param int $id Object id used for deleting images
     * @return bool
     */
    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name']) && isset($this->fieldImageSettings['dir'])) {
            return $this->uploadImage($id, $this->fieldImageSettings['name'], $this->fieldImageSettings['dir'].'/');
        } elseif (!empty($this->fieldImageSettings)) {
            foreach ($this->fieldImageSettings as $image) {
                if (isset($image['name']) && isset($image['dir'])) {
                    $this->uploadImage($id, $image['name'], $image['dir'].'/');
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
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;
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
                $this->errors[] = $this->trans('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings. ', array(), 'Admin.Notifications.Error');
            }

            // Copy new image
            if (empty($this->errors) && !ImageManager::resize($tmp_name, _PS_IMG_DIR_.$dir.$id.'.'.$this->imageType, (int)$width, (int)$height, ($ext ? $ext : $this->imageType))) {
                $this->errors[] = $this->trans('An error occurred while uploading the image.', array(), 'Admin.Notifications.Error');
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
     * Delete multiple items
     *
     * @return bool true if success
     */
    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $object = new $this->className();

            if (isset($object->noZeroObject)) {
                $objects_count = count(call_user_func(array($this->className, $object->noZeroObject)));

                // Check if all object will be deleted
                if ($objects_count <= 1 || count($this->boxes) == $objects_count) {
                    $this->errors[] = $this->trans('You need at least one object.', array(), 'Admin.Notifications.Error').
                        ' <b>'.$this->table.'</b><br />'.
                        $this->trans('You cannot delete all of the items.', array(), 'Admin.Notifications.Error');
                }
            } else {
                $result = true;
                foreach ($this->boxes as $id) {
                    /** @var $to_delete ObjectModel */
                    $to_delete = new $this->className($id);
                    $delete_ok = true;
                    if ($this->deleted) {
                        $to_delete->deleted = 1;
                        if (!$to_delete->update()) {
                            $result = false;
                            $delete_ok = false;
                        }
                    } elseif (!$to_delete->delete()) {
                        $result = false;
                        $delete_ok = false;
                    }

                    if ($delete_ok) {
                        PrestaShopLogger::addLog(sprintf($this->l('%s deletion', 'AdminTab', false, false), $this->className), 1, null, $this->className, (int)$to_delete->id, true, (int)$this->context->employee->id);
                    } else {
                        $this->errors[] = $this->trans('Can\'t delete #%id%', array('%id%' => $id), 'Admin.Notifications.Error');
                    }
                }
                if ($result) {
                    $this->redirect_after = self::$currentIndex.'&conf=2&token='.$this->token;
                }
                $this->errors[] = $this->trans('An error occurred while deleting this selection.', array(), 'Admin.Notifications.Error');
            }
        } else {
            $this->errors[] = $this->trans('You must select at least one element to delete.', array(), 'Admin.Notifications.Error');
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
                <script src='"._PS_JS_DIR_."jquery/jquery-1.11.0.min.js'></script>
                <script src='"._PS_JS_DIR_."admin.js'></script>
                <script src='"._PS_JS_DIR_."tools.js'></script>
                <script>
                    help_class_name='".addslashes($help_class_name)."';
                    iso_user = '".addslashes($this->context->language->iso_code)."'
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
     * Enable multiple items
     *
     * @return bool true if success
     */
    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    /**
     * Disable multiple items
     *
     * @return bool true if success
     */
    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    /**
     * Toggle status of multiple items
     *
     * @param bool $status
     * @return bool true if success
     * @throws PrestaShopException
     */
    protected function processBulkStatusSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                /** @var ObjectModel $object */
                $object = new $this->className((int)$id);
                $object->active = (int)$status;
                $result &= $object->update();
            }
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
            $result = $object->affectZoneToSelection(Tools::getValue($this->table.'Box'), Tools::getValue('zone_to_affect'));

            if ($result) {
                $this->redirect_after = self::$currentIndex.'&conf=28&token='.$this->token;
            }
            $this->errors[] = $this->trans('An error occurred while assigning a zone to the selection.', array(), 'Admin.Notifications.Error');
        } else {
            $this->errors[] = $this->trans('You must select at least one element to assign a new zone.', array(), 'Admin.Notifications.Error');
        }

        return $result;
    }

    /**
     * Called before Add
     *
     * @param ObjectModel $object Object
     * @return bool
     */
    protected function beforeAdd($object)
    {
        return true;
    }

    /**
     * Prepare the view to display the required fields form
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
     * @return Smarty_Internal_Template
     */
    public function createTemplate($tpl_name)
    {
        // Use override tpl if it exists
        // If view access is denied, we want to use the default template that will be used to display an error
        if ($this->viewAccess() && $this->override_folder) {
            if (!Configuration::get('PS_DISABLE_OVERRIDES') && file_exists($this->context->smarty->getTemplateDir(1).DIRECTORY_SEPARATOR.$this->override_folder.$tpl_name)) {
                return $this->context->smarty->createTemplate($this->override_folder.$tpl_name, $this->context->smarty);
            } elseif (file_exists($this->context->smarty->getTemplateDir(0).'controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$tpl_name)) {
                return $this->context->smarty->createTemplate('controllers'.DIRECTORY_SEPARATOR.$this->override_folder.$tpl_name, $this->context->smarty);
            }
        }

        return $this->context->smarty->createTemplate($this->context->smarty->getTemplateDir(0).$tpl_name, $this->context->smarty);
    }

    /**
     * Shortcut to set up a json success payload
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
     * Shortcut to set up a json error payload
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
     * @param string $file
     * @param int $timeout
     * @return bool
     */
    public function isFresh($file, $timeout = 604800)
    {
        if (($time = @filemtime(_PS_ROOT_DIR_.$file)) && filesize(_PS_ROOT_DIR_.$file) > 0) {
            return ((time() - $time) < $timeout);
        }

        return false;
    }

    /** @var bool */
    protected static $is_prestashop_up = true;

    /**
     * @param string $file_to_refresh
     * @param string $external_file
     * @return bool
     */
    public function refresh($file_to_refresh, $external_file)
    {
        if (self::$is_prestashop_up && $content = Tools::file_get_contents($external_file)) {
            return (bool)file_put_contents(_PS_ROOT_DIR_.$file_to_refresh, $content);
        }
        self::$is_prestashop_up = false;
        return false;
    }

    /**
     * @param Module $module
     * @param string $output_type
     * @param string|null $back
     * @param string|bool $install_source_tracking
     */
    public function fillModuleData(&$module, $output_type = 'link', $back = null, $install_source_tracking = false)
    {
        /** @var Module $obj */
        $obj = null;
        if ($module->onclick_option) {
            $obj = new $module->name();
        }
        // Fill module data
        $module->logo = '../../img/questionmark.png';

        if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
            .DIRECTORY_SEPARATOR.'logo.gif')) {
            $module->logo = 'logo.gif';
        }
        if (@filemtime(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).DIRECTORY_SEPARATOR.$module->name
            .DIRECTORY_SEPARATOR.'logo.png')) {
            $module->logo = 'logo.png';
        }

        $link_admin_modules = $this->context->link->getAdminLink('AdminModules', true);

        $module->options['install_url'] = $link_admin_modules.'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name
            .'&anchor='.ucfirst($module->name).($install_source_tracking ? '&source='.$install_source_tracking : '');
        $module->options['update_url'] = $link_admin_modules.'&update='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);
        $module->options['uninstall_url'] = $link_admin_modules.'&uninstall='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);

        // free modules get their source tracking data here
        $module->optionsHtml = $this->displayModuleOptions($module, $output_type, $back, $install_source_tracking);
        // pay modules get their source tracking data here
        if ($install_source_tracking && isset($module->addons_buy_url)) {
            $module->addons_buy_url .= ($install_source_tracking ? '&utm_term='.$install_source_tracking : '');
        }

        $module->options['uninstall_onclick'] = ((!$module->onclick_option) ?
            ((empty($module->confirmUninstall)) ? 'return confirm(\''.$this->l('Do you really want to uninstall this module?').'\');' : 'return confirm(\''.addslashes($module->confirmUninstall).'\');') :
            $obj->onclickOption('uninstall', $module->options['uninstall_url']));

        if ((Tools::getValue('module_name') == $module->name || in_array($module->name, explode('|', Tools::getValue('modules_list')))) && (int)Tools::getValue('conf') > 0) {
            $module->message = $this->_conf[(int)Tools::getValue('conf')];
        }

        if ((Tools::getValue('module_name') == $module->name || in_array($module->name, explode('|', Tools::getValue('modules_list')))) && (int)Tools::getValue('conf') > 0) {
            unset($obj);
        }
    }

    /** @var array */
    protected $translationsTab = array();

    /**
     * Display modules list
     *
     * @param Module $module
     * @param string $output_type (link or select)
     * @param string|null $back
     * @param string|bool $install_source_tracking
     * @return string|array
     */
    public function displayModuleOptions($module, $output_type = 'link', $back = null, $install_source_tracking = false)
    {
        if (!isset($module->enable_device)) {
            $module->enable_device = Context::DEVICE_COMPUTER | Context::DEVICE_TABLET | Context::DEVICE_MOBILE;
        }

        $this->translationsTab['confirm_uninstall_popup'] = (isset($module->confirmUninstall) ? $module->confirmUninstall : $this->l('Do you really want to uninstall this module?'));
        if (!isset($this->translationsTab['Disable this module'])) {
            $this->translationsTab['Disable this module'] = $this->l('Disable this module');
            $this->translationsTab['Enable this module for all shops'] = $this->l('Enable this module for all shops');
            $this->translationsTab['Disable'] = $this->l('Disable');
            $this->translationsTab['Enable'] = $this->l('Enable');
            $this->translationsTab['Disable on mobiles'] = $this->l('Disable on mobiles');
            $this->translationsTab['Disable on tablets'] = $this->l('Disable on tablets');
            $this->translationsTab['Disable on computers'] = $this->l('Disable on computers');
            $this->translationsTab['Display on mobiles'] = $this->l('Display on mobiles');
            $this->translationsTab['Display on tablets'] = $this->l('Display on tablets');
            $this->translationsTab['Display on computers'] = $this->l('Display on computers');
            $this->translationsTab['Reset'] = $this->l('Reset');
            $this->translationsTab['Configure'] = $this->l('Configure');
            $this->translationsTab['Delete'] = $this->l('Delete');
            $this->translationsTab['Install'] = $this->l('Install');
            $this->translationsTab['Uninstall'] = $this->l('Uninstall');
            $this->translationsTab['Would you like to delete the content related to this module ?'] = $this->l('Would you like to delete the content related to this module ?');
            $this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this?'] = $this->l('This action will permanently remove the module from the server. Are you sure you want to do this?');
            $this->translationsTab['Remove from Favorites'] = $this->l('Remove from Favorites');
            $this->translationsTab['Mark as Favorite'] = $this->l('Mark as Favorite');
        }

        $link_admin_modules = $this->context->link->getAdminLink('AdminModules', true);
        $modules_options = array();

        $configure_module = array(
            'href' => $link_admin_modules.'&configure='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.urlencode($module->name),
            'onclick' => $module->onclick_option && isset($module->onclick_option_content['configure']) ? $module->onclick_option_content['configure'] : '',
            'title' => '',
            'text' => $this->translationsTab['Configure'],
            'cond' => $module->id && isset($module->is_configurable) && $module->is_configurable,
            'icon' => 'wrench',
        );

        $desactive_module = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->active ? 'enable=0' : 'enable=1').'&tab_module='.$module->tab,
            'onclick' => $module->active && $module->onclick_option && isset($module->onclick_option_content['desactive']) ? $module->onclick_option_content['desactive'] : '' ,
            'title' => Shop::isFeatureActive() ? htmlspecialchars($module->active ? $this->translationsTab['Disable this module'] : $this->translationsTab['Enable this module for all shops']) : '',
            'text' => $module->active ? $this->translationsTab['Disable'] : $this->translationsTab['Enable'],
            'cond' => $module->id,
            'icon' => 'off',
        );
        $link_reset_module = $link_admin_modules.'&module_name='.urlencode($module->name).'&reset&tab_module='.$module->tab;

        $is_reset_ready = false;
        if (Validate::isModuleName($module->name)) {
            if (method_exists(Module::getInstanceByName($module->name), 'reset')) {
                $is_reset_ready = true;
            }
        }

        $reset_module = array(
            'href' => $link_reset_module,
            'onclick' => $module->onclick_option && isset($module->onclick_option_content['reset']) ? $module->onclick_option_content['reset'] : '',
            'title' => '',
            'text' => $this->translationsTab['Reset'],
            'cond' => $module->id && $module->active,
            'icon' => 'undo',
            'class' => ($is_reset_ready ? 'reset_ready' : '')
        );

        $delete_module = array(
            'href' => $link_admin_modules.'&delete='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.urlencode($module->name),
            'onclick' => $module->onclick_option && isset($module->onclick_option_content['delete']) ? $module->onclick_option_content['delete'] : 'return confirm(\''.$this->translationsTab['This action will permanently remove the module from the server. Are you sure you want to do this?'].'\');',
            'title' => '',
            'text' => $this->translationsTab['Delete'],
            'cond' => true,
            'icon' => 'trash',
            'class' => 'text-danger'
        );

        $display_mobile = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->enable_device & Context::DEVICE_MOBILE ? 'disable_device' : 'enable_device').'='.Context::DEVICE_MOBILE.'&tab_module='.$module->tab,
            'onclick' => '',
            'title' => htmlspecialchars($module->enable_device & Context::DEVICE_MOBILE ? $this->translationsTab['Disable on mobiles'] : $this->translationsTab['Display on mobiles']),
            'text' => $module->enable_device & Context::DEVICE_MOBILE ? $this->translationsTab['Disable on mobiles'] : $this->translationsTab['Display on mobiles'],
            'cond' => $module->id,
            'icon' => 'mobile'
        );

        $display_tablet = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->enable_device & Context::DEVICE_TABLET ? 'disable_device' : 'enable_device').'='.Context::DEVICE_TABLET.'&tab_module='.$module->tab,
            'onclick' => '',
            'title' => htmlspecialchars($module->enable_device & Context::DEVICE_TABLET ? $this->translationsTab['Disable on tablets'] : $this->translationsTab['Display on tablets']),
            'text' => $module->enable_device & Context::DEVICE_TABLET ? $this->translationsTab['Disable on tablets'] : $this->translationsTab['Display on tablets'],
            'cond' => $module->id,
            'icon' => 'tablet'
        );

        $display_computer = array(
            'href' => $link_admin_modules.'&module_name='.urlencode($module->name).'&'.($module->enable_device & Context::DEVICE_COMPUTER ? 'disable_device' : 'enable_device').'='.Context::DEVICE_COMPUTER.'&tab_module='.$module->tab,
            'onclick' => '',
            'title' => htmlspecialchars($module->enable_device & Context::DEVICE_COMPUTER ? $this->translationsTab['Disable on computers'] : $this->translationsTab['Display on computers']),
            'text' => $module->enable_device & Context::DEVICE_COMPUTER ? $this->translationsTab['Disable on computers'] : $this->translationsTab['Display on computers'],
            'cond' => $module->id,
            'icon' => 'desktop'
        );

        $install = array(
            'href' => $link_admin_modules.'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name)
                .(!is_null($back) ? '&back='.urlencode($back) : '').($install_source_tracking ? '&source='.$install_source_tracking : ''),
            'onclick' => '',
            'title' => $this->translationsTab['Install'],
            'text' => $this->translationsTab['Install'],
            'cond' => $module->id,
            'icon' => 'plus-sign-alt'
        );

        $uninstall = array(
            'href' => $link_admin_modules.'&uninstall='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).(!is_null($back) ? '&back='.urlencode($back) : ''),
            'onclick' => (isset($module->onclick_option_content['uninstall']) ? $module->onclick_option_content['uninstall'] : 'return confirm(\''.$this->translationsTab['confirm_uninstall_popup'].'\');'),
            'title' => $this->translationsTab['Uninstall'],
            'text' => $this->translationsTab['Uninstall'],
            'cond' => $module->id,
            'icon' => 'minus-sign-alt'
        );

        $remove_from_favorite = array(
            'href' => '#',
            'class' => 'action_unfavorite toggle_favorite',
            'onclick' =>'',
            'title' => $this->translationsTab['Remove from Favorites'],
            'text' => $this->translationsTab['Remove from Favorites'],
            'cond' => $module->id,
            'icon' => 'star',
            'data-value' => '0',
            'data-module' => $module->name
        );

        $mark_as_favorite = array(
            'href' => '#',
            'class' => 'action_favorite toggle_favorite',
            'onclick' => '',
            'title' => $this->translationsTab['Mark as Favorite'],
            'text' => $this->translationsTab['Mark as Favorite'],
            'cond' => $module->id,
            'icon' => 'star',
            'data-value' => '1',
            'data-module' => $module->name
        );

        $update = array(
            'href' => $module->options['update_url'],
            'onclick' => '',
            'title' => 'Update it!',
            'text' => 'Update it!',
            'icon' => 'refresh',
            'cond' => $module->id,
        );

        $divider = array(
            'href' => '#',
            'onclick' => '',
            'title' => 'divider',
            'text' => 'divider',
            'cond' => $module->id,
        );

        if (isset($module->version_addons) && $module->version_addons) {
            $modules_options[] = $update;
        }

        if ($module->active) {
            $modules_options[] = $configure_module;
            $modules_options[] = $desactive_module;
            $modules_options[] = $display_mobile;
            $modules_options[] = $display_tablet;
            $modules_options[] = $display_computer;
        } else {
            $modules_options[] = $desactive_module;
            $modules_options[] = $configure_module;
        }

        $modules_options[] = $reset_module;

        if ($output_type == 'select') {
            if (!$module->id) {
                $modules_options[] = $install;
            } else {
                $modules_options[] = $uninstall;
            }
        } elseif ($output_type == 'array') {
            if ($module->id) {
                $modules_options[] = $uninstall;
            }
        }

        if (isset($module->preferences) && isset($module->preferences['favorite']) && $module->preferences['favorite'] == 1) {
            $remove_from_favorite['style'] = '';
            $mark_as_favorite['style'] = 'display:none;';
            $modules_options[] = $remove_from_favorite;
            $modules_options[] = $mark_as_favorite;
        } else {
            $mark_as_favorite['style'] = '';
            $remove_from_favorite['style'] = 'display:none;';
            $modules_options[] = $remove_from_favorite;
            $modules_options[] = $mark_as_favorite;
        }

        if ($module->id == 0) {
            $install['cond'] = 1;
            $install['flag_install'] = 1;
            $modules_options[] = $install;
        }
        $modules_options[] = $divider;
        $modules_options[] = $delete_module;

        $return = '';
        foreach ($modules_options as $option_name => $option) {
            if ($option['cond']) {
                if ($output_type == 'link') {
                    $return .= '<li><a class="'.$option_name.' action_module';
                    $return .= '" href="'.$option['href'].(!is_null($back) ? '&back='.urlencode($back) : '').'"';
                    $return .= ' onclick="'.$option['onclick'].'"  title="'.$option['title'].'"><i class="icon-'.(isset($option['icon']) && $option['icon'] ? $option['icon']:'cog').'"></i>&nbsp;'.$option['text'].'</a></li>';
                } elseif ($output_type == 'array') {
                    if (!is_array($return)) {
                        $return = array();
                    }

                    $html = '<a class="';

                    $is_install = isset($option['flag_install']) ? true : false;

                    if (isset($option['class'])) {
                        $html .= $option['class'];
                    }
                    if ($is_install) {
                        $html .= ' btn btn-success';
                    }
                    if (!$is_install && count($return) == 0) {
                        $html .= ' btn btn-default';
                    }

                    $html .= '"';

                    if (isset($option['data-value'])) {
                        $html .= ' data-value="'.$option['data-value'].'"';
                    }

                    if (isset($option['data-module'])) {
                        $html .= ' data-module="'.$option['data-module'].'"';
                    }

                    if (isset($option['style'])) {
                        $html .= ' style="'.$option['style'].'"';
                    }

                    $html .= ' href="'.htmlentities($option['href']).(!is_null($back) ? '&back='.urlencode($back) : '').'" onclick="'.$option['onclick'].'"  title="'.$option['title'].'"><i class="icon-'.(isset($option['icon']) && $option['icon'] ? $option['icon']:'cog').'"></i> '.$option['text'].'</a>';
                    $return[] = $html;
                } elseif ($output_type == 'select') {
                    $return .= '<option id="'.$option_name.'" data-href="'.htmlentities($option['href']).(!is_null($back) ? '&back='.urlencode($back) : '').'" data-onclick="'.$option['onclick'].'">'.$option['text'].'</option>';
                }
            }
        }

        if ($output_type == 'select') {
            $return = '<select id="select_'.$module->name.'">'.$return.'</select>';
        }

        return $return;
    }

    public function ajaxProcessGetModuleQuickView()
    {
        $modules = Module::getModulesOnDisk();

        foreach ($modules as $module) {
            if ($module->name == Tools::getValue('module')) {
                break;
            }
        }

        $url = $module->url;

        if (isset($module->type) && ($module->type == 'addonsPartner' || $module->type == 'addonsNative')) {
            $url = $this->context->link->getAdminLink('AdminModules').'&install='.urlencode($module->name).'&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name);
        }

        $this->context->smarty->assign(array(
            'displayName' => $module->displayName,
            'image' => $module->image,
            'nb_rates' => (int)$module->nb_rates[0],
            'avg_rate' => (int)$module->avg_rate[0],
            'badges' => $module->badges,
            'compatibility' => $module->compatibility,
            'description_full' => $module->description_full,
            'additional_description' => $module->additional_description,
            'is_addons_partner' => (isset($module->type) && ($module->type == 'addonsPartner' || $module->type == 'addonsNative')),
            'url' => $url,
            'price' => $module->price

        ));
        // Fetch the translations in the right place - they are not defined by our current controller!
        Context::getContext()->override_controller_name_for_translations = 'AdminModules';
        $this->smartyOutputContent('controllers/modules/quickview.tpl');
        die(1);
    }

    /**
     * Add an entry to the meta title.
     *
     * @param string $entry New entry.
     */
    public function addMetaTitle($entry)
    {
        // Only add entry if the meta title was not forced.
        if (is_array($this->meta_title)) {
            $this->meta_title[] = $entry;
        }
    }

    /**
     * Set action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Set IdObject
     *
     * @param int $id_object
     */
    public function setIdObject($id_object)
    {
        $this->id_object = (int)$id_object;
    }

    /**
     *
     * @return string
     */
    public function getTabSlug()
    {
        if (empty($this->tabSlug)) {
            $this->tabSlug = Access::findSlugByIdTab($this->id);
        }

        return $this->tabSlug;
    }

    protected function buildContainer()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new LegacyCompilerPass());
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $env = _PS_MODE_DEV_ === true ? 'dev' : 'prod';
        $loader->load(_PS_CONFIG_DIR_.'services/admin/services_'. $env .'.yml');
        $container->compile();

        return $container;
    }

    /**
     * Return the type of authorization on module page.
     *
     * @return int(integer)
     */
    public function authorizationLevel()
    {
        if (
            Access::isGranted(
                'ROLE_MOD_TAB_'.strtoupper($this->controller_name).'_DELETE',
                $this->context->employee->id_profile
            )
        ) {
            return AdminController::LEVEL_DELETE;
        } elseif (
            Access::isGranted(
                'ROLE_MOD_TAB_'.strtoupper($this->controller_name).'_CREATE',
                $this->context->employee->id_profile
            )
        ) {
            return AdminController::LEVEL_ADD;
        } elseif (
            Access::isGranted(
                'ROLE_MOD_TAB_'.strtoupper($this->controller_name).'_UPDATE',
                $this->context->employee->id_profile
            )
        ) {
            return AdminController::LEVEL_EDIT;
        } elseif (
            Access::isGranted(
                'ROLE_MOD_TAB_'.strtoupper($this->controller_name).'_READ',
                $this->context->employee->id_profile
            )
        ) {
            return AdminController::LEVEL_VIEW;
        } else {
            return 0;
        }
    }
}
