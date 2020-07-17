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

        $this->fields_list = [
            'id_referrer' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'width' => 25,
                'align' => 'center',
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Admin.Global'),
                'width' => 80,
            ],
            'cache_visitors' => [
                'title' => $this->trans('Visitors', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'center',
            ],
            'cache_visits' => [
                'title' => $this->trans('Visits', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'center',
            ],
            'cache_pages' => [
                'title' => $this->trans('Pages', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'center',
            ],
            'cache_registrations' => [
                'title' => $this->trans('Reg.', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'center',
            ],
            'cache_orders' => [
                'title' => $this->trans('Orders', [], 'Admin.Global'),
                'width' => 30,
                'align' => 'center',
            ],
            'cache_sales' => [
                'title' => $this->trans('Sales', [], 'Admin.Global'),
                'width' => 80,
                'align' => 'right',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'price' => true,
            ],
            'cart' => [
                'title' => $this->trans('Avg. cart', [], 'Admin.Shopparameters.Feature'),
                'width' => 50,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true,
            ],
            'cache_reg_rate' => [
                'title' => $this->trans('Reg. rate', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'center',
            ],
            'cache_order_rate' => [
                'title' => $this->trans('Order rate', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'center',
            ],
            'fee0' => [
                'title' => $this->trans('Click', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true,
            ],
            'fee1' => [
                'title' => $this->trans('Base', [], 'Admin.Shopparameters.Feature'),
                'width' => 30,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true,
            ],
            'fee2' => [
                'title' => $this->trans('Percent', [], 'Admin.Global'),
                'width' => 30,
                'align' => 'right',
                'price' => true,
                'havingFilter' => true,
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJqueryUI('ui.datepicker');
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_referrer'] = [
                'href' => self::$currentIndex . '&addreferrer&token=' . $this->token,
                'desc' => $this->trans('Add new referrer', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            ];
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
			LEFT JOIN `' . _DB_PREFIX_ . 'referrer_shop` sa
				ON (sa.`' . bqSQL($this->identifier) . '` = a.`' . bqSQL($this->identifier) . '` AND sa.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . '))';

        $this->_group = 'GROUP BY sa.id_referrer';

        $this->tpl_list_vars = [
            'enable_calendar' => $this->enableCalendar(),
            'calendar_form' => $this->displayCalendar(),
            'settings_form' => $this->displaySettings(),
        ];

        return parent::renderList();
    }

    public function renderForm()
    {
        $uri = Tools::getHttpHost(true, true) . __PS_BASE_URI__;

        $this->fields_form[0] = ['form' => [
            'legend' => [
                'title' => $this->trans('Affiliate', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-group',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Admin.Global'),
                    'name' => 'name',
                    'required' => true,
                    'autocomplete' => false,
                ],
                [
                    'type' => 'password',
                    'label' => $this->trans('Password', [], 'Admin.Global'),
                    'name' => 'passwd',
                    'desc' => $this->trans('Leave blank if no change.', [], 'Admin.Shopparameters.Help'),
                    'autocomplete' => false,
                ],
            ],
            'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
        ]];

        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();

        if ($moduleManager->isInstalled('trackingfront')) {
            $this->fields_form[0]['form']['desc'] = [
                $this->trans('Affiliates can access their data with this name and password.', [], 'Admin.Shopparameters.Feature'),
                $this->trans('Front access:', [], 'Admin.Shopparameters.Feature') . ' <a class="btn btn-link" href="' . $uri . 'modules/trackingfront/stats.php" onclick="return !window.open(this.href);"><i class="icon-external-link-sign"></i> ' . $uri . 'modules/trackingfront/stats.php</a>',
            ];
        } else {
            $this->fields_form[0]['form']['desc'] = [
                $this->trans(
                    'Please install the "%modulename%" module in order to give your affiliates access to their own statistics.',
                    [
                        '%modulename%' => Module::getModuleName('trackingfront'),
                    ],
                    'Admin.Shopparameters.Notification'
                ),
            ];
        }

        $this->fields_form[1] = ['form' => [
            'legend' => [
                'title' => $this->trans('Commission plan', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-dollar',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Click fee', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'click_fee',
                    'desc' => $this->trans('Fee given for each visit.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Base fee', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'base_fee',
                    'desc' => $this->trans('Fee given for each order placed.', [], 'Admin.Shopparameters.Help'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Percent fee', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'percent_fee',
                    'desc' => $this->trans('Percent of the sales.', [], 'Admin.Shopparameters.Notification'),
                ],
            ],
            'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
        ]];

        if (Shop::isFeatureActive()) {
            $this->fields_form[1]['form']['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->fields_form[2] = ['form' => [
            'legend' => [
                'title' => $this->trans('Technical information -- Simple mode', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-cogs',
            ],
            'help' => true,
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Include', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'http_referer_like',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('HTTP referrer', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'http_referer_like_not',
                    'cols' => 40,
                    'rows' => 1,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Include', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'request_uri_like',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('Request URI', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'request_uri_like_not',
                    'cols' => 40,
                    'rows' => 1,
                ],
            ],
            'desc' => $this->trans(
                'If you know how to use MySQL regular expressions, you can use the [1]expert mode[/1].',
                [
                    '[1]' => '<a style="cursor: pointer; font-weight: bold;" onclick="$(\'#tracking_expert\').slideToggle();">',
                    '[/1]' => '</a>',
                ],
                'Admin.Shopparameters.Help'
            ),
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
        ]];

        $this->fields_form[3] = ['form' => [
            'legend' => [
                'title' => $this->trans('Technical information -- Expert mode', [], 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Include', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'http_referer_regexp',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('HTTP referrer', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'http_referer_regexp_not',
                    'cols' => 40,
                    'rows' => 1,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Include', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'request_uri_regexp',
                    'cols' => 40,
                    'rows' => 1,
                    'legend' => $this->trans('Request URI', [], 'Admin.Shopparameters.Feature'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Exclude', [], 'Admin.Shopparameters.Feature'),
                    'name' => 'request_uri_regexp_not',
                    'cols' => 40,
                    'rows' => 1,
                ],
            ],
        ]];

        $this->multiple_fieldsets = true;

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = [
            'click_fee' => number_format((float) ($this->getFieldValue($obj, 'click_fee')), 2),
            'base_fee' => number_format((float) ($this->getFieldValue($obj, 'base_fee')), 2),
            'percent_fee' => number_format((float) ($this->getFieldValue($obj, 'percent_fee')), 2),
            'http_referer_like' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'http_referer_like'), ENT_COMPAT, 'UTF-8')),
            'http_referer_like_not' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'http_referer_like_not'), ENT_COMPAT, 'UTF-8')),
            'request_uri_like' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'request_uri_like'), ENT_COMPAT, 'UTF-8')),
            'request_uri_like_not' => str_replace('\\', '\\\\', htmlentities($this->getFieldValue($obj, 'request_uri_like_not'), ENT_COMPAT, 'UTF-8')),
        ];

        $this->tpl_form_vars = ['uri' => $uri];

        return parent::renderForm();
    }

    public function displayAjaxProductFilter()
    {
        $this->ajaxRender(
            Referrer::getAjaxProduct(
                (int) Tools::getValue('id_referrer'),
                (int) Tools::getValue('id_product'),
                new Employee((int) Tools::getValue('id_employee'))
        ));
    }

    public function displayAjaxFillProducts()
    {
        $json_array = [];
        $result = Db::getInstance()->executeS('
            SELECT p.id_product, pl.name
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
                ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int) Tools::getValue('id_lang') . ')
            ' . (Tools::getValue('filter') != 'undefined' ? 'WHERE name LIKE "%' . pSQL(Tools::getValue('filter')) . '%"' : '')
        );

        foreach ($result as $row) {
            $json_array[] = [
                'id_product' => (int) $row['id_product'],
                'name' => $row['name'],
            ];
        }

        $this->ajaxRender('[' . implode(',', $json_array) . ']');
    }

    public function displayCalendar($action = null, $table = null, $identifier = null, $id = null)
    {
        return AdminReferrersController::displayCalendarForm([
            'Calendar' => $this->trans('Calendar', [], 'Admin.Global'),
            'Day' => $this->trans('Today', [], 'Admin.Global'),
            'Month' => $this->trans('Month', [], 'Admin.Global'),
            'Year' => $this->trans('Year', [], 'Admin.Global'),
        ], $this->token, $action, $table, $identifier, $id);
    }

    public static function displayCalendarForm($translations, $token, $action = null, $table = null, $identifier = null, $id = null)
    {
        $context = Context::getContext();
        $tpl = $context->controller->createTemplate('calendar.tpl');

        $context->controller->addJqueryUI('ui.datepicker');

        $tpl->assign([
            'current' => self::$currentIndex,
            'token' => $token,
            'action' => $action,
            'table' => $table,
            'identifier' => $identifier,
            'id' => $id,
            'translations' => $translations,
            'datepickerFrom' => Tools::getValue('datepickerFrom', $context->employee->stats_date_from),
            'datepickerTo' => Tools::getValue('datepickerTo', $context->employee->stats_date_to),
        ]);

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
            $tpl->assign([
                'statsdata_name' => $statsdata_name,
                'current' => self::$currentIndex,
                'token' => $this->token,
                'tracking_dt' => (int) Tools::getValue('tracking_dt', Configuration::get('TRACKING_DIRECT_TRAFFIC')),
                'exclude_tx' => (int) Tools::getValue('exclude_tx', Configuration::get('REFERER_TAX')),
                'exclude_ship' => (int) Tools::getValue('exclude_ship', Configuration::get('REFERER_SHIPPING')),
            ]);

            return $tpl->fetch();
        }
    }

    protected function enableCalendar()
    {
        return !Tools::isSubmit('add' . $this->table) && !Tools::isSubmit('submitAdd' . $this->table) && !Tools::isSubmit('update' . $this->table);
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
                if (Configuration::updateValue('TRACKING_DIRECT_TRAFFIC', (int) Tools::getValue('tracking_dt'))
                    && Configuration::updateValue('REFERER_TAX', (int) Tools::getValue('exclude_tx'))
                    && Configuration::updateValue('REFERER_SHIPPING', (int) Tools::getValue('exclude_ship'))) {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . Tools::getValue('token'));
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
        $referrer = new Referrer((int) Tools::getValue('id_referrer'));

        $display_tab = [
            'uniqs' => $this->trans('Unique visitors', [], 'Admin.Shopparameters.Feature'),
            'visitors' => $this->trans('Visitors', [], 'Admin.Shopparameters.Feature'),
            'visits' => $this->trans('Visits', [], 'Admin.Shopparameters.Feature'),
            'pages' => $this->trans('Pages viewed', [], 'Admin.Shopparameters.Feature'),
            'registrations' => $this->trans('Registrations', [], 'Admin.Shopparameters.Feature'),
            'orders' => $this->trans('Orders', [], 'Admin.Global'),
            'sales' => $this->trans('Sales', [], 'Admin.Global'),
            'reg_rate' => $this->trans('Registration rate', [], 'Admin.Shopparameters.Feature'),
            'order_rate' => $this->trans('Order rate', [], 'Admin.Shopparameters.Feature'),
            'click_fee' => $this->trans('Click fee', [], 'Admin.Shopparameters.Feature'),
            'base_fee' => $this->trans('Base fee', [], 'Admin.Shopparameters.Feature'),
            'percent_fee' => $this->trans('Percent fee', [], 'Admin.Shopparameters.Feature'),
        ];

        $this->tpl_view_vars = [
            'enable_calendar' => $this->enableCalendar(),
            'calendar_form' => $this->displayCalendar($this->action, $this->table, $this->identifier, (int) Tools::getValue($this->identifier)),
            'referrer' => $referrer,
            'display_tab' => $display_tab,
            'id_employee' => (int) $this->context->employee->id,
            'id_lang' => (int) $this->context->language->id,
        ];

        return parent::renderView();
    }
}
