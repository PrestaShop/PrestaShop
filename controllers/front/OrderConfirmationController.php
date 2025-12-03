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

use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
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
    public function init(): void
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

        /*
         * There is a special case for free orders, when this page does more than just display
         * the confirmation. It also creates the order if it is free. This is done if free_order
         * parameter is passed to this page.
         *
         * After the order is created, we redirect to the same page without free_order parameter
         * and display the confirmation as usual.
         */
        if ((bool) Tools::getValue('free_order') === true) {
            $this->checkFreeOrder();
        }

        /*
         * Because of order splitting scenarios, we must get the data by id_cart parameter (not id_order),
         * so we can display all orders made from this cart.
         *
         * It's not implemented yet, however, and probably won't be, because we are switching to a new
         * logic of multiple shipments per order, which doesn't require splitting orders anymore.
         */
        $this->id_order = Order::getIdByCartId((int) $this->id_cart);
        if (empty($this->id_order)) {
            Tools::redirect('pagenotfound');
        }

        // Now, load the order object and check validity
        $this->order = new Order((int) $this->id_order);
        if (!Validate::isLoadedObject($this->order)) {
            Tools::redirect('pagenotfound');
        }

        /*
         * Now, to prevent users from seeing other customers' order confirmations, we are using
         * a secure key mechanism. The confirmation link contains a unique key which is also saved
         * in database when the order is created. If the key from the URL doesn't match the one
         * in database, we redirect to "page not found".
         */
        $this->secure_key = Tools::getValue('key', false);
        if (empty($this->secure_key) || $this->secure_key != $this->order->secure_key) {
            Tools::redirect('pagenotfound');
        }

        // Last step, initialize some other data
        $this->id_module = $this->order->module == 'free_order' ? -1 : Module::getModuleIdByName($this->order->module);

        // This data is kept only for backward compatibility purposes
        $this->reference = (string) $this->order->reference;

        // If checks passed, initialize customer, we will need him anyway
        $this->customer = new Customer((int) $this->order->id_customer);
    }

    /**
     * Logic after submitting forms
     *
     * @see FrontController::postProcess()
     */
    public function postProcess(): void
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

            // Prevent error
            // A) either on page refresh
            // B) if we already transformed him in other window or through backoffice
            if ($this->customer->is_guest == 0) {
                $this->errors[] = $this->trans(
                    'A customer account has already been created from this guest account. Please sign in.',
                    [],
                    'Shop.Notifications.Error'
                );
            // Check if a different customer with the same email was not already created in a different window or through backoffice
            } elseif (Customer::customerExists($this->customer->email)) {
                $this->errors[] = $this->trans(
                    'You can\'t transform your account into a customer account, because a registered customer with the same email already exists.',
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
    public function initContent(): void
    {
        parent::initContent();

        /** @var FeatureFlagStateCheckerInterface $featureFlagManager */
        $featureFlagManager = $this->get(FeatureFlagStateCheckerInterface::class);

        $this->context->smarty->assign([
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation($this->order),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn($this->order),
            'order' => (new OrderPresenter())->present($this->order),
            'order_customer' => $this->objectPresenter->present($this->customer),
            'is_multishipment_enabled' => $featureFlagManager->isEnabled(FeatureFlagSettings::FEATURE_FLAG_IMPROVED_SHIPMENT),
            'registered_customer_exists' => Customer::customerExists($this->customer->email),
        ]);
        $this->setTemplate('checkout/order-confirmation');

        // If logged in guest we clear the cookie for security reasons
        if ($this->context->customer->is_guest) {
            $this->context->customer->mylogout();
        }
    }

    /**
     * Execute the hook displayPaymentReturn. This hook should be used to display payment
     * information on the order confirmation page. Payment status, instructions, QR code etc.
     */
    public function displayPaymentReturn(Order $order)
    {
        // Check if we have a sensible module ID. Free orders have -1 as module ID
        if (!Validate::isUnsignedId($this->id_module)) {
            return false;
        }

        // Hook called only for the module concerned
        return Hook::exec('displayPaymentReturn', ['order' => $order], $this->id_module);
    }

    /**
     * Execute the hook displayOrderConfirmation.
     */
    public function displayOrderConfirmation(Order $order)
    {
        return Hook::exec('displayOrderConfirmation', ['order' => $order]);
    }

    /**
     * Check if an order is free and create it. After creation, we redirect to the same page
     * which will display the order confirmation as usual.
     */
    protected function checkFreeOrder(): void
    {
        /*
         * Verify if this is not a faulty or duplicate call. If an order
         * already exists for this cart, we do not create another one.
         */
        if (!empty(Order::getIdByCartId((int) $this->id_cart))) {
            return;
        }

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

        /*
         * Redirect back to this page to display the order confirmation.
         * Note the id_module parameter with value -1, it's only kept for
         * backward compatibility, but not used anymore.
         */
        Tools::redirect($this->context->link->getPageLink(
            'order-confirmation',
            null,
            null,
            [
                'id_cart' => (int) $cart->id,
                'id_module' => '-1',
                'id_order' => (int) $order->currentOrder,
                'key' => $cart->secure_key,
            ]
        ));
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Order confirmation', [], 'Shop.Theme.Checkout'),
            'url' => $this->context->link->getPageLink('order-confirmation'),
        ];

        return $breadcrumb;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
