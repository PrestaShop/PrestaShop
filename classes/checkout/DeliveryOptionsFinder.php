<?php

use Symfony\Component\Translation\TranslatorInterface;

class DeliveryOptionsFinderCore
{
    private $context;
    private $translator;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        Adapter_ObjectSerializer $objectSerializer,
        Adapter_PricePresenter $pricePresenter
    ) {
        $this->context = $context;
        $this->objectSerializer = $objectSerializer;
        $this->translator = $translator;
        $this->pricePresenter = $pricePresenter;
    }

    private function isFreeShipping($cart, array $carrier)
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

    public function getSelectedDeliveryOption()
    {
        return current($this->context->cart->getDeliveryOption(null, false, false));
    }

    public function getDeliveryOptions()
    {
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $include_taxes = !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer) && (int)Configuration::get('PS_TAX');
        $display_taxes_label = ((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')) && $this->context->smarty->tpl_vars['display_tax_label']->value);

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
                                $carrier['price'] = $this->translator->trans(
                                    'Free', [], 'Carrier'
                                );
                            } else {
                                if ($include_taxes) {
                                    $carrier['price'] = $this->pricePresenter->convertAndFormat($carriers_list['total_price_with_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = sprintf(
                                            $this->translator->trans(
                                                '%s tax incl.', [], 'Carrier'
                                            ),
                                            $carrier['price']
                                        );
                                    }
                                } else {
                                    $carrier['price'] = $this->pricePresenter->convertAndFormat($carriers_list['total_price_without_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = sprintf(
                                            $this->translator->trans(
                                                '%s tax excl.', [], 'Carrier'
                                            ),
                                            $carrier['price']
                                        );
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
        }
        return $carriers_available;
    }
}
