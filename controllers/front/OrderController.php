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

    private $cart_presented;

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

    protected function renderGenders()
    {
        $genders = [];
        $collec = Gender::getGenders();
        foreach ($collec as $g) {
            $genders[] = $this->objectSerializer->toArray($g);
        }

        return $genders;
    }

    /**
     * Assign date var to smarty
     */
    protected function assignDate()
    {
        $selectedYears = (int)(Tools::getValue('years', 0));
        $years = Tools::dateYears();
        $selectedMonths = (int)(Tools::getValue('months', 0));
        $months = Tools::dateMonths();
        $selectedDays = (int)(Tools::getValue('days', 0));
        $days = Tools::dateDays();

        $this->context->smarty->assign([
            'birthday_dates' => [
                'years' => $years,
                'sl_year' => $selectedYears,
                'months' => $months,
                'sl_month' => $selectedMonths,
                'days' => $days,
                'sl_day' => $selectedDays
            ]]);
    }

    protected function renderAddressFormDelivery()
    {
        return $this->render('checkout/_partials/address-form-delivery.tpl', [
            'address_fields' => $this->address_fields,
            'address' => $this->address,
            'countries' => $this->address_form->getCountryList(),
        ]);
    }

    protected function renderAddressFormInvoice()
    {
        return $this->render('checkout/_partials/address-form-invoice.tpl', [
            'address_fields' => $this->address_fields,
            'address' => $this->address,
            'countries' => $this->address_form->getCountryList(),
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
        return $this->render('checkout/_partials/cart-summary.tpl', [
            'cart' => $this->cart_presented,
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

        $this->cart_presented = $this->cart_presenter->present($this->context->cart);

        $this->context->smarty->assign([
            'cart' => $this->cart_presented,
            'payment_options' => $this->renderPaymentOptions(),
            'cart_summary' => $this->renderCartSummary(),
            'delivery_options' => $this->renderDeliveryOptions(),
            'genders' => $this->renderGenders(),
            'login' => (bool)Tools::getValue('login'),
            'guest_allowed' => (bool)Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
        ]);

        $this->assignDate();

        if (!$this->context->customer->isLogged()
            || ($this->context->customer->isLogged() && empty($this->context->customer->getSimpleAddresses()))
        ) {
            if (empty($this->address_fields)) {
                $this->address_fields = $this->address_form->getAddressFormat();
            }

            if (empty($this->address)) {
                $this->address = $this->context->customer->getSimpleAddress(0);
            }

            foreach (['firstname', 'lastname'] as $attr) {
                if (empty($this->address->{$attr})) {
                    $this->address[$attr] = $this->context->customer->{$attr};
                }
            }

            $this->context->smarty->assign([
                'address_form_delivery' => $this->renderAddressFormDelivery(),
                'address_form_invoice' => $this->renderAddressFormInvoice(),
            ]);
        }

        $this->setTemplate('checkout/checkout.tpl');
    }

    public function postProcess()
    {
        parent::postProcess();

        // StarterTheme: Better submit
        if (Tools::isSubmit('changeAddresses')) {
            $id_address_delivery = Tools::getValue('id_address_delivery');
            $this->context->cart->id_address_delivery = $id_address_delivery;
            if (Tools::getValue('checkout-different-address-for-invoice') === 'on') {
                $this->context->cart->id_address_invoice = Tools::getValue('id_address_invoice');
            } else {
                $this->context->cart->id_address_invoice = $id_address_delivery;
            }
        } elseif (Tools::isSubmit('submitAddressDelivery')) {
            $address_ok = $this->processAddressDelivery();
            if (!$address_ok) {
                return true;
            } else {
                Tools::redirect(
                    $this->context->link->getPageLink('order')
                );
            }
        } elseif (Tools::isSubmit('submitPersonalDetails')) {
            $this->processSubmitPersonalDetails();
        }
    }

    public function processAddressDelivery()
    {
        $this->address_fields = $this->address_form->getAddressFormatWithErrors();

        if ($this->address_form->hasErrors()) {
            $this->address = [
                'id' => Tools::getValue('id_address'),
                'id_country' => Tools::getValue('id_country'),
                'id_state' => Tools::getValue('id_state'),
            ];
            foreach ($this->address_fields as $key => $value) {
                $this->address[$key] = Tools::getValue($key);
            }

            return false;
        }

        // Save address
        $addr = new Address(Tools::getValue('id_address'));
        $addr->alias = $this->l('My address');
        $addr->id_customer = $this->context->cookie->id_customer;
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
