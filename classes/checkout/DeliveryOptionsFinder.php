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
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeliveryOptionsFinderCore
{
    private $context;
    private $objectPresenter;
    private $translator;
    private $priceFormatter;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        ObjectPresenter $objectPresenter,
        PriceFormatter $priceFormatter
    ) {
        $this->context = $context;
        $this->objectPresenter = $objectPresenter;
        $this->translator = $translator;
        $this->priceFormatter = $priceFormatter;
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
        // Load up configuration
        $include_taxes = !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer) && (int) Configuration::get('PS_TAX');
        $display_taxes_label = (Configuration::get('PS_TAX') && $this->context->country->display_tax_label && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'));

        // Get delivery options from list
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();

        // Prepare empty list we will populate
        $formattedDeliveryOptions = [];

        // If there are no carriers available, nothing to do here
        if (empty($delivery_option_list[$this->context->cart->id_address_delivery])) {
            return $formattedDeliveryOptions;
        }

        foreach ($delivery_option_list[$this->context->cart->id_address_delivery] as $deliveryOptionId => $deliveryOptionData) {
            /*
             * Prepare empty delivery option.
             * For some properties, we will use the whole list. For others, data from carriers themselves.
             */
            $formattedDeliveryOption = $deliveryOptionData;
            $formattedDeliveryOption['id'] = $deliveryOptionId;

            // Add pricing information
            if ($this->isFreeShipping($this->context->cart, $deliveryOptionData)) {
                $formattedDeliveryOption['price'] = $this->translator->trans(
                    'Free',
                    [],
                    'Shop.Theme.Checkout'
                );
            } else {
                $formattedDeliveryOption['price'] = $this->priceFormatter->format(
                    $include_taxes ? $deliveryOptionData['total_price_with_tax'] : $deliveryOptionData['total_price_without_tax']
                );
                if ($display_taxes_label) {
                    $formattedDeliveryOption['price'] = $this->translator->trans(
                        $include_taxes ? '%price% tax incl.' : '%price% tax excl.',
                        ['%price%' => $formattedDeliveryOption['price']],
                        'Shop.Theme.Checkout'
                    );
                }
            }

            /* 
             * Add names and delivery delays.
             * 
             * When the delivery option consists of more carriers, we join up their names and delays.
             * If it's only one carrier, we just use it.
             */
            if (count($deliveryOptionData['carrier_list']) > 1) {
                $formattedDeliveryOption['logo'] = null;
                $names = [];
                $delays = [];
                foreach ($deliveryOptionData['carrier_list'] as $carrier) {
                    $names[] = $carrier['instance']->name;
                    $delays[] = $carrier['instance']->delay[$this->context->language->id];
                }
                $formattedDeliveryOption['name'] = implode(', ', $names);
                $formattedDeliveryOption['delay'] = implode(', ', $delays);
            } else {
                $carrier = reset($deliveryOptionData['carrier_list']);
                $formattedDeliveryOption['logo'] = $carrier['logo'];
                $formattedDeliveryOption['name'] = $carrier['instance']->name;
                $formattedDeliveryOption['delay'] = $carrier['instance']->delay[$this->context->language->id];
            }

            /* 
             * If carriers are related to a module, check for additionnal data to display.
             * We will call these hooks all the carriers in the delivery option, so
             * all modules can display their extra data - pickup branches etc.
             */
            $formattedDeliveryOption['extraContent'] = '';
            foreach ($deliveryOptionData['carrier_list'] as $carrier) {
                if ($carrier['instance']->is_module) {
                    if ($moduleId = Module::getModuleIdByName($carrier['instance']->external_module_name)) {
                        // Hook called only for the module concerned
                        $formattedDeliveryOption['extraContent'] .= Hook::exec('displayCarrierExtraContent', ['carrier' => $carrier['instance']], $moduleId);
                    }
                }
            }

            // Add it to our list
            $formattedDeliveryOptions[$deliveryOptionId] = $formattedDeliveryOption;
        }

        return $formattedDeliveryOptions;
    }
}
