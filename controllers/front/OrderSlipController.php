<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class OrderSlipControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-slip';
    public $authRedirection = 'order-slip';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $credit_slips = $this->getTemplateVarCreditSlips();

        if (count($credit_slips) <= 0) {
            $this->warning[] = $this->trans('You have not received any credit slips.', array(), 'Shop.Notifications.Warning');
        }

        $this->context->smarty->assign([
            'credit_slips' => $credit_slips,
        ]);

        parent::initContent();
        $this->setTemplate('customer/order-slip');
    }

    public function getTemplateVarCreditSlips()
    {
        $credit_slips = [];
        $orders_slip = OrderSlip::getOrdersSlip(((int)$this->context->cookie->id_customer));

        foreach ($orders_slip as $order_slip) {
            $order = new Order($order_slip['id_order']);
            $credit_slips[$order_slip['id_order_slip']] = $order_slip;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_number'] = sprintf($this->trans('#%06d', array(), 'Shop.Theme.Customeraccount'), $order_slip['id_order_slip']);
            $credit_slips[$order_slip['id_order_slip']]['order_number'] = sprintf($this->trans('#%06d', array(), 'Shop.Theme.Customeraccount'), $order_slip['id_order']);
            $credit_slips[$order_slip['id_order_slip']]['order_reference'] = $order->reference;
            $credit_slips[$order_slip['id_order_slip']]['credit_slip_date'] = Tools::displayDate($order_slip['date_add'], null, false);
            $credit_slips[$order_slip['id_order_slip']]['url'] = $this->context->link->getPageLink('pdf-order-slip', true, null, 'id_order_slip='.(int)$order_slip['id_order_slip']);
            $credit_slips[$order_slip['id_order_slip']]['order_url_details'] = $this->context->link->getPageLink('order-detail', true, null, 'id_order='.(int)$order_slip['id_order']);
        }
        return $credit_slips;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
