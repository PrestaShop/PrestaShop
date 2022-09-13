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
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use ZxcvbnPhp\Zxcvbn;

class OrderConfirmationControllerCore extends FrontController
{
    /** @var bool */
    public $ssl = true;
    /** @var string */
    public $php_self = 'order-confirmation';
    /** @var int Cart ID */
    public $id_cart;
    public $id_module;
    public $id_order;
    public $secure_key;
    /** @var Order Order object we found by cart ID */
    protected $order;
    /** @var Customer Customer object related to the cart */
    protected $customer;
    public $reference; // Deprecated
    public $order_presenter; // Deprecated

    /**
     * Initialize order confirmation controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        // Test below to prevent unnecessary logs from "parent::init()"
        $this->id_cart = (int) Tools::getValue('id_cart', 0);
        if (!empty($this->context->cookie->id_cart) && $this->context->cookie->id_cart == $this->id_cart) {
            $cart = new Cart($this->id_cart);
            if ($cart->orderExists()) {
                unset($this->context->cookie->id_cart);
            }
        }

        parent::init();

        // If we are coming to this page to finish free order we do extra checks and validations
        // and redirect back here with bit more data.
        if (true === (bool) Tools::getValue('free_order')) {
            $this->checkFreeOrder();
        }

        /*
         * Because of order splitting scenarios, we must get the data by id_cart parameter (not id_order),
         * so we can display all orders made from this cart.
         *
         * It's not implemented yet, however.
         */
        $this->id_order = Order::getIdByCartId((int) ($this->id_cart));
        $this->secure_key = Tools::getValue('key', false);
        $this->order = new Order((int) ($this->id_order));
        $this->id_module = (int) (Tools::getValue('id_module', 0));

        // This data is kept only for backward compatibility purposes
        $this->reference = (string) $this->order->reference;

        $redirectLink = $this->context->link->getPageLink('history', $this->ssl);

        // The confirmation link must contain a unique order secure key matching the key saved in database,
        // this prevents user to view other customer's order confirmations
        if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key)) {
            Tools::redirect($redirectLink . (Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
        }

        if (!Validate::isLoadedObject($this->order) || $this->secure_key != $this->order->secure_key) {
            Tools::redirect($redirectLink);
        }

        // Free order uses -1 as id_module, it has a special check here
        if ($this->id_module == -1) {
            if ($this->order->module !== 'free_order') {
                Tools::redirect($redirectLink);
            }
        } else {
            // Otherwise we run a normal check that module matches
            $module = Module::getInstanceById((int) ($this->id_module));
            if ($this->order->module !== $module->name) {
                Tools::redirect($redirectLink);
            }
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
                    'Enter a password to transform your guest account into a customer account.',
                    [],
                    'Shop.Forms.Help'
                );
            } else {
                if (Validate::isAcceptablePasswordLength($password) === false) {
                    $this->errors[] = $this->translator->trans(
                        'Password must be between %d and %d characters long',
                        [
                            Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH),
                            Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH),
                        ],
                        'Shop.Notifications.Error'
                    );
                }
                if (Validate::isAcceptablePasswordScore($password) === false) {
                    $wordingsForScore = [
                        $this->translator->trans('Very weak', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Weak', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Average', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Strong', [], 'Shop.Theme.Global'),
                        $this->translator->trans('Very strong', [], 'Shop.Theme.Global'),
                    ];
                    $globalErrorMessage = $this->translator->trans(
                        'The minimum score must be: %s',
                        [
                            $wordingsForScore[(int) Configuration::get(PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE)],
                        ],
                        'Shop.Notifications.Error'
                    );
                    if ($this->context->shop->theme->get('global_settings.new_password_policy_feature') !== true) {
                        $zxcvbn = new Zxcvbn();
                        $result = $zxcvbn->passwordStrength($password);
                        if (!empty($result['feedback']['warning'])) {
                            $this->errors[] = $this->translator->trans(
                                $result['feedback']['warning'], [], 'Shop.Theme.Global'
                            );
                        } else {
                            $this->errors[] = $globalErrorMessage;
                        }
                        foreach ($result['feedback']['suggestions'] as $suggestion) {
                            $this->errors[] = $this->translator->trans($suggestion, [], 'Shop.Theme.Global');
                        }
                    } else {
                        $this->errors[] = $globalErrorMessage;
                    }
                }
            }

            if (!empty($this->errors)) {
                return;
            }

            if ($this->customer->is_guest == 0) {
                $this->errors[] = $this->trans(
                    'A customer account has already been created from this guest account. Please sign in.',
                    [],
                    'Shop.Notifications.Error'
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
        parent::initContent();

        $this->context->smarty->assign([
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation($this->order),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn($this->order),
            'order' => (new OrderPresenter())->present($this->order),
            'order_customer' => (new ObjectPresenter())->present($this->customer),
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
            (int) Configuration::get('PS_OS_PAYMENT'),
            0,
            $this->trans('Free order', [], 'Admin.Orderscustomers.Feature'),
            null,
            [],
            null,
            false,
            $cart->secure_key
        );

        // redirect back to us with rest of the data
        // note the id_module parameter with value -1
        // it acts as a marker for the module check to use "free_payment"
        // for the check
        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . (int) $cart->id . '&id_module=-1&id_order=' . (int) $order->currentOrder . '&key=' . $cart->secure_key);
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
