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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Address;
use Carrier;
use Cart;
use Customer;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @internal
 */
final class CarrierForOrderChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);

        $cart = Cart::getCartByOrderId($options['order_id']);
        $groups = Customer::getGroupsStatic((int) $cart->id_customer);
        $address = new Address((int) $cart->id_address_delivery);

        $carriers = Carrier::getCarriersForOrder(Address::getZoneById((int) $address->id), $groups, $cart);
        $choices = [];

        foreach ($carriers as $carrier) {
            $delay = $carrier['delay'] ? sprintf(' (%s)', $carrier['delay']) : '';

            $choices[$carrier['name'] . $delay] = (int) $carrier['id_carrier'];
        }

        return $choices;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'order_id',
        ]);
        $resolver->setAllowedTypes('order_id', 'int');

        return $resolver->resolve($options);
    }
}
