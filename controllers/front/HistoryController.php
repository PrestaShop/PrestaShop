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

use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;

class HistoryControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'history';
    public $authRedirection = 'history';
    public $ssl = true;
    public $order_presenter;

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

        if ($this->order_presenter === null) {
            $this->order_presenter = new OrderPresenter();
        }

        if (Tools::isSubmit('slowvalidation')) {
            $this->warning[] = $this->trans('If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.', array(), 'Shop.Notifications.Warning');
        }

        $orders = $this->getTemplateVarOrders();

        if (count($orders) <= 0) {
            $this->warning[] = $this->trans('You have not placed any orders.', array(), 'Shop.Notifications.Warning');
        }

        $this->context->smarty->assign(array(
            'orders' => $orders,
        ));

        parent::initContent();
        $this->setTemplate('customer/history');
    }

    public function getTemplateVarOrders()
    {
        $orders = array();
        $customer_orders = Order::getCustomerOrders($this->context->customer->id);
        foreach ($customer_orders as $customer_order) {
            $order = new Order((int) $customer_order['id_order']);
            $orders[$customer_order['id_order']] = $this->order_presenter->present($order);
        }

        return $orders;
    }

    public static function getUrlToInvoice($order, $context)
    {
        $url_to_invoice = '';

        if ((bool) Configuration::get('PS_INVOICE') && OrderState::invoiceAvailable($order->current_state) && count($order->getInvoicesCollection())) {
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
        if (!(bool) Configuration::get('PS_DISALLOW_HISTORY_REORDERING')) {
            $url_to_reorder = $context->link->getPageLink('order', true, null, 'submitReorder&id_order='.(int) $id_order);
        }

        return $url_to_reorder;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
