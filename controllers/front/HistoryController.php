<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class HistoryControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'history';
    public $authRedirection = 'history';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('slowvalidation')) {
            $this->warning[] = $this->l('If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.');
        }

        $orders = $this->getTemplateVarOrders();

        if (count($orders) <= 0) {
            $this->warning[] = $this->l('You have not placed any orders.');
        }

        $this->context->smarty->assign([
            'orders' => $orders,
        ]);

        $this->setTemplate('customer/history.tpl');
    }

    public function getTemplateVarOrders()
    {
        $orders = [];
        $customer_orders = Order::getCustomerOrders($this->context->customer->id);
        foreach ($customer_orders as $customer_order) {
            $myOrder = new Order((int)$customer_order['id_order']);
            if (Validate::isLoadedObject($myOrder)) {
                $orders[$customer_order['id_order']] = $customer_order;
                $orders[$customer_order['id_order']]['virtual'] = $myOrder->isVirtual(false);
                $orders[$customer_order['id_order']]['reference'] = Order::getUniqReferenceOf($customer_order['id_order']);
                $orders[$customer_order['id_order']]['order_date'] = Tools::displayDate($customer_order['date_add'], null, false);
                $orders[$customer_order['id_order']]['total_price'] = Tools::displayPrice($customer_order['total_paid'], (int)$customer_order['id_currency']);
                $orders[$customer_order['id_order']]['contrast'] = (Tools::getBrightness($customer_order['order_state_color']) > 128) ? 'dark' : 'bright';
                $orders[$customer_order['id_order']]['url_to_invoice'] = HistoryController::getUrlToInvoice($myOrder, $this->context);
                $orders[$customer_order['id_order']]['url_details'] = $this->context->link->getPageLink('order-detail', true, null, 'id_order='.(int)$customer_order['id_order']);
                $orders[$customer_order['id_order']]['url_to_reorder'] = HistoryController::getUrlToReorder((int)$customer_order['id_order'], $this->context);
            }
        }

        return $orders;
    }

    public static function getUrlToInvoice($order, $context)
    {
        $url_to_invoice = '';

        if ((bool)Configuration::get('PS_INVOICE') && OrderState::invoiceAvailable($order->current_state) && count($order->getInvoicesCollection())) {
            $url_to_invoice = $context->link->getPageLink('pdf-invoice', true, null, 'id_order='.$order->id);
            if ($context->cookie->is_guest) {
                $url_to_invoice .= '&amp;secure_key='.$order->secure_key;
            }
        }

        return $url_to_invoice;
    }

    public static function getUrlToReorder($id_order, $context)
    {
        $url_to_reorder = '';
        if (!(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING')) {
            $url_to_reorder = $context->link->getPageLink('order', true, null, 'submitReorder&id_order='.(int)$id_order);
        }

        return $url_to_reorder;
    }

    public function getBreadcrumb()
    {
        $breadcrumb = parent::getBreadcrumb();

        $breadcrumb[] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
