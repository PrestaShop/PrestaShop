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

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd().'/..');
}

if (Tools::getValue('token') == Tools::getAdminToken('AdminReferrers'.(int)Tab::getIdFromClassName('AdminReferrers').(int)Tools::getValue('id_employee'))) {
    if (Tools::isSubmit('ajaxProductFilter')) {
        Referrer::getAjaxProduct(
            (int)Tools::getValue('id_referrer'),
            (int)Tools::getValue('id_product'),
            new Employee((int)Tools::getValue('id_employee'))
        );
    } elseif (Tools::isSubmit('ajaxFillProducts')) {
        $json_array = array();
        $result = Db::getInstance()->executeS('
			SELECT p.id_product, pl.name
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl
				ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)Tools::getValue('id_lang').')
			'.(Tools::getValue('filter') != 'undefined' ? 'WHERE name LIKE "%'.pSQL(Tools::getValue('filter')).'%"' : '')
        );

        foreach ($result as $row) {
            $json_array[] = '{id_product:'.(int)$row['id_product'].',name:\''.addslashes($row['name']).'\'}';
        }

        die('['.implode(',', $json_array).']');
    }
}

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

/**
 * @property Referrer $object
 */
class AdminReferrersControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'referrer';
        $this->className = 'Referrer';

        parent::__construct();

        $this->fields_list = array(
            'id_referrer' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'width' => 25,
                'align' => 'center'
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'width' => 80
            ),
            'cache_visitors' => array(
                'title' => $this->trans('Visitors', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'center'
            ),
            'cache_visits' => array(
                'title' => $this->trans('Visits', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'center'
            ),
            'cache_pages' => array(
                'title' => $this->trans('Pages', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'center'
            ),
            'cache_registrations' => array(
                'title' => $this->trans('Reg.', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'center'
            ),
            'cache_orders' => array(
                'title' => $this->trans('Orders', array(), 'Admin.Global'),
                'width' => 30,
                'align' => 'center'
            ),
            'cache_sales' => array(
                'title' => $this->trans('Sales', array(), 'Admin.Global'),
                'width' => 80,
                'align' => 'right',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'price' => true
            ),
            'cart' => array(
                'title' => $this->trans('Avg. cart', array(), 'Admin.ShopParameters.Feature'),
                'width' => 50,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true
            ),
            'cache_reg_rate' => array(
                'title' => $this->trans('Reg. rate', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'center'
            ),
            'cache_order_rate' => array(
                'title' => $this->trans('Order rate', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'center'
            ),
            'fee0' => array(
                'title' => $this->trans('Click', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true
            ),
            'fee1' => array(
                'title' => $this->trans('Base', array(), 'Admin.ShopParameters.Feature'),
                'width' => 30,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true
            ),
            'fee2' => array(
                'title' => $this->trans('Percent', array(), 'Admin.Global'),
                'width' => 30,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJqueryUI('ui.datepicker');
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_referrer'] = array(
                'href' => self::$currentIndex.'&addreferrer&token='.$this->token,
                'desc' => $this->trans('Add new referrer', array(), 'Admin.ShopParameters.Feature'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        // Display list Referrers:
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'SUM(sa.cache_visitors) AS cache_visitors, SUM(sa.cache_visits) AS cache_visits, SUM(sa.cache_pages) AS cache_pages,
							SUM(sa.cache_registrations) AS cache_registrations, SUM(sa.cache_orders) AS cache_orders, SUM(sa.cache_sales) AS cache_sales,
							IF(sa.cache_orders > 0, ROUND(sa.cache_sales/sa.cache_orders, 2), 0) as cart, (sa.cache_visits*click_fee) as fee0,
							(sa.cache_orders*base_fee) as fee1, (sa.cache_sales*percent_fee/100) as fee2';
        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'referrer_shop` sa
				ON (sa.'.$this->identifier.' = a.'.$this->identifier.' AND sa.id_shop IN ('.implode(', ', Shop::getContextListShopID()).'))';

        $this->_group = 'GROUP BY sa.id_referrer';

        $this->tpl_list_vars = array(
            'enable_calendar' => $this->enableCalendar(),
            'calendar_form' => $this->displayCalendar(),
            'settings_form' => $this->displaySettings()
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        $uri = Tools::getHttpHost(true, true).__PS_BASE_URI__;

        $this->fields_form[0] = array('form' => array(
            'legend' => array(
                'title' => $this->trans('Affiliate', array(), 'Admin.ShopParameters.Feature'),
                'icon' => 'icon-group'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Name', array(), 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'password',
                    'label' => $this->trans('Password', array(), 'Admin.Global'),
                    'name' => 'passwd',
                    'desc' => $this->trans('Leave blank if no change.', array(), 'Admin.ShopParameters.Help'),
                    'autocomplete' => false
                )
            ),
            'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
        ));

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        if ($moduleManager->isInstalled('trackingfront')) {
            $this->fields_form[0]['form']['desc'] = array(
                $this->trans('Affiliates can access their data with this name and password.', array(), 'Admin.ShopParameters.Feature'),
                $this->trans('Front access:', array(), 'Admin.ShopParameters.Feature').' <a class="btn btn-link" href="'.$uri.'modules/trackingfront/stats.php" onclick="return !window.open(this.href);"><i class="icon-external-link-sign"></i> '.$uri.'modules/trackingfront/stats.php</a>'
            );
        } else {
            $this->fields_form[0]['form']['desc'] = array(
                $this->trans(
                    'Please install the "%modulename%" module in order to give your affiliates access to their own statistics.',
                    array(
                        '%modulename%' => Module::getModuleName('trackingfront'),
                    ),
                    'Admin.ShopParameters.Notification'
                ),
            );
        }

        $this->fields_form[1] = array('form' => array(
            'legend' => array(
                'title' => $this->trans('Commission plan', array(), 'Admin.ShopParameters.Feature'),
                'icon' => 'icon-dollar'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Click fee', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'click_fee',
                    'desc' => $this->trans('Fee given for each visit.', array(), 'Admin.ShopParameters.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Base fee', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'base_fee',
                    'desc' => $this->trans('Fee given for each order placed.', array(), 'Admin.ShopParameters.Help')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Percent fee', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'percent_fee',
                    'desc' => $this->trans('Percent of the sales.', array(), 'Admin.ShopParameters.Notification')
                )
            ),
            'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
        ));

        if (Shop::isFeatureActive()) {
            $this->fields_form[1]['form']['input'][] = array(
                'type' => 'shop',
                'label' => $this->trans('Shop association', array(), 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form[2] = array('form' => array(
            'legend' => array(
                'title' => $this->trans('Technical information -- Simple mode', array(), 'Admin.ShopParameters.Feature'),
                'icon' => 'icon-cogs'
            ),
            'help' => true,
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Include', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'http_referer_like',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('HTTP referrer', array(), 'Admin.ShopParameters.Feature')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'http_referer_like_not',
                    'cols' => 40,
                    'rows' => 1
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Include', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'request_uri_like',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('Request URI', array(), 'Admin.ShopParameters.Feature')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'request_uri_like_not',
                    'cols' => 40,
                    'rows' => 1
                )
            ),
            'desc' => $this->trans(
                'If you know how to use MySQL regular expressions, you can use the [1]expert mode[/1].',
                array(
                    '[1]' => '<a style="cursor: pointer; font-weight: bold;" onclick="$(\'#tracking_expert\').slideToggle();">',
                    '[/1]' => '</a>',
                ),
                'Admin.ShopParameters.Help'
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            )
        ));

        $this->fields_form[3] = array('form' => array(
            'legend' => array(
                'title' => $this->trans('Technical information -- Expert mode', array(), 'Admin.ShopParameters.Feature'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Include', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'http_referer_regexp',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('HTTP referrer', array(), 'Admin.ShopParameters.Feature')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'http_referer_regexp_not',
                    'cols' => 40,
                    'rows' => 1
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Include', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'request_uri_regexp',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('Request URI', array(), 'Admin.ShopParameters.Feature')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', array(), 'Admin.ShopParameters.Feature'),
                    'name' => 'request_uri_regexp_not',
                    'cols' => 40,
                    'rows' => 1
                )
            )
        ));

        $this->multiple_fieldsets = true;

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'click_fee' => number_format((float)($this->getFieldValue($obj, 'click_fee')), 2),
            'base_fee' => number_format((float)($this->getFieldValue($obj, 'base_fee')), 2),
            'percent_fee' => number_format((float)($this->getFieldValue($obj, 'percent_fee')), 2),
            'http_referer_like' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'http_referer_like'), ENT_COMPAT, 'UTF-8')),
            'http_referer_like_not' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'http_referer_like_not'), ENT_COMPAT, 'UTF-8')),
            'request_uri_like' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'request_uri_like'), ENT_COMPAT, 'UTF-8')),
            'request_uri_like_not' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'request_uri_like_not'), ENT_COMPAT, 'UTF-8'))
        );

        $this->tpl_form_vars = array('uri' => $uri);

        return parent::renderForm();
    }

    public function displayCalendar($action = null, $table = null, $identifier = null, $id = null)
    {
        return AdminReferrersController::displayCalendarForm(array(
            'Calendar' => $this->trans('Calendar', array(), 'Admin.Global'),
            'Day' => $this->trans('Today', array(), 'Admin.Global'),
            'Month' => $this->trans('Month', array(), 'Admin.Global'),
            'Year' => $this->trans('Year', array(), 'Admin.Global')
        ), $this->token, $action, $table, $identifier, $id);
    }

    public static function displayCalendarForm($translations, $token, $action = null, $table = null, $identifier = null, $id = null)
    {
        $context = Context::getContext();
        $tpl = $context->controller->createTemplate('calendar.tpl');

        $context->controller->addJqueryUI('ui.datepicker');

        $tpl->assign(array(
            'current' => self::$currentIndex,
            'token' => $token,
            'action' => $action,
            'table' => $table,
            'identifier' => $identifier,
            'id' => $id,
            'translations' => $translations,
            'datepickerFrom' => Tools::getValue('datepickerFrom', $context->employee->stats_date_from),
            'datepickerTo' => Tools::getValue('datepickerTo', $context->employee->stats_date_to)
        ));

        return $tpl->fetch();
    }

    public function displaySettings()
    {
        if (!Tools::isSubmit('viewreferrer')) {
            $tpl = $this->createTemplate('form_settings.tpl');

            $statsdata = Module::getInstanceByName('statsdata');

            $statsdata_name = false;
            if (Validate::isLoadedObject($statsdata)) {
                $statsdata_name = $statsdata->displayName;
            }
            $tpl->assign(array(
                'statsdata_name' => $statsdata_name,
                'current' => self::$currentIndex,
                'token' => $this->token,
                'tracking_dt' => (int)Tools::getValue('tracking_dt', Configuration::get('TRACKING_DIRECT_TRAFFIC'))
            ));

            return $tpl->fetch();
        }
    }

    protected function enableCalendar()
    {
        return (!Tools::isSubmit('add'.$this->table) && !Tools::isSubmit('submitAdd'.$this->table) && !Tools::isSubmit('update'.$this->table));
    }

    public function postProcess()
    {
        if ($this->enableCalendar()) {
            // Warning, instantiating a controller here changes the controller in the Context...
            $calendar_tab = new AdminStatsController();
            $calendar_tab->postProcess();
            // ...so we set it back to the correct one here
            $this->context->controller = $this;
        }

        if (Tools::isSubmit('submitSettings')) {
            if ($this->access('edit')) {
                if (Configuration::updateValue('TRACKING_DIRECT_TRAFFIC', (int)Tools::getValue('tracking_dt'))) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.Tools::getValue('token'));
                }
            }
        }

        if (ModuleGraph::getDateBetween() != Configuration::get('PS_REFERRERS_CACHE_LIKE') || Tools::isSubmit('submitRefreshCache')) {
            Referrer::refreshCache();
        }
        if (Tools::isSubmit('submitRefreshIndex')) {
            Referrer::refreshIndex();
        }

        return parent::postProcess();
    }

    public function renderView()
    {
        $referrer = new Referrer((int)Tools::getValue('id_referrer'));

        $display_tab = array(
            'uniqs' => $this->trans('Unique visitors', array(), 'Admin.ShopParameters.Feature'),
            'visitors' => $this->trans('Visitors', array(), 'Admin.ShopParameters.Feature'),
            'visits' => $this->trans('Visits', array(), 'Admin.ShopParameters.Feature'),
            'pages' => $this->trans('Pages viewed', array(), 'Admin.ShopParameters.Feature'),
            'registrations' => $this->trans('Registrations', array(), 'Admin.ShopParameters.Feature'),
            'orders' => $this->trans('Orders', array(), 'Admin.Global'),
            'sales' => $this->trans('Sales', array(), 'Admin.Global'),
            'reg_rate' => $this->trans('Registration rate', array(), 'Admin.ShopParameters.Feature'),
            'order_rate' => $this->trans('Order rate', array(), 'Admin.ShopParameters.Feature'),
            'click_fee' => $this->trans('Click fee', array(), 'Admin.ShopParameters.Feature'),
            'base_fee' => $this->trans('Base fee', array(), 'Admin.ShopParameters.Feature'),
            'percent_fee' => $this->trans('Percent fee', array(), 'Admin.ShopParameters.Feature'));

        $this->tpl_view_vars = array(
            'enable_calendar' => $this->enableCalendar(),
            'calendar_form' => $this->displayCalendar($this->action, $this->table, $this->identifier, (int)Tools::getValue($this->identifier)),
            'referrer' => $referrer,
            'display_tab' => $display_tab,
            'id_employee' => (int)$this->context->employee->id,
            'id_lang' => (int)$this->context->language->id
        );

        return parent::renderView();
    }
}
