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


use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;

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
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $include_taxes = !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer) && (int) Configuration::get('PS_TAX');
        $display_taxes_label = (Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'));

        $carriers_available = array();

        if (isset($delivery_option_list[$this->context->cart->id_address_delivery])) {
            foreach ($delivery_option_list[$this->context->cart->id_address_delivery] as $id_carriers_list => $carriers_list) {
                foreach ($carriers_list as $carriers) {
                    if (is_array($carriers)) {
                        foreach ($carriers as $carrier) {
                            $carrier = array_merge($carrier, $this->objectPresenter->present($carrier['instance']));
                            $delay = $carrier['delay'][$this->context->language->id];
                            unset($carrier['instance'], $carrier['delay']);
                            $carrier['delay'] = $delay;
                            if ($this->isFreeShipping($this->context->cart, $carriers_list)) {
                                $carrier['price'] = $this->translator->trans(
                                    'Free', array(), 'Shop.Theme.Checkout'
                                );
                            } else {
                                if ($include_taxes) {
                                    $carrier['price'] = $this->priceFormatter->format($carriers_list['total_price_with_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = $this->translator->trans(
                                            '%price% tax incl.',
                                            array('%price%' => $carrier['price']),
                                            'Shop.Theme.Checkout'
                                        );
                                    }
                                } else {
                                    $carrier['price'] = $this->priceFormatter->format($carriers_list['total_price_without_tax']);
                                    if ($display_taxes_label) {
                                        $carrier['price'] = $this->translator->trans(
                                            '%price% tax excl.',
                                            array('%price%' => $carrier['price']),
                                            'Shop.Theme.Checkout'
                                        );
                                    }
                                }
                            }

                            if (count($carriers) > 1) {
                                $carrier['label'] = $carrier['price'];
                            } else {
                                $carrier['label'] = $carrier['name'].' - '.$carrier['delay'].' - '.$carrier['price'];
                            }

                            // If carrier related to a module, check for additionnal data to display
                            $carrier['extraContent'] = '';
                            if ($carrier['is_module']) {
                                if ($moduleId = Module::getModuleIdByName($carrier['external_module_name'])) {
                                    $carrier['extraContent'] = Hook::exec('displayCarrierExtraContent', array('carrier' => $carrier), $moduleId);
                                }
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
