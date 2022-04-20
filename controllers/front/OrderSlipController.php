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
class OrderSlipControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'order-slip';
    /** @var string */
    public $authRedirection = 'order-slip';
    /** @var bool */
    public $ssl = true;

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $this->context->smarty->assign([
            'credit_slips' => $this->getTemplateVarCreditSlips(),
        ]);

        parent::initContent();
        $this->setTemplate('customer/order-slip');
    }

    public function getTemplateVarCreditSlips()
    {
        $credit_slips = [];
        $orders_slip = OrderSlip::getOrdersSlip(((int) $this->context->cookie->id_customer));

        foreach ($orders_slip as $order_slip) {
            $order = new Order($order_slip['id_order']);
            $credit_slips[$order_slip['id_order_slip']] = $order_slip;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_number'] = $this->trans('#%id%', ['%id%' => $order_slip['id_order_slip']], 'Shop.Theme.Customeraccount');
            $credit_slips[$order_slip['id_order_slip']]['order_number'] = $this->trans('#%id%', ['%id%' => $order_slip['id_order']], 'Shop.Theme.Customeraccount');
            $credit_slips[$order_slip['id_order_slip']]['order_reference'] = $order->reference;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_date'] = Tools::displayDate($order_slip['date_add'], false);
            $credit_slips[$order_slip['id_order_slip']]['url'] = $this->context->link->getPageLink('pdf-order-slip', true, null, 'id_order_slip=' . (int) $order_slip['id_order_slip']);
            $credit_slips[$order_slip['id_order_slip']]['order_url_details'] = $this->context->link->getPageLink('order-detail', true, null, 'id_order=' . (int) $order_slip['id_order']);
        }

        return $credit_slips;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Credit slips', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('order-slip'),
        ];

        return $breadcrumb;
    }
}
