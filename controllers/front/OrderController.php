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

use PrestaShop\PrestaShop\Core\Business\Checkout\TermsAndConditions;

class OrderControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order';
    public $page_name = 'checkout';
    public $address;

    private $address_formatter;
    private $address_form;
    private $address_fields;

    private function render($template, array $params)
    {
        $this->context->smarty->assign($params);
        return $this->context->smarty->fetch($template);
    }

    private function getConditionsToApprove()
    {
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));

        $termsAndConditions = new TermsAndConditions;
        $termsAndConditions
            ->setText(
                $this->l('I agree to the [terms of service] and will adhere to them unconditionally.'),
                $link
            )
            ->setIdentifier('terms-and-conditions')
        ;

        $allConditions = Hook::exec('termsAndConditions', [], null, true);
        if (!is_array($allConditions)) {
            $allConditions = [];
        }

        array_unshift($allConditions, $termsAndConditions);

        // TODO StarterTheme : currently we don't handle opening the link inside a modal.
        return TermsAndConditions::formatForTemplate($allConditions);
    }

    protected function renderDeliveryOptions()
    {
        if (Tools::getValue('delivery_option')) {
            $this->context->cart->setDeliveryOption(Tools::getValue('delivery_option'));
        } else {
            $this->setDefaultCarrierSelection();
        }

        if (!$this->context->cart->update()) {
            return false;
        }

        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
        $include_taxes = !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer) && (int)Configuration::get('PS_TAX');
        $display_taxes_label = ((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')) && $this->context->smarty->tpl_vars['display_tax_label']->value);
        $pricePresenter = new Adapter_PricePresenter();

        $carriers_available = array();

        if (isset($delivery_option_list[$this->context->cart->id_address_delivery])) {
            foreach ($delivery_option_list[$this->context->cart->id_address_delivery] as $id_carriers_list => $carriers_list) {
                foreach ($carriers_list as $carriers) {
                    if (is_array($carriers)) {
                        foreach ($carriers as $carrier) {
                            $carrier = array_merge($carrier, $this->objectSerializer->toArray($carrier['instance']));
                            $delay = $carrier['delay'][$this->context->language->id];
                            unset($carrier['instance'], $carrier['delay']);
                            $carrier['delay'] = $delay;
                            if ($this->isFreeShipping($this->context->cart, $carriers_list)) {
                                $carrier['price'] = $this->l('Free');
                            } else {
                                if ($include_taxes) {
                                    $carrier['price'] = $pricePresenter->convertAndFormat($carriers_list['total_price_with_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = sprintf($this->l('%s tax incl.'), $carrier['price']);
                                    }
                                } else {
                                    $carrier['price'] = $pricePresenter->convertAndFormat($carriers_list['total_price_without_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = sprintf($this->l('%s tax excl.'), $carrier['price']);
                                    }
                                }
                            }

                            if (count($carriers) > 1) {
                                $carrier['label'] = $carrier['price'];
                            } else {
                                $carrier['label'] = $carrier['name'].' - '.$carrier['delay'].' - '.$carrier['price'];
                            }

                            $carriers_available[$id_carriers_list] = $carrier;
                        }
                    }
                }
            }

            $vars = [
                'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', [
                    'delivery_option_list' => $delivery_option_list,
                    'delivery_option' => $delivery_option
                ]),
            ];

            Cart::addExtraCarriers($vars);
            return $this->render('checkout/delivery.tpl', array_merge([
                'carriers_available' => $carriers_available,
                'id_address' => $this->context->cart->id_address_delivery,
                'delivery_option' => current($delivery_option),
            ], $vars));
        } else {
            return $this->render('checkout/delivery.tpl', [
                'HOOK_BEFORECARRIER' => null,
                'carriers_available' => []
            ]);
        }
    }

    protected function setDefaultCarrierSelection()
    {
        if (!$this->context->cart->getDeliveryOption(null, true)) {
            $this->context->cart->setDeliveryOption($this->context->cart->getDeliveryOption());
        }
    }

    protected function selectDeliveryOptionAction()
    {
        return $this->renderDeliveryOptions();
    }

    protected function isFreeShipping($cart, array $carrier)
    {
        $free_shipping = false;

        if ($carrier['is_free']) {
            $free_shipping = true;
        } else {
            foreach ($cart->getCartRules() as $rule) {
                if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                    $free_shipping = true;
                    break;
                }
            }
        }

        return $free_shipping;
    }

    protected function renderPaymentOptions()
    {
        $all_conditions_approved = $this->checkWhetherAllConditionsAreApproved();

        $payment_options = (new Adapter_AdvancedPaymentOptionsConverter)->getPaymentOptionsForTemplate();
        $selected_payment_option = Tools::getValue('select_payment_option');
        if ($selected_payment_option) {
            $all_conditions_approved = true;
        }

        return $this->render('checkout/payment.tpl', [
            'payment_options' => $payment_options,
            'conditions_to_approve' => $this->getConditionsToApprove(),
            'approved_conditions' => $this->getSubmittedConditionsApproval(),
            'all_conditions_approved' => $all_conditions_approved,
            'selected_payment_option' => $selected_payment_option
        ]);
    }

    /**
     * Terms and conditions and other conditions are posted as an associative
     * array with the condition identifier as key.
     */
    private function getSubmittedConditionsApproval()
    {
        $required  = $this->getConditionsToApprove();
        $submitted = Tools::getValue('conditions_to_approve');
        if (!is_array($submitted)) {
            $submitted = [];
        }

        $approval = [];
        foreach ($required as $requiredConditionName => $unused) {
            $approval[$requiredConditionName] = !empty($submitted[$requiredConditionName]);
        }

        return $approval;
    }

    private function checkWhetherAllConditionsAreApproved()
    {
        foreach ($this->getSubmittedConditionsApproval() as $approved) {
            if (!$approved) {
                return false;
            }
        }
        return true;
    }

    public function getPaymentOptionsAction()
    {
        return $this->renderPaymentOptions();
    }

    public function renderCartSummary()
    {
        $cart_presenter = new Adapter_CartPresenter;
        return $this->render('checkout/_partials/cart-summary.tpl', [
            'cart' => $cart_presenter->present($this->context->cart)
        ]);
    }

    public function getCartSummaryAction()
    {
        return $this->renderCartSummary();
    }

    public function init()
    {
        parent::init();

        $id_country = Tools::getValue('id_country');
        if (!$id_country) {
            $id_country = Tools::getCountry();
        }

        $this->address_formatter = new Adapter_AddressFormatter(new Country($id_country));
        $this->address_form = new Adapter_AddressForm(
            $this->address_formatter,
            Tools::getAllValues(),
            $this->context->language,
            new Adapter_Translator()
        );

        if (($action = Tools::getValue('action'))) {
            $result = $this->{$action . 'Action'}();
            ob_end_clean();
            die($result);
        }
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('submitReorder') && $id_order = (int)Tools::getValue('id_order')) {
            $oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
            $duplication = $oldCart->duplicate();
            if (!$duplication || !Validate::isLoadedObject($duplication['cart'])) {
                $this->errors[] = $this->l('Sorry. We cannot renew your order.');
            } elseif (!$duplication['success']) {
                $this->errors[] = $this->l('Some items are no longer available, and we are unable to renew your order.');
            } else {
                $this->context->cookie->id_cart = $duplication['cart']->id;
                $context = $this->context;
                $context->cart = $duplication['cart'];
                CartRule::autoAddToCart($context);
                $this->context->cookie->write();
                Tools::redirect('index.php?controller=order');
            }
        }

        $this->context->smarty->assign([
            'payment_options' => $this->renderPaymentOptions(),
            'cart_summary' => $this->renderCartSummary(),
            'delivery_options' => $this->renderDeliveryOptions(),
        ]);

        if (!$this->context->customer->isLogged()) {
            if (empty($this->address_fields)) {
                $this->address_fields = $this->address_form->getAddressFormat();
            }

            $this->address_fields = array_merge(
                    ['email' => [
                        'label' => $this->l('Email Address'),
                        'required' => true,
                        'errors' => [],
                    ]],
                    $this->address_fields,
                    ['passwd' => [
                        'label' => $this->l('Set a password to create a full account'),
                        'required' => !Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                        'errors' => [],
                    ]]
                );

            if (empty($this->address)) {
                if (Validate::isLoadedObject($this->context->customer) && $this->context->customer->is_guest) {
                    $this->address = array_values($this->context->customer->getSimpleAddresses())[0];
                    $this->address['email'] = $this->context->customer->email;
                } else {
                    $this->address = $this->context->customer->getSimpleAddress(0);
                    $this->address['email'] = Tools::getValue('email');
                }
                $this->address['passwd'] = '';
            }

            $this->context->smarty->assign([
                'address_fields' => $this->address_fields,
                'address' => $this->address,
                'countries' => $this->address_form->getCountryList(),
                'back' => $this->context->link->getPageLink('order'),
                'mod' => false,
            ]);
        }

        $this->setTemplate('checkout/checkout.tpl');
    }

    public function postProcess()
    {
        parent::postProcess();

        // StarterTheme: Better submit
        if (Tools::isSubmit('submitAddress')) {
            $address_ok = $this->processAddressRegistration();
            if (!$address_ok) {
                return true;
            } else {
                Tools::redirect(
                    $this->context->link->getPageLink('order')
                );
            }
        }
    }

    public function processAddressRegistration()
    {
        $this->address_fields = $this->address_form->getAddressFormatWithErrors();

        if ($this->address_form->hasErrors()) {
            $this->address = [
                'id' => Tools::getValue('id_address'),
                'id_country' => Tools::getValue('id_country'),
                'id_state' => Tools::getValue('id_state'),
                'email' => Tools::getValue('email'),
                'passwd' => '',
            ];
            foreach ($this->address_fields as $key => $value) {
                $this->address[$key] = Tools::getValue($key);
            }

            return false;
        }

        $guest = new Guest($this->context->cookie->id_guest);
        if ($this->context->cookie->id_customer) {
            $new_customer = new Customer($this->context->cookie->id_customer);
            if ($pwd = Tools::getValue('passwd')) {
                $crypto = new Core_Foundation_Crypto_Hashing();
                $pwd = $crypto->encrypt($pwd, _COOKIE_KEY_);
                $new_customer->passwd = $pwd;
                $new_customer->is_guest = 0;
                if ($new_customer->update()) {
                    $guest->delete();
                }
            }
        } else {
            $new_customer = new Customer();
            $new_customer->firstname = Tools::getValue('firstname');
            $new_customer->lastname = Tools::getValue('lastname');
            $new_customer->email = Tools::getValue('email');

            $pwd = Tools::getValue('passwd');
            if (!$pwd) {
                $pwd = Tools::passwdGen(16);
                $new_customer->is_guest = 1;
            }
            $crypto = new Core_Foundation_Crypto_Hashing();
            $pwd = $crypto->encrypt($pwd, _COOKIE_KEY_);
            $new_customer->passwd = $pwd;

            if ($new_customer->add()) {
                $this->context->cookie->id_customer = $new_customer->id;
                $this->context->cookie->write();
                $this->context->customer = $new_customer;
            } else {
                $this->errors['unexpected'] = $this->l('An unexpected error occured while saving your data');
                return false;
            }
        }

        // Save address
        $addr = new Address(Tools::getValue('id_address'));
        $addr->alias = $this->l('My address');
        $addr->id_customer = $new_customer->id;
        $addr->validateController();

        if ($addr->save()) {
            $this->context->cart->id_address_delivery = $addr->id;
            $this->context->cart->id_address_invoice = $addr->id;
            $this->context->cart->update();
            return true;
        }

        $this->errors['unexpected'] = $this->l('An unexpected error occured while saving your data');
        return false;
    }
}
