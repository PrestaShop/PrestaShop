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
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;

class OrderConfirmationControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order-confirmation';
    public $id_module;
    public $id_order;
    public $secure_key;
    protected $order;
    protected $customer;
    public $reference; // Deprecated
    public $id_cart; // Deprecated
    public $order_presenter; // Deprecated

    /**
     * Initialize order confirmation controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        // If are coming to this page to finish free order, we need to run additional logic
        if (true === (bool) Tools::getValue('free_order')) {
            $this->checkFreeOrder();
        }

        $this->id_module = (int) (Tools::getValue('id_module', 0));
        $this->id_order = (int) (Tools::getValue('id_order', 0));
        $this->secure_key = Tools::getValue('key', false);
        $this->order = new Order((int) ($this->id_order));

        // This data is kept only for backward compatibility purposes
        $this->id_cart = (int) $this->order->id_cart;
        $this->reference = (string) $this->order->reference;

        // The confirmation link must contain a unique order secure key matching the key saved in database,
        // this prevents user to view other customer's order confirmations
        if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key)) {
            Tools::redirect($redirectLink . (Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
        }
        $redirectLink = $this->context->link->getPageLink('history', $this->ssl);
        if (!Validate::isLoadedObject($this->order) || $this->secure_key != $this->order->secure_key) {
            Tools::redirect($redirectLink);
        }
        $module = Module::getInstanceById((int) ($this->id_module));
        if ($this->order->module != $module->name) {
            Tools::redirect($redirectLink);
        }

        // If checks passed, initialize customer, we will need him anyway
        $this->customer = new Customer((int) ($this->order->id_customer));
    }

    /**
     * Logic after submitting forms
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitTransformGuestToCustomer')) {
            // Only variable we need is the password
            // There is no need to check other variables, because hacker would be kicked out in init(), if he tried to convert another customer
            $password = Tools::getValue('password');

            if (empty($password)) {
                $this->errors[] = $this->trans(
                    'To convert your account, you must enter a password.',
                    [],
                    'Shop.Forms.Help'
                );
            } elseif (strlen($password) < Validate::PASSWORD_LENGTH) {
                $this->errors[] = $this->trans(
                    'Your password must be at least %min% characters long.',
                    ['%min%' => Validate::PASSWORD_LENGTH],
                    'Shop.Forms.Help'
                );
            // Prevent error
            // A) either on page refresh
            // B) if we already transformed him in other window or through backoffice
            } elseif ($this->customer->is_guest == 0) {
                $this->success[] = $this->trans(
                    'Your guest account has been already transformed into a customer account. You can log in as a registered shopper.',
                    [],
                    'Shop.Notifications.Success'
                );
            // Attempt to convert the customer
            } elseif ($this->customer->transformToCustomer($this->context->language->id, $password)) {
                $this->success[] = $this->trans(
                    'Your guest account has been successfully transformed into a customer account. You can now log in as a registered shopper.',
                    [],
                    'Shop.Notifications.Success'
                );
            } else {
                $this->errors[] = $this->trans(
                    'An unexpected error occurred while creating your account.',
                    [],
                    'Shop.Notifications.Error'
                );
            }
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        // Initialize presenters
        $order_presenter = new OrderPresenter();
        $object_presenter = new ObjectPresenter();

        parent::initContent();

        // Assign data to output and set template
        $this->context->smarty->assign([
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation($this->order),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn($this->order),
            'order' => $order_presenter->present($this->order),
            'order_customer' => $object_presenter->present($this->customer),
            'registered_customer_exists' => Customer::customerExists($this->customer->email, false, true),
        ]);
        $this->setTemplate('checkout/order-confirmation');

        // If logged in guest we clear the cookie for security reasons
        if ($this->context->customer->is_guest) {
            $this->context->customer->mylogout();
        }
    }

    /**
     * Execute the hook displayPaymentReturn.
     */
    public function displayPaymentReturn($order)
    {
        if (!Validate::isUnsignedId($this->id_module)) {
            return false;
        }

        return Hook::exec('displayPaymentReturn', ['order' => $order], $this->id_module);
    }

    /**
     * Execute the hook displayOrderConfirmation.
     */
    public function displayOrderConfirmation($order)
    {
        return Hook::exec('displayOrderConfirmation', ['order' => $order]);
    }

    /**
     * Check if an order is free and create it.
     */
    protected function checkFreeOrder()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        if ($total > 0) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $order = new PaymentFree();
        $order->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_PAYMENT'),
            0,
            $this->trans('Free order', [], 'Admin.Orderscustomers.Feature'),
            null,
            [],
            null,
            false,
            $cart->secure_key
        );
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Order confirmation', [], 'Shop.Theme.Checkout'),
            'url' => $this->context->link->getPageLink('order-confirmation'),
        ];

        return $breadcrumb;
    }
}
