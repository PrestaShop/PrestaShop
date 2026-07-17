<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\DataTransformer;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityAddressType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityType;
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
        foreach ($value[BusinessEntityType::BILLING_ADDRESS_TYPE] ?? [] as $index => $addressData) {
            $billingAddresses[] = new BusinessEntityBillingAddress(
                $addressData[BusinessEntityAddressType::FIELD_ALIAS],
                $addressData[BusinessEntityAddressType::FIELD_ADDRESS_1],
                $addressData[BusinessEntityAddressType::FIELD_ADDRESS_2],
                $addressData[BusinessEntityAddressType::FIELD_CITY],
                $addressData[BusinessEntityAddressType::FIELD_POSTCODE],
                $addressData[BusinessEntityAddressType::FIELD_COUNTRY_ID],
                $index === (int) $value[BusinessEntityType::DEFAULT_BILLING_ADDRESS],
                $addressData[BusinessEntityAddressType::FIELD_STATE_ID] ?? null,
                $addressData[BusinessEntityAddressType::FIELD_PHONE] ?? null,
                $addressData[BusinessEntityAddressType::FIELD_PHONE_MOBILE] ?? null,
            );
        }

        $shippingAddresses = [];
        foreach ($value[BusinessEntityType::SHIPPING_ADDRESS_TYPE] ?? [] as $index => $addressData) {
            $shippingAddresses[] = new BusinessEntityShippingAddress(
                $addressData[BusinessEntityAddressType::FIELD_ALIAS],
                $addressData[BusinessEntityAddressType::FIELD_ADDRESS_1],
                $addressData[BusinessEntityAddressType::FIELD_ADDRESS_2],
                $addressData[BusinessEntityAddressType::FIELD_CITY],
                $addressData[BusinessEntityAddressType::FIELD_POSTCODE],
                $addressData[BusinessEntityAddressType::FIELD_COUNTRY_ID],
                $index === (int) $value[BusinessEntityType::DEFAULT_SHIPPING_ADDRESS],
                $addressData[BusinessEntityAddressType::FIELD_STATE_ID] ?? null,
                $addressData[BusinessEntityAddressType::FIELD_PHONE] ?? null,
                $addressData[BusinessEntityAddressType::FIELD_PHONE_MOBILE] ?? null,
            );
        }

        return [
            BusinessEntityType::GENERAL_INFORMATION => new BusinessEntityGeneralInformation(
                $value[BusinessEntityType::GENERAL_INFORMATION]['name'],
                $value[BusinessEntityType::GENERAL_INFORMATION]['legal_name'],
                $value[BusinessEntityType::GENERAL_INFORMATION]['external_ref'],
                $value[BusinessEntityType::GENERAL_INFORMATION]['delivery_authorized'],
                $value[BusinessEntityType::GENERAL_INFORMATION]['status'],
                (int) $value[BusinessEntityType::SHOP_ID],
                (int) $value[BusinessEntityType::GENERAL_INFORMATION]['customer_group_id'],
            ),
            BusinessEntityType::BILLING_ADDRESS_TYPE => $billingAddresses,
            BusinessEntityType::SHIPPING_ADDRESS_TYPE => $shippingAddresses,
            BusinessEntityType::BILLING_ADDRESS_AS_SHIPPING_ADDRESS => (bool) $value[BusinessEntityType::BILLING_ADDRESS_AS_SHIPPING_ADDRESS],
        ];
    }
}
