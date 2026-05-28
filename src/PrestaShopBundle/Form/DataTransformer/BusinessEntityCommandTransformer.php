<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\DataTransformer;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms BusinessEntity form data to structured array
 */
class BusinessEntityCommandTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        $billingAddresses = [];
        foreach ($value['billing_address'] ?? [] as $index => $addressData) {
            $billingAddresses[] = new BusinessEntityBillingAddress(
                $addressData['alias'],
                $addressData['address1'],
                $addressData['address2'],
                $addressData['city'],
                $addressData['postcode'],
                $addressData['id_country'],
                $index === (int) $value['default_billing_address'],
                $addressData['id_state'] ?? null,
            );
        }

        $shippingAddresses = [];
        foreach ($value['shipping_address'] ?? [] as $index => $addressData) {
            $shippingAddresses[] = new BusinessEntityShippingAddress(
                $addressData['alias'],
                $addressData['address1'],
                $addressData['address2'],
                $addressData['city'],
                $addressData['postcode'],
                $addressData['id_country'],
                $index === (int) $value['default_shipping_address'],
                $addressData['id_state'] ?? null,
            );
        }

        return [
            'general_information' => new BusinessEntityGeneralInformation(
                $value['general_information']['name'],
                $value['general_information']['legal_name'],
                $value['general_information']['external_ref'],
                $value['general_information']['delivery_authorized'],
                $value['general_information']['status'],
                (int) $value['shop_id'],
                (int) $value['general_information']['customer_group_id'],
            ),
            'billing_address' => $billingAddresses,
            'shipping_address' => $shippingAddresses,
            'billingAddressAsShippingAddress' => (bool) $value['billingAddressAsShippingAddress'],
        ];
    }
}
