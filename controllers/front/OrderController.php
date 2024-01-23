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

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;
use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;
use PrestaShopBundle\Translation\TranslatorComponent;

class OrderControllerCore extends FrontController
{
    /** @var bool */
    public $ssl = true;
    /** @var string */
    public $php_self = 'order';
    /** @var string */
    public $page_name = 'checkout';
    public $checkoutWarning = [];

    /**
     * @var CheckoutProcess
     */
    protected $checkoutProcess;

    /**
     * @var CartChecksum
     */
    protected $cartChecksum;

    /**
     * Overrides the same parameter in FrontController
     *
     * @var bool automaticallyAllocateInvoiceAddress
     */
    protected $automaticallyAllocateInvoiceAddress = false;

    /**
     * Overrides the same parameter in FrontController
     *
     * @var bool
     */
    protected $automaticallyAllocateDeliveryAddress = false;

    /**
     * Initialize order controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        $this->cartChecksum = new CartChecksum(new AddressChecksum());
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('submitReorder')
            && $this->context->customer->isLogged()
            && $id_order = (int) Tools::getValue('id_order')
        ) {
            $oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
            $duplication = $oldCart->duplicate();
            if (!$duplication || !Validate::isLoadedObject($duplication['cart'])) {
                $this->errors[] = $this->trans('Sorry. We cannot renew your order.', [], 'Shop.Notifications.Error');
            } elseif (!$duplication['success']) {
                $this->errors[] = $this->trans(
                    'Some items are no longer available, and we are unable to renew your order.',
                    [],
                    'Shop.Notifications.Error'
                );
            } else {
                $this->context->cookie->id_cart = $duplication['cart']->id;
                $context = $this->context;
                $context->cart = $duplication['cart'];
                CartRule::autoAddToCart($context);
                $this->context->cookie->write();
                Tools::redirect('index.php?controller=order');
            }
        }

        $this->bootstrap();
    }

    /**
     * @return CheckoutProcess
     */
    public function getCheckoutProcess()
    {
        return $this->checkoutProcess;
    }

    /**
     * @return CheckoutSession
     */
    public function getCheckoutSession()
    {
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new PriceFormatter()
        );

