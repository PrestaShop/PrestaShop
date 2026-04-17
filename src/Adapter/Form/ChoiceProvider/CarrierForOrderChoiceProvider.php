<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Address;
use Carrier;
use Cart;
use Customer;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
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

        return FormChoiceFormatter::formatFormChoices(
            Carrier::getCarriersForOrder(Address::getZoneById((int) $address->id), $groups, $cart),
            'id_carrier',
            'name'
        );
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
