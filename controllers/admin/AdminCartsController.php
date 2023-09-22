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

/**
 * @property Cart $object
 */
class AdminCartsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cart';
        $this->className = 'Cart';
        $this->lang = false;
        $this->explicitSelect = true;

        parent::__construct();

        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->_orderWay = 'DESC';

        $this->_select = '
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`,
            a.id_cart total,
            ca.name carrier,
            o.id_order,
            IF (
		        IFNULL(o.id_order, \'' . $this->trans('Non ordered', [], 'Admin.Orderscustomers.Feature') . '\') = \'' . $this->trans('Non ordered', [], 'Admin.Orderscustomers.Feature') . '\',
		        IF(a.`date_add` > date_add(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', interval 1 day), \'' . $this->trans('Abandoned cart', [], 'Admin.Orderscustomers.Feature') . '\',
		        \'' . $this->trans('Non ordered', [], 'Admin.Orderscustomers.Feature') . '\'),
		        o.id_order
            ) AS status,
		    IF(o.id_order, 1, 0) badge_success,
		    IF(o.id_order, 0, 1) badge_danger,
		    IF(co.id_guest, 1, 0) id_guest';
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = a.id_customer)
		LEFT JOIN ' . _DB_PREFIX_ . 'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN ' . _DB_PREFIX_ . 'carrier ca ON (ca.id_carrier = a.id_carrier)
		LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_cart = a.id_cart)
		LEFT JOIN (
            SELECT DISTINCT `id_guest`
            FROM `' . _DB_PREFIX_ . 'connections`
            WHERE `date_add` > date_add(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', interval -30 minute)
       ) AS co ON co.`id_guest` = a.`id_guest`';

        if (Tools::getValue('action') && Tools::getValue('action') == 'filterOnlyAbandonedCarts') {
            $this->_having = 'status = \'' . $this->trans('Abandoned cart', [], 'Admin.Orderscustomers.Feature') . '\'';
        } else {
            $this->_use_found_rows = false;
        }

        $this->fields_list = [
            'id_cart' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'status' => [
                'title' => $this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'),
                'align' => 'text-center',
                'badge_danger' => true,
                'havingFilter' => true,
            ],
            'customer' => [
                'title' => $this->trans('Customer', [], 'Admin.Global'),
                'filter_key' => 'c!lastname',
            ],
            'total' => [
                'title' => $this->trans('Total', [], 'Admin.Global'),
                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                'orderby' => false,
                'search' => false,
                'align' => 'text-right',
                'badge_success' => true,
            ],
            'carrier' => [
                'title' => $this->trans('Carrier', [], 'Admin.Shipping.Feature'),
                'align' => 'text-left',
                'filter_key' => 'ca!name',
            ],
            'date_add' => [
                'title' => $this->trans('Date', [], 'Admin.Global'),
                'align' => 'text-left',
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
                'filter_key' => 'a!date_add',
            ],
        ];

        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            $this->fields_list['id_guest'] = [
                'title' => $this->trans('Online', [], 'Admin.Global'),
                'align' => 'text-center',
                'type' => 'bool',
                'havingFilter' => true,
                'class' => 'fixed-width-xs',
            ];
        }

        $this->shopLinkType = 'shop';

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['export_cart'] = [
                'href' => self::$currentIndex . '&exportcart&token=' . $this->token,
                'desc' => $this->trans('Export carts', [], 'Admin.Orderscustomers.Feature'),
                'icon' => 'process-icon-export',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = [];

        /* The data generation is located in AdminStatsControllerCore */
        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'icon-sort-by-attributes-alt';
        //$helper->chart = true;
        $helper->color = 'color1';
        $helper->title = $this->trans('Conversion Rate', [], 'Admin.Global');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('CONVERSION_RATE') !== false) {
            $helper->value = ConfigurationKPI::get('CONVERSION_RATE');
        }
        if (ConfigurationKPI::get('CONVERSION_RATE_CHART') !== false) {
            $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=conversion_rate';
        $helper->refresh = (bool) (ConfigurationKPI::get('CONVERSION_RATE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color2';
        $helper->title = $this->trans('Abandoned Carts', [], 'Admin.Orderscustomers.Feature');
        $date_from = date(Context::getContext()->language->date_format_lite, strtotime('-2 day'));
        $date_to = date(Context::getContext()->language->date_format_lite, strtotime('-1 day'));
        $helper->subtitle = $this->trans('From %date1% to %date2%', ['%date1%' => $date_from, '%date2%' => $date_to], 'Admin.Orderscustomers.Feature');
        $helper->href = $this->context->link->getAdminLink('AdminCarts') . '&action=filterOnlyAbandonedCarts';
        if (ConfigurationKPI::get('ABANDONED_CARTS') !== false) {
            $helper->value = ConfigurationKPI::get('ABANDONED_CARTS');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=abandoned_cart';
        $helper->refresh = (bool) (ConfigurationKPI::get('ABANDONED_CARTS_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-money';
        $helper->color = 'color3';
        $helper->title = $this->trans('Average Order Value', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('AVG_ORDER_VALUE') !== false) {
            $helper->value = $this->trans('%amount% tax excl.', ['%amount%' => ConfigurationKPI::get('AVG_ORDER_VALUE')], 'Admin.Orderscustomers.Feature');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=average_order_value';
        $helper->refresh = ConfigurationKPI::get('AVG_ORDER_VALUE_EXPIRE') < $time;
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visitor';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $this->trans('Net Profit per Visitor', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->trans('30 days', [], 'Admin.Global');
        if (ConfigurationKPI::get('NETPROFIT_VISITOR') !== false) {
            $helper->value = ConfigurationKPI::get('NETPROFIT_VISITOR');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=netprofit_visitor';
        $helper->refresh = (bool) (ConfigurationKPI::get('NETPROFIT_VISITOR_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        $context = Context::getContext();
        $context->cart = new Cart($id_cart);
        $context->currency = new Currency((int) $context->cart->id_currency);
        $context->customer = new Customer((int) $context->cart->id_customer);

        return Cart::getTotalCart($id_cart, true, Cart::BOTH_WITHOUT_SHIPPING);
    }

    public function displayDeleteLink($token, $id, $name = null)
    {
        // don't display ordered carts
        foreach ($this->_list as $row) {
            if ($row['id_cart'] == $id && isset($row['id_order']) && is_numeric($row['id_order'])) {
                return;
            }
        }

        return $this->helper->displayDeleteLink($token, $id, $name);
    }

    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query', [], 'Admin.Notifications.Error') . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $skip_list = [];

        foreach ($this->_list as $row) {
            if (isset($row['id_order']) && is_numeric($row['id_order'])) {
                $skip_list[] = $row['id_cart'];
            }
        }

        if (array_key_exists('delete', $helper->list_skip_actions)) {
            $helper->list_skip_actions['delete'] = array_merge($helper->list_skip_actions['delete'], (array) $skip_list);
        } else {
            $helper->list_skip_actions['delete'] = (array) $skip_list;
        }
        $helper->force_show_bulk_actions = true;
        $helper->force_hide_bulk_actions_btn = count($helper->list_skip_actions['delete']) === count($this->_list);

        return $helper->generateList($this->_list, $this->fields_list);
    }

    /**
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    protected function getOrderByClause($orderBy, $orderDirection)
    {
        $this->_orderBy = $this->checkOrderBy($orderBy);
        $this->_orderWay = $this->checkOrderDirection($orderDirection);

        if ($this->_orderBy == 'status') {
            return ' ORDER BY CAST(status AS unsigned)' . $this->_orderWay .
                ($this->_tmpTableFilter ? ') tmpTable WHERE 1' . $this->_tmpTableFilter : '');
        }

        return parent::getOrderByClause($orderBy, $orderDirection);
    }
}
