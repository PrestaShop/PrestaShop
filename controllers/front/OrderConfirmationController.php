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

class OrderConfirmationControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order-confirmation';
    public $id_cart;
    public $id_module;
    public $id_order;
    public $reference;
    public $secure_key;
    public $order_presenter;

    /**
     * Initialize order confirmation controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if (true === (bool) Tools::getValue('free_order')) {
            $this->checkFreeOrder();
        }

        $this->id_cart = (int) (Tools::getValue('id_cart', 0));

        $redirectLink = 'index.php?controller=history';

        $this->id_module = (int) (Tools::getValue('id_module', 0));
        $this->id_order = Order::getIdByCartId((int) ($this->id_cart));
        $this->secure_key = Tools::getValue('key', false);
        $order = new Order((int) ($this->id_order));

        if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key)) {
            Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
        }
        $this->reference = $order->reference;
        if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key) {
            Tools::redirect($redirectLink);
        }
        $module = Module::getInstanceById((int) ($this->id_module));
        if ($order->module != $module->name) {
            Tools::redirect($redirectLink);
        }
        $this->order_presenter = new OrderPresenter();
    }

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

        $order = new Order(Order::getIdByCartId((int) ($this->id_cart)));
        $presentedOrder = $this->order_presenter->present($order);
        $register_form = $this
            ->makeCustomerForm()
            ->setGuestAllowed(false)
            ->fillWith(Tools::getAllValues());

        parent::initContent();

        $this->context->smarty->assign(array(
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation($order),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn($order),
            'order' => $presentedOrder,
            'register_form' => $register_form,
        ));

        if ($this->context->customer->is_guest) {
            /* If guest we clear the cookie for security reason */
            $this->context->customer->mylogout();
        }
        $this->setTemplate('checkout/order-confirmation');
    }

    /**
     * Execute the hook displayPaymentReturn.
     */
    public function displayPaymentReturn($order)
    {
        if (!Validate::isUnsignedId($this->id_module)) {
            return false;
        }

        return Hook::exec('displayPaymentReturn', array('order' => $order), $this->id_module);
    }

    /**
     * Execute the hook displayOrderConfirmation.
     */
    public function displayOrderConfirmation($order)
    {
        return Hook::exec('displayOrderConfirmation', array('order' => $order));
    }

    /**
     * Check if an order is free and create it
     */
    private function checkFreeOrder()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        if ($total > 0) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $order = new PaymentFree();
        $order->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_PAYMENT'),
            0,
            $this->trans('Free order', array(), 'Admin.Orderscustomers.Feature'),
            null, array(), null, false,
            $cart->secure_key
        );

    }
}
