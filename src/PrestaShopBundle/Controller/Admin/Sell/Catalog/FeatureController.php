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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Configuration;
use Db;
use DbQuery;
use HelperList;
use Language;
use Media;
use ObjectModel;
use PrestaShopBundle\Component\ActionBar\ActionsBarButtonsCollection;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopException;
use Profile;
use QuickAccess;
use Shop;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tab;
use Tools;
use Validate;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController
{
    public const LAYOUT = 'layout.tpl';
    private const DEFAULT_THEME = 'default';

    public const ACTION_TYPE_BULK = 'bulk';
    public const ACTION_TYPE_ROW = 'row';
    public const ACTION_TYPE_HEADER_TOOLBAR = 'header_toolbar';
    public const ACTION_TYPE_LIST_HEADER_TOOLBAR = 'list_header_toolbar';

    protected string $controllerName;
    public string $php_self;
    protected string $controllerNameLegacy;

    protected string $token;

    private int $id;
    private string $table = 'feature';
    private string $listId = 'feature';
    private string $shopLinkType;
    private array $fieldsList;
    private string $identifier = 'id_feature';
    protected bool $deleted = false;
    protected string $positionIdentifier;

    //Display
    protected bool $displayHeader = true;
    protected bool $displayFooter = true;
    protected bool $displayHeaderJavascript = true;
    protected string $display = 'list';
    protected bool $liteDisplay = false;
    protected bool $bootstrap = true;

    //Toolbar
    protected array $toolbarButton = [];
    protected array $breadcrumbs = [];
    protected array $toolbarTitle = [];
    protected array $pageHeaderToolbarButton = [];
    protected bool $showPageHeaderToolbar = true;
    protected string $pageHeaderToolbarTitle = '';

    //Filters
    protected array $filter = [];
    protected array $filterList = [];

    //Current index
    protected string $currentIndex;

    //Meta
    protected array $metaTitle = [];

    //Media
    protected array $cssFiles = [];
    protected array $jsFiles = [];

    //Template
    protected array $listVariableTemplate = [];
    public array $deleteLinksVariableTemplate = [];
    protected string $folderTemplate;
    public string $template = 'content.tpl';

    //Actions or Template
    /** @var array List of available actions for each list row - default actions are view, edit, delete, duplicate */
    protected array $actionsAvailable = ['view', 'edit', 'duplicate', 'delete'];
    protected array $actions = [];
    protected array $bulkActions;

    //Flash messages
    protected array $errors = [];
    protected array $warnings = [];
    protected array $confirmations = [];
    protected array $informations = [];

    //Handle later
    protected bool $ajax = false;
    protected bool $json = false;

    //SQL section
    protected array $_filter;
    protected string $_filterHaving;
    private string $_orderBy = '';
    private string $_orderWay = '';
    private string $_defaultOrderBy = 'position';
    protected string $_defaultOrderWay = 'ASC';
    protected array $_pagination = [20, 50, 100, 300, 1000];
    protected int $_default_pagination = 50;
    protected array $_list;
    protected string $_listTotal;
    protected string $_list_error;
    protected string $_select;
    protected string $_where;
    protected string $_join;
    /** @var bool Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records */
    protected bool $_useFoundRows = true;

    //Contain the query SQL for list
    protected string $_listsql;

    /** @var bool Do not automatically select * anymore but select only what is necessary */
    protected $explicitSelect = false;

    /** @var bool Automatically join language table if true */
    //default false
    public $isJoinLanguageTableAuto = true;

    private $multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;

    //Variable dans le constructeur à regarder pour filtrer
    public function __construct()
    {
        $this->positionIdentifier = 'id_feature';
        $this->controllerName = get_class($this);
        $this->php_self = $this->controllerName;
        $this->controllerNameLegacy = 'AdminFeatures';
        $this->id = Tab::getIdFromClassName($this->controllerNameLegacy);

        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        }

        if (empty($this->_defaultOrderBy)) {
            $this->_defaultOrderBy = $this->identifier;
        }

        $this->folderTemplate = Tools::toUnderscoreCase(substr($this->controllerName, 5)) . '/';

        $this->setCurrentIndex();

        parent::__construct();
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request)
    {
        //Keep this and in futur version maybe delete this
        $this->getContext()->controller = $this;
        $this->initBreadcrumbs();
        $this->initToken($request);

        //build list and page with action
        $this->setListFields();
        $this->processFilter();
        $this->buildActionList();
        $this->getList($this->getContextLangId());

        return $this->renderSmarty(
            $this->buildList(),
            []
        );
    }

    public function renderSmarty(string $content, array $configuration): Response
    {
        $this->initBreadcrumbs();
        $this->initNotifications();
        $this->initToolbarFlags();
        $this->setMedia();
        $this->initHeader();
        $this->initFooter();

        $this->getContext()->smarty->assign([
            'content' => $content,
        ]);

        return new Response($this->display());
    }

    /**
     * Sets the smarty variables and js defs used to show / hide some notifications.
     */
    public function initNotifications()
    {
        $accesses = Profile::getProfileAccesses($this->getUser()->getData()->id_profile, 'class_name');

        $notificationsSettings = [
            'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS') && isset($accesses['AdminCustomers']) && $accesses['AdminCustomers']['view'] ? '1' : false,
            'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES') && isset($accesses['AdminCustomerThreads']) && $accesses['AdminCustomerThreads']['view'] ? '1' : false,
            'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS') && isset($accesses['AdminOrders']) && $accesses['AdminOrders']['view'] ? '1' : false,
        ];
        $this->getContext()->smarty->assign($notificationsSettings);

        Media::addJsDef($notificationsSettings);
    }

    private function buildActionList()
    {
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature', [
            //Used $this->generateUrl('admin_features_add')
            'href' => $this->currentIndex . '&addfeature&token=' . $this->token,
            'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
            'icon' => 'process-icon-new',
        ]);
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature_value', [
            //Used $this->generateUrl('admin_features_add_value')
            'href' => $this->currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->token,
            'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
            'icon' => 'process-icon-new',
        ]);

        $this->addAction(self::ACTION_TYPE_LIST_HEADER_TOOLBAR, 'new', [
            //Replace by $this->generateUrl('admin_features_add')
            'href' => $this->currentIndex . '&addfeature&token=' . $this->token,
            'desc' => $this->trans('Add new', 'Admin.Actions'),
        ]);

        $this->addAction(self::ACTION_TYPE_ROW, 'view');
        $this->addAction(self::ACTION_TYPE_ROW, 'edit');
        $this->addAction(self::ACTION_TYPE_ROW, 'delete');

        $this->addAction(self::ACTION_TYPE_BULK, 'delete', [
            'text' => $this->trans('Delete selected', 'Admin.Actions'),
            'icon' => 'icon-trash',
            'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
        ]);
    }

    public function addAction(string $type, string $action, array $config = []): void
    {
        switch ($type) {
            case self::ACTION_TYPE_BULK:
                $this->bulkActions[$action] = $config;
                break;

            case self::ACTION_TYPE_ROW:
                $this->actions[] = $action;
                break;

            case self::ACTION_TYPE_HEADER_TOOLBAR:
                $this->pageHeaderToolbarButton[$action] = $config;
                break;

            case self::ACTION_TYPE_LIST_HEADER_TOOLBAR:
                $this->toolbarButton[$action] = $config;
                break;

            default:
                throw new \Exception('This type doesn\'t exist');
        }
    }

    //Todo change name
    public function buildList()
    {
        $this->initList();

        if (!($this->fieldsList && is_array($this->fieldsList))) {
            return false;
        }

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query', 'Admin.Notifications.Error', []) . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->_default_pagination = $this->_default_pagination;
        $helper->_pagination = $this->_pagination;
        $helper->tpl_vars = $this->getTemplateListVars();
        $helper->tpl_delete_link_vars = $this->deleteLinksVariableTemplate;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actionsAvailable as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }

        /* @phpstan-ignore-next-line */
        $helper->sql = $this->_listsql;

        return $helper->generateList($this->_list, $this->fieldsList);
    }

    public function initList(): void
    {
        $this->getContext()->smarty->assign('link', $this->getContext()->link);
        $this->getContext()->smarty->assign('title', 'pedro');
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        if ($this->table == 'feature_value') {
            $this->_where .= ' AND (a.custom = 0 OR a.custom IS NULL)';
        }

        //Dispatch hook not work
        //Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
        //    'select' => &$this->_select,
        //    'join' => &$this->_join,
        //    'where' => &$this->_where,
        //    'group_by' => &$this->_group,
        //    'order_by' => &$this->_orderBy,
        //    'order_way' => &$this->_orderWay,
        //    'fields' => &$this->fieldsList,
        //]);

        if (!isset($this->listId)) {
            $this->listId = $this->table;
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
        if ((int) Tools::getValue('submitFilter' . $this->listId)) {
            $start = ((int) Tools::getValue('submitFilter' . $this->listId) - 1) * $limit;
        } elseif (
            empty($start)
            && isset($this->getContext()->cookie->{$this->listId . '_start'})
            && Tools::isSubmit('export' . $this->table)
        ) {
            $start = $this->getContext()->cookie->{$this->listId . '_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->getContext()->cookie->{$this->listId . '_start'} = $start;
        } elseif (isset($this->getContext()->cookie->{$this->listId . '_start'})) {
            unset($this->getContext()->cookie->{$this->listId . '_start'});
        }

        // Add SQL shop restriction
        $select_shop = '';
        if ($this->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($this->multishop_context && Shop::isTableAssociated($this->table) && !empty($this->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->getUser()->getData()->isSuperAdmin()) {
                $test_join = !preg_match('#`?' . preg_quote(_DB_PREFIX_ . $this->table . '_shop') . '`? *sa#', $this->_join);
                if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($this->table)) {
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
                foreach ($this->fieldsList as $key => $array_value) {
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
                $this->_listsql .= ($this->isJoinLanguageTableAuto ? 'b.*,' : '') . ' a.*';
            }

            $this->_listsql .= "\n" . (isset($this->_select) ? ', ' . rtrim($this->_select, ', ') : '') . $select_shop;

            $limitClause = ' ' . (($shouldLimitSqlResults) ? ' LIMIT ' . (int) $start . ', ' . (int) $limit : '');

            if ($this->_useFoundRows || isset($this->_filterHaving) || isset($this->_having)) {
                //$this->_listsql = 'SELECT SQL_CALC_FOUND_ROWS ' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                $this->_listsql = 'SELECT SQL_CALC_FOUND_ROWS ' .
                    $this->_listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $this->table . '`';
            } else {
                //$this->_listsql = 'SELECT ' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                $this->_listsql = 'SELECT ' .
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

            $this->_listTotal = Db::getInstance()->getValue($list_count, false);

            if ($shouldLimitSqlResults) {
                $start = (int) $start - (int) $limit;
                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($this->_list));

        if ($this->table == 'feature') {
            $nb_items = count($this->_list);
            for ($i = 0; $i < $nb_items; ++$i) {
                $item = &$this->_list[$i];

                $query = new DbQuery();
                $query->select('COUNT(fv.id_feature_value) as count_values');
                $query->from('feature_value', 'fv');
                $query->where('fv.id_feature =' . (int) $item['id_feature']);
                $query->where('(fv.custom=0 OR fv.custom IS NULL)');
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
                $item['value'] = (int) $res;
                unset($query);
            }
        }

        //Hook::exec('action' . $this->controller_name . 'ListingResultsModifier', [
        //    'list' => &$this->_list,
        //    'list_total' => &$this->_listTotal,
        //]);
    }

    /**
     * Assign smarty variables for the header.
     */
    public function initHeader()
    {
        header('Cache-Control: no-store, no-cache');

        $this->getContext()->smarty->assign([
            'table' => $this->table,
            'current' => $this->generateUrl('admin_features_index'),
            //'token' => $this->token,
            'host_mode' => (int) defined('_PS_HOST_MODE_'),
            'stock_management' => (int) Configuration::get('PS_STOCK_MANAGEMENT'),
            'no_order_tip' => $this->getNotificationTip('order'),
            'no_customer_tip' => $this->getNotificationTip('customer'),
            'no_customer_message_tip' => $this->getNotificationTip('customer_message'),
        ]);

        if ($this->displayHeader) {
            $this->getContext()->smarty->assign(
                'displayBackOfficeHeader',
                \Hook::exec('displayBackOfficeHeader')
            );
        }

        // Fetch Employee Menu
        $menuLinksCollections = new ActionsBarButtonsCollection();
        //Hook::exec(
        //    'displayBackOfficeEmployeeMenu',
        //    [
        //        'links' => $menuLinksCollections,
        //    ],
        //    null,
        //    true
        //);
        //
        $this->getContext()->smarty->assign([
            //'displayBackOfficeTop' => Hook::exec('displayBackOfficeTop'),
            //@Todo handle this
            'displayBackOfficeEmployeeMenu' => $menuLinksCollections,
            //'submit_form_ajax' => (int) Tools::getValue('submitFormAjax'),
        ]);
        //
        //// Multishop
        //$is_multishop = Shop::isFeatureActive();

        $tabs = $this->getTabs();
        $currentTabLevel = 0;
        foreach ($tabs as $tab) {
            $currentTabLevel = isset($tab['current_level']) ? $tab['current_level'] : $currentTabLevel;
        }
        //
        //    $helperShop = new HelperShop();
        //    /* Hooks are voluntary out the initialize array (need those variables already assigned) */
        $this->getContext()->smarty->assign([
            'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
            'collapse_menu' => isset($this->getContext()->cookie->collapse_menu) ? (int) $this->getContext()->cookie->collapse_menu : 0,
            //'current_shop_name' => $helperShop->getCurrentShopName(),
            'default_tab_link' => $this->getContext()->link->getAdminLink(Tab::getClassNameById((int) $this->getUser()->getData()->default_tab)),
            'employee' => $this->getUser()->getData(),
            //'help_box' => Configuration::get('PS_HELPBOX'),
            //'is_multishop' => $is_multishop,
            'login_link' => $this->getContext()->link->getAdminLink('AdminLogin'),
            'logout_link' => $this->getContext()->link->getAdminLink('AdminLogin', true, [], ['logout' => 1]),
            //'multi_shop' => Shop::isFeatureActive(),
            //'multishop_context' => $this->multishop_context,
            'quick_access' => QuickAccess::getQuickAccessesWithToken($this->getContextLangId(), (int) $this->getUser()->getData()->id),
            'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
            //'search_type' => Tools::getValue('bo_search_type'),
            //'shop' => $this->context->shop,
            //'shop_group' => new ShopGroup((int) Shop::getContextShopGroupID()),
            //'shop_list' => $helperShop->getRenderedShopList(),
        ]);
        //} else {
        //    $this->getContext()->smarty->assign('default_tab_link', $this->getContext()->link->getAdminLink('AdminDashboard'));
        //}
        //
        //// Shop::initialize() in config.php may empty $this->context->shop->virtual_uri so using a new shop instance for getBaseUrl()
        //$this->context->shop = new Shop((int) $this->context->shop->id);

        $this->getContext()->smarty->assign([
            'base_url' => $this->getContext()->shop->getBaseURL(true),
            'bootstrap' => $this->bootstrap,
            //'class_name' => $this->className,
            'controller_name' => $this->controllerName,
            'country_iso_code' => $this->getContext()->country->iso_code,
            'currentIndex' => $this->generateUrl('admin_features_index'),
            //'current_parent_id' => (int) Tab::getCurrentParentId(),
            'current_tab_level' => $currentTabLevel,
            'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'full_language_code' => $this->getContext()->language->language_code,
            'full_cldr_language_code' => $this->getContextLocale()->getCode(),
            'img_dir' => _PS_IMG_,
            'install_dir_exists' => file_exists(_PS_ADMIN_DIR_ . '/../install'),
            'iso' => $this->getContext()->language->iso_code,
            'iso_user' => $this->getContext()->language->iso_code,
            'lang_is_rtl' => $this->getContext()->language->is_rtl,
            //'lang_iso' => $this->context->language->iso_code,
            'link' => $this->getContext()->link,
            //'pic_dir' => _THEME_PROD_PIC_DIR_,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            //@Todo handle this later
            'tabs' => $tabs,
            'version' => _PS_VERSION_,
        ]);
    }

    public function initFooter()
    {
        //RTL Support
        //rtl.js overrides inline styles
        //iso_code.css overrides default fonts for every language (optional)

        // We assign js and css files on the last step before display template, because controller can add many js and css files
        $this->getContext()->smarty->assign('css_files', $this->cssFiles);
        $this->getContext()->smarty->assign('js_files', array_unique($this->jsFiles));

        $this->getContext()->smarty->assign([
            'ps_version' => _PS_VERSION_,
            'iso_is_fr' => strtoupper($this->getContext()->language->iso_code) == 'FR',
            //'modals' => $this->renderModal(),
        ]);
    }

    /**
     * This function sets various display options for helper list.
     *
     //* @param HelperList|HelperView|HelperOptions $helper
     */
    public function setHelperDisplay($helper)
    {
        //if (empty($this->breadcrumbs)) {
        //    $this->initBreadcrumbs();
        //}

        if (empty($this->toolbarTitle)) {
            $this->initToolbarTitle();
        }
        //// tocheck
        //if ($this->object && $this->object->id) {
        //    $helper->id = $this->object->id;
        //}
        //
        //// @todo : move that in Helper
        /// Sortons ça d'ici et mettons le en dur => Pas pas mettre en dur car traduit depuis la BDD
        $helper->title = is_array($this->toolbarTitle) ? implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $this->toolbarTitle) : $this->toolbarTitle;
        $helper->toolbar_btn = $this->toolbarButton;
        //$helper->show_toolbar = $this->show_toolbar;
        $helper->show_toolbar = true;
        //$helper->toolbar_scroll = $this->toolbar_scroll;
        //$helper->override_folder = $this->tpl_folder;
        $helper->actions = $this->actions;
        //$helper->simple_header = $this->list_simple_header;
        $helper->bulk_actions = $this->bulkActions;
        $helper->currentIndex = $this->generateUrl('admin_features_index');
        //Useless for list
        //if (isset($helper->className)) {
        //    $helper->className = $this->className;
        //}
        $helper->table = $this->table;
        if (isset($helper->name_controller)) {
            $helper->name_controller = $this->controllerName;
        }
        //Useless for list
        $helper->orderBy = $this->_orderBy;
        //Useless for list
        //$helper->orderWay = $this->_orderWay;
        $helper->listTotal = $this->_listTotal;
        //if (isset($helper->shopLink)) {
        //    $helper->shopLink = $this->shopLink;
        //}
        //$helper->shopLinkType = $this->shopLinkType;
        $helper->identifier = $this->identifier;
        //$helper->token = $this->token;
        //// @phpstan-ignore-next-line
        //$helper->languages = $this->_languages;
        //$helper->specificConfirmDelete = $this->specificConfirmDelete;
        //$helper->imageType = $this->imageType;
        //$helper->no_link = $this->list_no_link;
        //$helper->colorOnBackground = $this->colorOnBackground;
        //$helper->ajax_params = isset($this->ajax_params) ? $this->ajax_params : null;
        //// @phpstan-ignore-next-line
        //$helper->default_form_language = $this->default_form_language;
        //if (isset($helper->allow_employee_form_lang)) {
        //    $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        //}
        //if (isset($helper->multiple_fieldsets)) {
        //    $helper->multiple_fieldsets = $this->multiple_fieldsets;
        //}
        //$helper->row_hover = $this->row_hover;
        $helper->position_identifier = $this->positionIdentifier;
        //if (isset($helper->position_group_identifier)) {
        //    $helper->position_group_identifier = $this->position_group_identifier;
        //}
        //// @phpstan-ignore-next-line
        $helper->controller_name = $this->controllerName;
        $helper->list_id = $this->listId ?? $this->table;
        $helper->bootstrap = $this->bootstrap;
        //
        //// For each action, try to add the corresponding skip elements list
        //$helper->list_skip_actions = $this->list_skip_actions;
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
                isset($this->getContext()->cookie->{$this->listId . '_pagination'}) &&
                $this->getContext()->cookie->{$this->listId . '_pagination'}
            ) {
                $limit = $this->getContext()->cookie->{$this->listId . '_pagination'};
            } else {
                $limit = $this->_default_pagination;
            }
        }

        $limit = (int) Tools::getValue($this->listId . '_pagination', $limit);
        if (in_array($limit, $this->_pagination) && $limit != $this->_default_pagination) {
            $this->getContext()->cookie->{$this->listId . '_pagination'} = $limit;
        } else {
            unset($this->getContext()->cookie->{$this->listId . '_pagination'});
        }

        if (!is_numeric($limit)) {
            throw new PrestaShopException('Invalid limit. It should be a numeric.');
        }

        return $limit;
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
        if ($this->isJoinLanguageTableAuto) {
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
    protected function getWhereClause(): string
    {
        $whereShop = '';
        if ($this->shopLinkType) {
            //$whereShop = Shop::addSqlRestriction($this->shopShareDatas, 'a');
            $whereShop = Shop::addSqlRestriction(false, 'a');
        }

        return ' WHERE 1 ' . (isset($this->_where) ? $this->_where . ' ' : '') .
            ($this->deleted ? 'AND a.`deleted` = 0 ' : '') .
            (isset($this->_filter) ? $this->_filter : '') . $whereShop . "\n" .
            (isset($this->_group) ? $this->_group . ' ' : '') . "\n" .
            $this->getHavingClause();
    }

    /**
     * @return string
     */
    protected function getHavingClause(): string
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
            . $this->_orderWay;
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

            if ($this->getContext()->cookie->{$prefix . $this->listId . 'Orderby'}) {
                $orderBy = $this->getContext()->cookie->{$prefix . $this->listId . 'Orderby'};
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

        if (!isset($this->fieldsList[$orderBy]['order_key']) && isset($this->fieldsList[$orderBy]['filter_key'])) {
            $this->fieldsList[$orderBy]['order_key'] = $this->fieldsList[$orderBy]['filter_key'];
        }

        if (isset($this->fieldsList[$orderBy]['order_key'])) {
            $orderBy = $this->fieldsList[$orderBy]['order_key'];
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
            if ($this->getContext()->cookie->{$prefix . $this->listId . 'Orderway'}) {
                $orderDirection = $this->getContext()->cookie->{$prefix . $this->listId . 'Orderway'};
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
     * Set the filters used for the list display.
     */
    protected function getCookieFilterPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }

    protected function shouldLimitSqlResults($limit): bool
    {
        return $limit !== false;
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

    public function getTemplateListVars()
    {
        return $this->listVariableTemplate;
    }

    private function setListFields(): void
    {
        $this->fieldsList = [
            'id_feature' => [
                'title' => $this->trans('ID', 'Admin.Global', []),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', 'Admin.Global', []),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ],
            'value' => [
                'title' => $this->trans('Values', 'Admin.Global', []),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->trans('Position', 'Admin.Global', []),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ],
        ];
    }

    public function setCurrentIndex(): void
    {
        // Set current index
        $currentIndex = 'index.php' . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');
        if ($back = Tools::getValue('back')) {
            $currentIndex .= '&back=' . urlencode($back);
        }

        $this->currentIndex = $currentIndex;
    }

    public function display(): string
    {
        $this->getContext()->smarty->assign([
            'display_header' => $this->displayHeader,
            'display_header_javascript' => $this->displayHeaderJavascript,
            'display_footer' => $this->displayFooter,
            'js_def' => Media::getJsDef(),
            'toggle_navigation_url' => $this->getContext()->link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
        ]);

        // Use page title from metaTitle if it has been set else from the breadcrumbs array
        //Todo
        if (!$this->metaTitle) {
            $this->metaTitle = $this->toolbarTitle;
        }
        $this->getContext()->smarty->assign(
            'meta_title',
            strip_tags(implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $this->metaTitle))
        );

        $template_dirs = $this->getContext()->smarty->getTemplateDir() ?: [];

        // Check if header/footer have been overridden
        $dir = $this->getContext()->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . trim($this->folderTemplate, '\\/') . DIRECTORY_SEPARATOR;
        $module_list_dir = $this->getContext()->smarty->getTemplateDir(0) . 'helpers' . DIRECTORY_SEPARATOR . 'modules_list' . DIRECTORY_SEPARATOR;

        $header_tpl = file_exists($dir . 'header.tpl') ? $dir . 'header.tpl' : 'header.tpl';
        $page_header_toolbar = file_exists($dir . 'page_header_toolbar.tpl') ? $dir . 'page_header_toolbar.tpl' : 'page_header_toolbar.tpl';
        $footer_tpl = file_exists($dir . 'footer.tpl') ? $dir . 'footer.tpl' : 'footer.tpl';
        $modal_module_list = file_exists($module_list_dir . 'modal.tpl') ? $module_list_dir . 'modal.tpl' : '';
        //@Todo later handle different view now just handle list
        $tpl_action = $this->folderTemplate . $this->display . '.tpl';

        // Check if action template has been overridden
        foreach ($template_dirs as $template_dir) {
            if (file_exists($template_dir . DIRECTORY_SEPARATOR . $tpl_action) && $this->display != 'view' && $this->display != 'options') {
                if (method_exists($this, $this->display . Tools::toCamelCase($this->controllerName))) {
                    $this->{$this->display . Tools::toCamelCase($this->controllerName)}();
                }
                $this->getContext()->smarty->assign('content', $this->getContext()->smarty->fetch($tpl_action));

                break;
            }
        }

        //if (!$this->ajax) {
            $template = $this->createTemplate($this->template);
            $page = $template->fetch();
        //} else {
        //    $page = $this->content;
        //}

        //@Todo Handle later
        //if ($conf = Tools::getValue('conf')) {
        //    $this->getContext()->smarty->assign('conf', $this->json ? json_encode($this->_conf[(int) $conf]) : $this->_conf[(int) $conf]);
        //}

        //@Todo Handle later
        //if ($error = Tools::getValue('error')) {
        //    $this->getContext()->smarty->assign('error', $this->json ? json_encode($this->_error[(int) $error]) : $this->_error[(int) $error]);
        //}

        foreach (['errors', 'warnings', 'informations', 'confirmations'] as $type) {
            if (!is_array($this->$type)) {
                $this->$type = (array) $this->$type;
            }
            $this->getContext()->smarty->assign($type, $this->json ? json_encode(array_unique($this->$type)) : array_unique($this->$type));
        }

        if ($this->showPageHeaderToolbar && !$this->liteDisplay) {
            $this->getContext()->smarty->assign(
                [
                    'page_header_toolbar' => $this->getContext()->smarty->fetch($page_header_toolbar),
                ]
            );
            if (!empty($modal_module_list)) {
                $this->getContext()->smarty->assign(
                    [
                        'modal_module_list' => $this->getContext()->smarty->fetch($modal_module_list),
                    ]
                );
            }
        }

        $this->getContext()->smarty->assign('baseAdminUrl', __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/');

        $this->getContext()->smarty->assign(
            [
                'page' => $this->json ? json_encode($page) : $page,
                'header' => $this->getContext()->smarty->fetch($header_tpl),
                'footer' => $this->getContext()->smarty->fetch($footer_tpl),
            ]
        );

        return $this->smartyOutputContent();
    }

    /**
     * Create a template from the override file, else from the base file.
     *
     * @param string $templateName filename
     *
     * @return \Smarty_Internal_Template
     */
    public function createTemplate($templateName)
    {
        if ($this->folderTemplate) {
            if (!Configuration::get('PS_DISABLE_OVERRIDES') && file_exists($this->getContext()->smarty->getTemplateDir(1) . DIRECTORY_SEPARATOR . $this->folderTemplate . $templateName)) {
                return $this->getContext()->smarty->createTemplate($this->folderTemplate . $templateName, $this->getContext()->smarty);
            } elseif (file_exists($this->getContext()->smarty->getTemplateDir(0) . 'controllers' . DIRECTORY_SEPARATOR . $this->folderTemplate . $templateName)) {
                return $this->getContext()->smarty->createTemplate('controllers' . DIRECTORY_SEPARATOR . $this->folderTemplate . $templateName, $this->getContext()->smarty);
            }
        }

        return $this->getContext()->smarty->createTemplate($this->getContext()->smarty->getTemplateDir(0) . $templateName, $this->getContext()->smarty);
    }

    /**
     * Renders controller templates and generates page content.
     *
     * @param array|string $templates Template file(s) to be rendered
     */
    protected function smartyOutputContent($templates = self::LAYOUT): string
    {
        $this->getContext()->cookie->write();

        $js_tag = 'js_def';
        $this->getContext()->smarty->assign($js_tag, $js_tag);

        if (!is_array($templates)) {
            $templates = [$templates];
        }

        $html = '';
        foreach ($templates as $template) {
            $html .= $this->getContext()->smarty->fetch($template);
        }

        return trim($html);
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        $adminWebpath = '';

        if (defined('_PS_ADMIN_DIR_')) {
            $adminWebpath = preg_replace(
                '/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '',
                str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_)
            );
        }

        if ($this->getContext()->language->is_rtl) {
            $this->addJS(_PS_JS_DIR_ . 'rtl.js');
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/' . $this->getContext()->language->iso_code . '.css');
        }

        if ($isNewTheme) {
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/theme.css', 'all', 1);
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/main.bundle.js');

            // the multistore dropdown should be called only once, and only if multistore is used
            if ($this->container->get('prestashop.adapter.multistore_feature')->isUsed()) {
                $this->addJs(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/multistore_dropdown.bundle.js');
            }
            $this->addJqueryPlugin(['chosen', 'fancybox']);
        } else {
            //Bootstrap
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/theme.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/vendor/titatoggle-min.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/public/theme.css', 'all', 0);

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

            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/vendor/bootstrap.min.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/vendor/modernizr.min.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/modernizr-loads.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/vendor/moment-with-langs.min.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/public/bundle.js');

            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

            if (!$this->liteDisplay) {
                $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/help.js');
            }

            if (!Tools::getValue('submitFormAjax')) {
                $this->addJS(_PS_JS_DIR_ . 'admin/notifications.js');
            }

            // Specific Admin Theme
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/overrides.css', 'all', PHP_INT_MAX);
        }

        $this->addJS([
            _PS_JS_DIR_ . 'admin.js?v=' . _PS_VERSION_, // TODO: SEE IF REMOVABLE
            __PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/cldr.bundle.js',
            _PS_JS_DIR_ . 'tools.js?v=' . _PS_VERSION_,
            __PS_BASE_URI__ . $adminWebpath . '/public/bundle.js',
        ]);

        Media::addJsDef([
            'changeFormLanguageUrl' => $this->getContext()->link->getAdminLink(
                'AdminEmployees',
                true,
                [],
                ['action' => 'formLanguage']
            ),
        ]);
        Media::addJsDef(['host_mode' => (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_)]);
        Media::addJsDef(['baseDir' => __PS_BASE_URI__]);
        Media::addJsDef(['baseAdminDir' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/']);
        Media::addJsDef(['currency' => [
            'iso_code' => $this->getContext()->currency->iso_code,
            'sign' => $this->getContext()->currency->sign,
            'name' => $this->getContext()->currency->name,
            'format' => $this->getContext()->currency->format,
        ]]);
        //Todo Handle this later
        //Media::addJsDef(
        //    [
        //        'currency_specifications' => $this->preparePriceSpecifications($this->getContext()),
        //        'number_specifications' => $this->prepareNumberSpecifications($this->getContext()),
        //    ]
        //);

        Media::addJsDef([
            'prestashop' => [
                'debug' => _PS_MODE_DEV_,
            ],
        ]);

        // Execute Hook AdminController SetMedia
        $this->dispatchHook('actionAdminControllerSetMedia', []);
    }

    /**
     * Adds a new stylesheet(s) to the page header.
     *
     * @param string|array $cssUri Path to CSS file, or list of css files like this : array(array(uri => media_type), ...)
     * @param string $cssMediaType
     * @param int|null $offset
     * @param bool $checkPath
     *
     * @return void
     */
    public function addCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true)
    {
        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }

        foreach ($cssUri as $cssFile => $media) {
            if (is_string($cssFile) && strlen($cssFile) > 1) {
                if ($checkPath) {
                    $css_path = Media::getCSSPath($cssFile, $media);
                } else {
                    $css_path = [$cssFile => $media];
                }
            } else {
                if ($checkPath) {
                    $css_path = Media::getCSSPath($media, $cssMediaType);
                } else {
                    $css_path = [$media => $cssMediaType];
                }
            }

            $key = is_array($css_path) ? key($css_path) : $css_path;
            if ($css_path && (!isset($this->cssFiles[$key]) || ($this->cssFiles[$key] != reset($css_path)))) {
                $size = count($this->cssFiles);
                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->cssFiles = array_merge(array_slice($this->cssFiles, 0, $offset), $css_path, array_slice($this->cssFiles, $offset));
            }
        }
    }

    /**
     * Adds a new JavaScript file(s) to the page header.
     *
     * @param string|array $jsUri Path to JS file or an array like: array(uri, ...)
     * @param bool $checkPath
     */
    public function addJS($jsUri, $checkPath = true)
    {
        if (!is_array($jsUri)) {
            $jsUri = [$jsUri];
        }

        foreach ($jsUri as $jsFile) {
            $jsFile = explode('?', $jsFile);
            $version = '';
            if (isset($jsFile[1]) && $jsFile[1]) {
                $version = $jsFile[1];
            }
            $js_path = $jsFile = $jsFile[0];
            if ($checkPath) {
                $js_path = Media::getJSPath($jsFile);
            }

            if ($js_path && !in_array($js_path, $this->jsFiles)) {
                $this->jsFiles[] = $js_path . ($version ? '?' . $version : '');
            }
        }
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list.
     *
     * @param string|array $name
     * @param string|null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true)
    {
        if (!is_array($name)) {
            $name = [$name];
        }

        foreach ($name as $plugin) {
            $plugin_path = Media::getJqueryPluginPath($plugin, $folder);

            if (!empty($plugin_path['js'])) {
                $this->addJS($plugin_path['js'], false);
            }
            if ($css && !empty($plugin_path['css'])) {
                $this->addCSS(key($plugin_path['css']), 'all', null, false);
            }
        }
    }

    /**
     * Adds jQuery UI component(s) to queued JS file list.
     *
     * @param string|array $component
     * @param string $theme
     * @param bool $check_dependencies
     */
    public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
    {
        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $ui_path = Media::getJqueryUIPath($ui, $theme, $check_dependencies);
            $this->addCSS($ui_path['css'], 'all');
            $this->addJS($ui_path['js'], false);
        }
    }

    /**
     * Sets the smarty variables and js defs used to show / hide some notifications.
     */
    public function initToken(Request $request)
    {
        $this->token = $request->query->get('_token');
    }

    /**
     * Set breadcrumbs array for the controller page.
     *
     * @param array $tabs
     */
    public function initBreadcrumbs(array $tabs = [])
    {
        if (!is_array($tabs)) {
            $tabs = [];
        }
        $tabId = $this->id;
        $tabs = Tab::recursiveTab($tabId, $tabs);

        $dummy = ['name' => '', 'href' => '', 'icon' => ''];
        $breadcrumbs2 = [
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy,
        ];

        if (!empty($tabs[0])) {
            $this->addMetaTitle($tabs[0]['name']);
            $breadcrumbs2['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs2['tab']['href'] = $this->getContext()->link->getTabLink($tabs[0]);
            if (!isset($tabs[1])) {
                $breadcrumbs2['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (!empty($tabs[1])) {
            $breadcrumbs2['container']['name'] = $tabs[1]['name'];
            $breadcrumbs2['container']['href'] = $this->getContext()->link->getTabLink($tabs[1]);
            $breadcrumbs2['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

        /* content, edit, list, add, details, options, view */
        //switch ($this->display) {
        //    case 'add':
        //        $breadcrumbs2['action']['name'] = $this->trans('Add', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-plus';
        //
        //        break;
        //    case 'edit':
        //        $breadcrumbs2['action']['name'] = $this->trans('Edit', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-pencil';
        //
        //        break;
        //    case '':
        //    case 'list':
        //        $breadcrumbs2['action']['name'] = $this->trans('List', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-th-list';
        //
        //        break;
        //    case 'details':
        //    case 'view':
        //        $breadcrumbs2['action']['name'] = $this->trans('View details', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-zoom-in';
        //
        //        break;
        //    case 'options':
        //        $breadcrumbs2['action']['name'] = $this->trans('Options', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-cogs';
        //
        //        break;
        //    case 'generator':
        //        $breadcrumbs2['action']['name'] = $this->trans('Generator', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-flask';
        //
        //        break;
        //}

        $this->getContext()->smarty->assign([
            'breadcrumbs2' => $breadcrumbs2,
            'quick_access_current_link_name' => Tools::safeOutput($breadcrumbs2['tab']['name'] . (isset($breadcrumbs2['action']) ? ' - ' . $breadcrumbs2['action']['name'] : '')),
            'quick_access_current_link_icon' => $breadcrumbs2['container']['icon'],
        ]);

        $this->breadcrumbs[] = $tabs[0]['name'] ?? '';
        $navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $this->getContext()->smarty->assign('navigationPipe', $navigationPipe);
    }

    /**
     * Add an entry to the meta title.
     *
     * @param string $entry new entry
     */
    public function addMetaTitle($entry)
    {
        // Only add entry if the meta title was not forced.
        if (is_array($this->metaTitle)) {
            $this->metaTitle[] = $entry;
        }
    }

    public function initToolbarFlags()
    {
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $this->getContext()->smarty->assign([
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
            'debug_mode' => (bool) _PS_MODE_DEV_,
            'lite_display' => $this->liteDisplay,
            'url_post' => $this->generateUrl('admin_features_index') . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->showPageHeaderToolbar,
            'page_header_toolbar_title' => $this->pageHeaderToolbarTitle,
            'title' => $this->pageHeaderToolbarTitle,
            'toolbar_btn' => $this->pageHeaderToolbarButton,
            'page_header_toolbar_btn' => $this->pageHeaderToolbarButton,
        ]);
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
                $this->toolbarButton['save'] = [
                    'href' => '#',
                    'desc' => $this->trans('Save', 'Admin.Actions'),
                ];
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = $this->generateUrl('admin_features_index');
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->liteDisplay) {
                    $this->toolbarButton['cancel'] = [
                        'href' => $back,
                        'desc' => $this->trans('Cancel', 'Admin.Actions'),
                    ];
                }

                break;
            case 'view':
                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = $this->generateUrl('admin_features_index');
                }
                if (!Validate::isCleanHtml($back)) {
                    die(Tools::displayError());
                }
                if (!$this->liteDisplay) {
                    $this->toolbarButton['back'] = [
                        'href' => $back,
                        'desc' => $this->trans('Back to list', 'Admin.Actions'),
                    ];
                }

                break;
            case 'options':
                $this->toolbarButton['save'] = [
                    'href' => '#',
                    'desc' => $this->trans('Save', 'Admin.Actions'),
                ];

                break;
            default:
                // list
                //if ($this->allow_export) {
                //    $this->toolbar_btn['export'] = [
                //        'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
                //        'desc' => $this->trans('Export', 'Admin.Actions'),
                //    ];
                //}
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->toolbarTitle)) {
            $this->initToolbarTitle();
        }

        //Todo pas encore fait l'edition
        //switch ($this->display) {
        //    case 'view':
        //        // Default cancel button - like old back link
        //        $back = Tools::safeOutput(Tools::getValue('back', ''));
        //        if (empty($back)) {
        //            $back = self::$currentIndex . '&token=' . $this->token;
        //        }
        //        if (!Validate::isCleanHtml($back)) {
        //            die(Tools::displayError());
        //        }
        //        if (!$this->lite_display) {
        //            $this->page_header_toolbar_btn['back'] = [
        //                'href' => $back,
        //                'desc' => $this->trans('Back to list'),
        //            ];
        //        }
        //        $obj = $this->loadObject(true);
        //        if (Validate::isLoadedObject($obj) && isset($obj->{$this->identifier_name}) && !empty($obj->{$this->identifier_name})) {
        //            array_pop($this->toolbarTitle);
        //            array_pop($this->meta_title);
        //            $this->toolbarTitle[] = is_array($obj->{$this->identifier_name}) ? $obj->{$this->identifier_name}[$this->context->employee->id_lang] : $obj->{$this->identifier_name};
        //            $this->addMetaTitle($this->toolbarTitle[count($this->toolbarTitle) - 1]);
        //        }
        //
        //        break;
        //    case 'edit':
        //        $obj = $this->loadObject(true);
        //        if (Validate::isLoadedObject($obj) && isset($obj->{$this->identifier_name}) && !empty($obj->{$this->identifier_name})) {
        //            array_pop($this->toolbarTitle);
        //            array_pop($this->meta_title);
        //            $this->toolbarTitle[] = $this->trans(
        //                'Edit: %s',
        //                [
        //                    (is_array($obj->{$this->identifier_name})
        //                        && isset($obj->{$this->identifier_name}[$this->context->employee->id_lang])
        //                    )
        //                        ? $obj->{$this->identifier_name}[$this->context->employee->id_lang]
        //                        : $obj->{$this->identifier_name},
        //                ]
        //            );
        //            $this->addMetaTitle($this->toolbarTitle[count($this->toolbarTitle) - 1]);
        //        }
        //
        //        break;
        //}

        if (count($this->toolbarTitle)) {
            $this->showPageHeaderToolbar = true;
        }

        if (empty($this->pageHeaderToolbarTitle)) {
            $this->pageHeaderToolbarTitle = $this->toolbarTitle[count($this->toolbarTitle) - 1];
        }
        $this->getContext()->smarty->assign('help_link', 'https://help.prestashop.com/' . Language::getIsoById($this->getUser()->getData()->id_lang) . '/doc/'
            . Tools::getValue('controller') . '?version=' . _PS_VERSION_ . '&country=' . Language::getIsoById($this->getUser()->getData()->id_lang));
    }

    /**
     * Set default toolbarTitle to admin breadcrumb.
     */
    public function initToolbarTitle()
    {
        $this->toolbarTitle = array_unique($this->breadcrumbs);

        //switch ($this->display) {
        //    case 'edit':
        //        $this->toolbarTitle[] = $this->trans('Edit');
        //        $this->addMetaTitle($this->trans('Edit'));
        //
        //        break;
        //
        //    case 'add':
        //        $this->toolbarTitle[] = $this->trans('Add new');
        //        $this->addMetaTitle($this->trans('Add new'));
        //
        //        break;
        //
        //    case 'view':
        //        $this->toolbarTitle[] = $this->trans('View');
        //        $this->addMetaTitle($this->trans('View'));
        //
        //        break;
        //}

        if ($filter = $this->addFiltersToBreadcrumbs()) {
            $this->toolbarTitle[] = $filter;
        }
    }

    /**
     * @return string|void
     */
    public function addFiltersToBreadcrumbs()
    {
        if ($this->filter) {
            $filters = [];

            foreach ($this->fieldsList as $field => $t) {
                if (isset($t['filter_key'])) {
                    $field = $t['filter_key'];
                }

                if (($val = Tools::getValue($this->table . 'Filter_' . $field)) || $val = $this->getContext()->cookie->{$this->getCookieFilterPrefix() . $this->table . 'Filter_' . $field}) {
                    if (!is_array($val)) {
                        $filter_value = '';
                        if (isset($t['type']) && $t['type'] == 'bool') {
                            $filter_value = ((bool) $val) ? $this->trans('yes', 'Admin.Actions') : $this->trans('no', 'Admin.Actions');
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
                            $filters[] = $this->trans('%s: %s', 'Admin.Actions', [$t['title'], $filter_value]);
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
                            $filters[] = $this->trans('%s: %s', 'Admin.Actions', [$t['title'], $filter_value]);
                        }
                    }
                }
            }

            if (count($filters)) {
                return $this->trans('filter by %s', 'Admin.Actions', [implode(', ', $filters)]);
            }
        }
    }

    private function getNotificationTip($type)
    {
        $tips = [
            'order' => [
                $this->trans(
                    'Have you checked your [1][2]abandoned carts[/2][/1]?[3]Your next order could be hiding there!',
                    'Admin.Navigation.Notification',
                    [
                        '[1]' => '<strong>',
                        '[/1]' => '</strong>',
                        '[2]' => '<a href="' . $this->getContext()->link->getAdminLink('AdminCarts', true, [], ['action' => 'filterOnlyAbandonedCarts']) . '">',
                        '[/2]' => '</a>',
                        '[3]' => '<br>',
                    ]
                ),
            ],
            'customer' => [
                $this->trans('Are you active on social media these days?', 'Admin.Navigation.Notification'),
            ],
            'customer_message' => [
                $this->trans('Seems like all your customers are happy :)', 'Admin.Navigation.Notification'),
            ],
        ];

        if (!isset($tips[$type])) {
            return '';
        }

        return $tips[$type][array_rand($tips[$type])];
    }

    private function getTabs($parentId = 0, $level = 0)
    {
        $tabs = Tab::getTabs($this->getContext()->language->id, $parentId);
        $current_id = Tab::getCurrentParentId();

        foreach ($tabs as $index => $tab) {
            if (!Tab::checkTabRights($tab['id_tab'])
                || !$tab['enabled']
                || ($tab['class_name'] == 'AdminStock' && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
                || $tab['class_name'] == 'AdminCarrierWizard') {
                unset($tabs[$index]);

                continue;
            }

            // tab[class_name] does not contains the "Controller" suffix
            if (($tab['class_name'] . 'Controller' == $this->controllerNameLegacy) || ($current_id == $tab['id_tab']) || $tab['class_name'] == $this->controllerNameLegacy) {
                $tabs[$index]['current'] = true;
                $tabs[$index]['current_level'] = $level;
            } else {
                $tabs[$index]['current'] = false;
            }
            $tabs[$index]['img'] = null;
            $tabs[$index]['href'] = $this->getContext()->link->getTabLink($tab);
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

    public function processFilter()
    {
        //Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
        //    'fields' => &$this->fields_list,
        //]);

        if (!isset($this->listId)) {
            $this->listId = $this->table;
        }

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->listId)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->getContext()->cookie->{$prefix . $key});
                } elseif (stripos($key, $this->listId . 'Filter_') === 0) {
                    $this->getContext()->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->getContext()->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            //todo not used this but use Request
            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->listId . 'Filter_') === 0) {
                    $this->getContext()->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->getContext()->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $this->listId . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->getContext()->cookie->{$prefix . $key});
                    } else {
                        $this->getContext()->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->listId . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->getContext()->cookie->{$prefix . $key});
                    } else {
                        $this->getContext()->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->getContext()->cookie->getFamily($prefix . $this->listId . 'Filter_');
        $definition = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix . $this->listId . 'Filter_', 7 + Tools::strlen($prefix . $this->listId))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $this->listId));
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
                    //if (array_key_exists('tmpTableFilter', $field)) {
                    //    //$sql_filter = &$this->_tmpTableFilter;
                    //} elseif (array_key_exists('havingFilter', $field)) {
                    if (array_key_exists('havingFilter', $field)) {
                        $sql_filter = &$this->_filterHaving;
                    } else {
                        $sql_filter = &$this->_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = $this->trans('The \'From\' date format is invalid (YYYY-MM-DD)', 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = $this->trans('The \'To\' date format is invalid (YYYY-MM-DD)', 'Admin.Notifications.Error');
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
                            $value = (float) str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . $value . ' ';
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                }
            }
        }
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
}
