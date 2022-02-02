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
use Media;
use ObjectModel;
use PrestaShopBundle\Bridge\AddActionTrait;
use PrestaShopBundle\Bridge\AddActionInterface;
use PrestaShopBundle\Bridge\ControllerConfiguration;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopException;
use Shop;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tab;
use Tools;
use Validate;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController implements AddActionInterface
{
    use AddActionTrait;

    private const DEFAULT_THEME = 'default';
    private const CONTROLLER_NAME_LEGACY = 'AdminFeatures';
    private const POSITION_IDENTIFIER = 'id_feature';
    private const TABLE = 'feature';

    /**
     * @var string
     */
    public $php_self;

    /**
     * @var string
     */
    private $listId = 'feature';

    /**
     * @var string
     */
    private $shopLinkType;

    /**
     * @var string
     */
    private $identifier = 'id_feature';

    /**
     * @var bool
     */
    protected $deleted = false;

    //Filters
    /**
     * @var array
     */
    protected $filterList = [];

    /**
     * @var array
     */
    public $deleteLinksVariableTemplate = [];

    //Handle later
    /**
     * @var bool
     */
    protected $ajax = false;

    //SQL section
    /**
     * @var array
     */
    protected $_filter;

    /**
     * @var string
     */
    protected $_filterHaving;

    /**
     * @var string
     */
    private $_orderBy = '';

    /**
     * @var string
     */
    private $_orderWay = '';

    /**
     * @var string
     */
    private $_defaultOrderBy = 'position';

    /**
     * @var string
     */
    protected $_defaultOrderWay = 'ASC';

    /**
     * @var array
     */
    protected $_pagination = [20, 50, 100, 300, 1000];

    /**
     * @var int
     */
    protected $_default_pagination = 50;

    /**
     * @var array
     */
    protected array $_list;

    /**
     * @var string
     */
    protected $_listTotal;

    /**
     * @var string
     */
    protected $_list_error;

    /**
     * @var string
     */
    protected $_select;

    /**
     * @var string
     */
    protected $_where;

    /**
     * @var string
     */
    protected $_join;

    /**
     * @var bool Use SQL_CALC_FOUND_ROWS / FOUND_ROWS to count the number of records
     */
    protected $_useFoundRows = true;

    //Contain the query SQL for list
    /**
     * @var string
     */
    protected $_listsql;

    /**
     * @var bool Do not automatically select * anymore but select only what is necessary
     */
    protected $explicitSelect = false;

    /**
     * @var bool Automatically join language table if true
     */
    //default false
    public $isJoinLanguageTableAuto = true;

    /**
     * @var int
     */
    private $multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;

    //We will keep this
    /**
     * @var ControllerConfiguration
     */
    private $controllerConfiguration;

    public function initController(Request $request)
    {
        //Unmovable
        $this->php_self = get_class($this);
        $this->getContext()->controller = $this;

        $this->controllerConfiguration = $this->get('prestashop.core.bridge.controller_configuration_factory')->create([
            'id' => Tab::getIdFromClassName(
                self::CONTROLLER_NAME_LEGACY
            ),
            'controllerName' => get_class($this),
            'controllerNameLegacy' => self::CONTROLLER_NAME_LEGACY,
            'positionIdentifier' => self::POSITION_IDENTIFIER,
            'table' => self::TABLE,
        ]);

        //Todo handle this later
        if (!Shop::isFeatureActive()) {
            $this->shopLinkType = '';
        }

        //Todo this will be move when I handle HelperListBridge
        if (empty($this->_defaultOrderBy)) {
            $this->_defaultOrderBy = $this->identifier;
        }

        //Todo this will be move when I handle SmartyRenderBridge
        $this->controllerConfiguration->folderTemplate = Tools::toUnderscoreCase(substr($this->controllerConfiguration->controllerNameLegacy, 5)) . '/';

        $this->setCurrentIndex();
        $this->initToken($request);
    }

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request)
    {
        //Moved in other way
        $this->initController($request);

        //build list and page with action
        $this->setListFields();
        $this->processFilter();
        $this->buildActionList();
        $this->getList($this->controllerConfiguration->context->language->id);

        return $this->renderSmarty(
            $this->renderList(),
            $this->controllerConfiguration
        );
    }

    public function renderSmarty(string $content, ControllerConfiguration $controllerConfiguration): Response
    {
        $this->setMedia();

        return $this->get('prestashop.core.bridge.smarty_bridge')->render($content, $controllerConfiguration);
    }

    /**
     * Render list content
     *
     * @return string
     */
    public function renderList(): string
    {
        $this->getContext()->smarty->assign('link', $this->getContext()->link);

        if (!($this->controllerConfiguration->fieldsList && is_array($this->controllerConfiguration->fieldsList))) {
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
        $helper->tpl_delete_link_vars = $this->deleteLinksVariableTemplate;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->controllerConfiguration->actionsAvailable as $action) {
            if (!in_array($action, $this->controllerConfiguration->actions) && isset($this->controllerConfiguration->$action) && $this->controllerConfiguration->$action) {
                $this->controllerConfiguration->actions[] = $action;
            }
        }

        /* @phpstan-ignore-next-line */
        $helper->sql = $this->_listsql;

        return $helper->generateList($this->_list, $this->controllerConfiguration->fieldsList);
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        if ($this->controllerConfiguration->table == 'feature_value') {
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
            $this->listId = $this->controllerConfiguration->table;
        }

        if (!Validate::isTableOrIdentifier($this->controllerConfiguration->table)) {
            throw new PrestaShopException(sprintf('Table name %s is invalid:', $this->controllerConfiguration->table));
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
            && Tools::isSubmit('export' . $this->controllerConfiguration->table)
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
        //Todo handle this later
        $select_shop = '';
        if ($this->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($this->multishop_context && Shop::isTableAssociated($this->controllerConfiguration->table) && !empty($this->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->getUser()->getData()->isSuperAdmin()) {
                $test_join = !preg_match('#`?' . preg_quote(_DB_PREFIX_ . $this->controllerConfiguration->table . '_shop') . '`? *sa#', $this->_join);
                if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($this->controllerConfiguration->table)) {
                    $this->_where .= ' AND EXISTS (
                            SELECT 1
                            FROM `' . _DB_PREFIX_ . $this->controllerConfiguration->table . '_shop` sa
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
                    } elseif ($key == 'id_' . $this->controllerConfiguration->table) {
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

                $list_count = 'SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $this->controllerConfiguration->table . '`';
            } else {
                //$this->_listsql = 'SELECT ' . ($this->_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                $this->_listsql = 'SELECT ' .
                    $this->_listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT COUNT(*) AS `' . _DB_PREFIX_ . $this->controllerConfiguration->table . '` ' .
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

        if ($this->controllerConfiguration->table == 'feature') {
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
     * This function sets various display options for helper list.
     *
     //* @param HelperList|HelperView|HelperOptions $helper
     */
    public function setHelperDisplay($helper)
    {
        if (empty($this->breadcrumbs)) {
            $this->get('prestashop.core.bridge.breadcrumbs_and_title_hydrator')->hydrate($this->controllerConfiguration);
        }

        if (empty($this->controllerConfiguration->toolbarTitle)) {
            $this->get('prestashop.core.bridge.toolbar_flags_hydrator')->hydrate($this->controllerConfiguration);
        }
        //// tocheck
        //if ($this->object && $this->object->id) {
        //    $helper->id = $this->object->id;
        //}
        //
        //// @todo : move that in Helper
        /// Sortons ça d'ici et mettons le en dur => Pas pas mettre en dur car traduit depuis la BDD
        $helper->title = is_array($this->controllerConfiguration->toolbarTitle) ? implode(' ' . Configuration::get('PS_NAVIGATION_PIPE') . ' ', $this->controllerConfiguration->toolbarTitle) : $this->controllerConfiguration->toolbarTitle;
        $helper->toolbar_btn = $this->controllerConfiguration->toolbarButton;
        //$helper->show_toolbar = $this->show_toolbar;
        $helper->show_toolbar = true;
        //$helper->toolbar_scroll = $this->toolbar_scroll;
        //$helper->override_folder = $this->tpl_folder;
        $helper->actions = $this->controllerConfiguration->actions;
        //$helper->simple_header = $this->list_simple_header;
        $helper->bulk_actions = $this->controllerConfiguration->bulkActions;
        $helper->currentIndex = $this->generateUrl('admin_features_index');
        //Useless for list
        //if (isset($helper->className)) {
        //    $helper->className = $this->className;
        //}
        $helper->table = $this->controllerConfiguration->table;
        if (isset($helper->name_controller)) {
            $helper->name_controller = $this->controllerConfiguration->controllerNameLegacy;
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
        //$helper->token = $this->controllerConfiguration->token;
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
        $helper->position_identifier = $this->controllerConfiguration->positionIdentifier;
        //if (isset($helper->position_group_identifier)) {
        //    $helper->position_group_identifier = $this->position_group_identifier;
        //}
        //// @phpstan-ignore-next-line
        $helper->controller_name = $this->controllerConfiguration->controllerNameLegacy;
        $helper->list_id = $this->listId ?? $this->controllerConfiguration->table;
        $helper->bootstrap = $this->controllerConfiguration->bootstrap;
        //
        //// For each action, try to add the corresponding skip elements list
        //$helper->list_skip_actions = $this->list_skip_actions;
    }

    public function setCurrentIndex(): void
    {
        // Set current index
        $currentIndex = 'index.php' . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');
        if ($back = Tools::getValue('back')) {
            $currentIndex .= '&back=' . urlencode($back);
        }

        $this->controllerConfiguration->currentIndex = $currentIndex;
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

            if (!$this->controllerConfiguration->liteDisplay) {
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
            if ($css_path && (!isset($this->controllerConfiguration->cssFiles[$key]) || ($this->controllerConfiguration->cssFiles[$key] != reset($css_path)))) {
                $size = count($this->controllerConfiguration->cssFiles);
                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->controllerConfiguration->cssFiles = array_merge(array_slice($this->controllerConfiguration->cssFiles, 0, $offset), $css_path, array_slice($this->controllerConfiguration->cssFiles, $offset));
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

            if ($js_path && !in_array($js_path, $this->controllerConfiguration->jsFiles)) {
                $this->controllerConfiguration->jsFiles[] = $js_path . ($version ? '?' . $version : '');
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
        $this->controllerConfiguration->token = $request->query->get('_token');
    }

    public function processFilter()
    {
        //Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
        //    'fields' => &$this->fields_list,
        //]);

        if (!isset($this->listId)) {
            $this->listId = $this->controllerConfiguration->table;
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
        $sql_table = $this->controllerConfiguration->table == 'order' ? 'orders' : $this->controllerConfiguration->table;

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
            $languageJoinClause = 'LEFT JOIN `' . _DB_PREFIX_ . bqSQL($this->controllerConfiguration->table) . '_lang` b
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
        $this->controllerConfiguration->warnings[] = $msg;
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
     * Build action for list interface
     *
     * @return void
     *
     * @throws \Exception
     */
    private function buildActionList(): void
    {
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature', [
            //Used $this->generateUrl('admin_features_add')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
            'icon' => 'process-icon-new',
        ]);
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature_value', [
            //Used $this->generateUrl('admin_features_add_value')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
            'icon' => 'process-icon-new',
        ]);

        $this->addAction(self::ACTION_TYPE_LIST_HEADER_TOOLBAR, 'new', [
            //Replace by $this->generateUrl('admin_features_add')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature&token=' . $this->controllerConfiguration->token,
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

    /**
     * Define fields in the list
     *
     * @return void
     */
    private function setListFields(): void
    {
        $this->controllerConfiguration->fieldsList = [
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

    /**
     * Set the filters used for the list display.
     */
    private function getCookieFilterPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }
}