        $session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );

        return $session;
    }

    protected function bootstrap()
    {
        $translator = $this->getTranslator();
        $session = $this->getCheckoutSession();

        $this->checkoutProcess = $this->buildCheckoutProcess($session, $translator);
        Hook::exec('actionCheckoutRender', ['checkoutProcess' => &$this->checkoutProcess]);
    }

    /**
     * Persists cart-related data in checkout session.
     *
     * @param CheckoutProcess $process
     */
    protected function saveDataToPersist(CheckoutProcess $process)
    {
        $data = $process->getDataToPersist();
        $cart = $this->context->cart;

        $data['checksum'] = $this->cartChecksum->generateChecksum($cart);

        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'cart SET checkout_session_data = "' . pSQL(json_encode($data)) . '"
                WHERE id_cart = ' . (int) $cart->id
        );
    }

    /**
     * Restores from checkout session some previously persisted cart-related data.
     *
     * @param CheckoutProcess $process
     */
    protected function restorePersistedData(CheckoutProcess $process)
    {
        $cart = $this->context->cart;
        $customer = $this->context->customer;
        $rawData = Db::getInstance()->getValue(
            'SELECT checkout_session_data FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . (int) $cart->id
        );
        $data = json_decode($rawData ?? '', true);
        if (!is_array($data)) {
            $data = [];
        }

        $addressValidator = new AddressValidator();
        $invalidAddressIds = $addressValidator->validateCartAddresses($cart);

        // Build the currently selected address' warning message (if relevant)
        if (!$customer->isGuest() && !empty($invalidAddressIds)) {
            $this->checkoutWarning['address'] = [
                'id_address' => (int) reset($invalidAddressIds),
                'exception' => $this->trans(
                    'Your address is incomplete, please update it.',
                    [],
                    'Shop.Notifications.Error'
                ),
            ];
        }

        // Prevent check for guests
        if ($customer->id) {
            // Prepare all other addresses' warning messages (if relevant).
            // These messages are displayed when changing the selected address.
            $allInvalidAddressIds = $addressValidator->validateCustomerAddresses($customer, $this->context->language);
            $this->checkoutWarning['invalid_addresses'] = $allInvalidAddressIds;
        }

        if (isset($data['checksum']) && $data['checksum'] === $this->cartChecksum->generateChecksum($cart)) {
            $process->restorePersistedData($data);
        }
    }

    public function displayAjaxselectDeliveryOption()
    {
        $cart = $this->cart_presenter->present(
            $this->context->cart,
            true
        );

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode([
            'preview' => $this->render('checkout/_partials/cart-summary', [
                'cart' => $cart,
                'static_token' => Tools::getToken(false),
            ]),
        ]));
    }

    public function displayAjaxCheckCartStillOrderable(): void
    {
        $responseData = [
            'errors' => false,
            'cartUrl' => '',
        ];

        if ($this->context->cart->isAllProductsInStock() !== true ||
            $this->context->cart->checkAllProductsAreStillAvailableInThisState() !== true ||
            $this->context->cart->checkAllProductsHaveMinimalQuantities() !== true) {
            $responseData['errors'] = true;
            $responseData['cartUrl'] = $this->context->link->getPageLink('cart', null, null, ['action' => 'show']);
        }

        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($responseData));
    }

    public function initContent()
    {
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $this->restorePersistedData($this->checkoutProcess);
        $this->checkoutProcess->handleRequest(
            Tools::getAllValues()
        );

        $presentedCart = $this->cart_presenter->present($this->context->cart, true);

        $shouldRedirectToCart = false;

        // Check the cart meets minimal order amount treshold
        // Check that the cart is not empty
        if (count($presentedCart['products']) <= 0 || $presentedCart['minimalPurchaseRequired']) {
            $shouldRedirectToCart = true;
        }

        // Check that products are still orderable, at any point in checkout
        if ($this->context->cart->isAllProductsInStock() !== true ||
            $this->context->cart->checkAllProductsAreStillAvailableInThisState() !== true ||
            $this->context->cart->checkAllProductsHaveMinimalQuantities() !== true) {
            $shouldRedirectToCart = true;
        }

        // If there was a problem, we redirect the user to cart, CartController deals with display of detailed errors
        // We don't redirect in case of ajax requests, so we can get our response
        if ($shouldRedirectToCart === true && !$this->ajax) {
            $cartLink = $this->context->link->getPageLink('cart', null, null, ['action' => 'show']);
            $this->redirectWithNotifications($cartLink);
        }

        $this->checkoutProcess
            ->setNextStepReachable()
            ->markCurrentStep()
            ->invalidateAllStepsAfterCurrent();

        $this->saveDataToPersist($this->checkoutProcess);

        if (!$this->checkoutProcess->hasErrors()) {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET' && !$this->ajax) {
                return $this->redirectWithNotifications(
                    $this->checkoutProcess->getCheckoutSession()->getCheckoutURL()
                );
            }
        }

        $this->context->smarty->assign([
            'checkout_process' => new RenderableProxy($this->checkoutProcess),
            'display_transaction_updated_info' => Tools::getIsset('updatedTransaction'),
            'tos_cms' => $this->getDefaultTermsAndConditions(),
        ]);

        parent::initContent();
        $this->setTemplate('checkout/checkout');
    }

    public function displayAjaxAddressForm()
    {
        $addressForm = $this->makeAddressForm();

        if (Tools::getIsset('id_address') && ($id_address = (int) Tools::getValue('id_address'))) {
            $addressForm->loadAddressById($id_address);
        }

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(['id_country' => Tools::getValue('id_country')]);
        }

        $stepTemplateParameters = [];
        foreach ($this->checkoutProcess->getSteps() as $step) {
            if ($step instanceof CheckoutAddressesStep) {
                $stepTemplateParameters = $step->getTemplateParameters();
            }
        }

        $templateParams = array_merge(
            $addressForm->getTemplateVariables(),
            $stepTemplateParameters,
            ['type' => 'delivery']
        );

        ob_end_clean();
        header('Content-Type: application/json');

        $this->ajaxRender(json_encode([
            'address_form' => $this->render(
                'checkout/_partials/address-form',
                $templateParams
            ),
        ]));
    }

    /**
     * Return default TOS link for checkout footer
     *
     * @return string|bool
     */
    protected function getDefaultTermsAndConditions()
    {
        $cms = new CMS((int) Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);

        if (!Validate::isLoadedObject($cms)) {
            return false;
        }

        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite);

        $termsAndConditions = new TermsAndConditions();
        $termsAndConditions
            ->setText(
                '[' . $cms->meta_title . ']',
                $link
            )
            ->setIdentifier('terms-and-conditions-footer');

        return $termsAndConditions->format();
    }

    /**
     * @param CheckoutSession $session
     * @param TranslatorComponent $translator
     *
     * @return CheckoutProcess
     */
    protected function buildCheckoutProcess(CheckoutSession $session, $translator)
    {
        $checkoutProcess = new CheckoutProcess(
            $this->context,
            $session
        );

        $checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context,
                $translator,
                $this->makeLoginForm(),
                $this->makeCustomerForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context,
                $translator,
                $this->makeAddressForm()
            ));

        if (!$this->context->cart->isVirtualCart()) {
            $checkoutDeliveryStep = new CheckoutDeliveryStep(
                $this->context,
                $translator
            );

            $checkoutDeliveryStep
                ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
                ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
                ->setIncludeTaxes(
                    !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                    && (int) Configuration::get('PS_TAX')
                )
                ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
                ->setGiftCost(
                    $this->context->cart->getGiftWrappingPrice(
                        $checkoutDeliveryStep->getIncludeTaxes()
                    )
                );

            $checkoutProcess->addStep($checkoutDeliveryStep);
        }

        $checkoutProcess
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $translator,
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $translator
                )
            ));

        return $checkoutProcess;
    }
}
