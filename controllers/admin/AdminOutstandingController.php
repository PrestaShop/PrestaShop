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
 * @property OrderInvoice $object
 */
class AdminOutstandingControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_invoice';
        $this->className = 'OrderInvoice';
        $this->addRowAction('view');

        parent::__construct();

        $this->_select = '`id_order_invoice` AS `id_invoice`,
		`id_order_invoice` AS `outstanding`,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		c.`outstanding_allow_amount`,
		r.`color`,
		c.`company`,
		rl.`name` AS `risk`';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.`id_order` = a.`id_order`)
		LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = o.`id_customer`)
		LEFT JOIN `' . _DB_PREFIX_ . 'risk` r ON (r.`id_risk` = c.`id_risk`)
		LEFT JOIN `' . _DB_PREFIX_ . 'risk_lang` rl ON (r.`id_risk` = rl.`id_risk` AND rl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_where = 'AND number > 0';
        $this->_use_found_rows = false;

        $risks = [];
        foreach (Risk::getRisks() as $risk) {
            /* @var Risk $risk */
            $risks[$risk->id] = $risk->name;
        }

        $this->fields_list = [
            'number' => [
                'title' => $this->trans('Invoice', [], 'Admin.Global'),
            ],
            'date_add' => [
                'title' => $this->trans('Date', [], 'Admin.Global'),
                'type' => 'date',
                'align' => 'right',
                'filter_key' => 'a!date_add',
            ],
            'customer' => [
                'title' => $this->trans('Customer', [], 'Admin.Global'),
                'filter_key' => 'customer',
                'tmpTableFilter' => true,
            ],
            'company' => [
                'title' => $this->trans('Company', [], 'Admin.Global'),
                'align' => 'center',
            ],
            'risk' => [
                'title' => $this->trans('Risk', [], 'Admin.Orderscustomers.Feature'),
                'align' => 'center',
                'orderby' => false,
                'type' => 'select',
                'color' => 'color',
                'list' => $risks,
                'filter_key' => 'r!id_risk',
                'filter_type' => 'int',
            ],
            'outstanding_allow_amount' => [
                'title' => $this->trans('Outstanding Allowance', [], 'Admin.Orderscustomers.Feature'),
                'align' => 'center',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'type' => 'price',
            ],
            'outstanding' => [
                'title' => $this->trans('Current Outstanding', [], 'Admin.Orderscustomers.Feature'),
                'align' => 'center',
                'callback' => 'printOutstandingCalculation',
                'orderby' => false,
                'search' => false,
            ],
            'id_invoice' => [
                'title' => $this->trans('Invoice', [], 'Admin.Global'),
                'align' => 'center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
            ],
        ];
    }

    /**
     * Toolbar initialisation.
     *
     * @return bool Force true (Hide New button)
     */
    public function initToolbar()
    {
        return true;
    }

    /**
     * Column callback for print PDF incon.
     *
     * @param $id_invoice integer Invoice ID
     * @param $tr array Row data
     *
     * @return string HTML content
     */
    public function printPDFIcons($id_invoice, $tr)
    {
        $this->context->smarty->assign([
            'id_invoice' => $id_invoice,
        ]);

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function printOutstandingCalculation($id_invoice, $tr)
    {
        $order_invoice = new OrderInvoice($id_invoice);
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('object OrderInvoice cannot be loaded');
        }
        $order = new Order($order_invoice->id_order);
        if (!Validate::isLoadedObject($order)) {
            throw new PrestaShopException('object Order cannot be loaded');
        }
        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('object Customer cannot be loaded');
        }

        return '<b>' . $this->context->getCurrentLocale()->formatPrice($customer->getOutstanding(), Context::getContext()->currency->iso_code) . '</b>';
    }

    /**
     * View render.
     *
     * @throws PrestaShopException Invalid objects
     */
    public function renderView()
    {
        $order_invoice = new OrderInvoice((int) Tools::getValue('id_order_invoice'));
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('object OrderInvoice cannot be loaded');
        }
        $order = new Order($order_invoice->id_order);
        if (!Validate::isLoadedObject($order)) {
            throw new PrestaShopException('object Order cannot be loaded');
        }

        $parameters = ['vieworder' => 1, 'id_order' => $order->id];
        $link = $this->context->link->getAdminLink('AdminOrders', true, [], $parameters);

        $this->redirect_after = $link;
        $this->redirect();
    }
}
