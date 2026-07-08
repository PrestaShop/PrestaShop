<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\DataTransformer;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
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
                $addressData['alias'],
                $addressData['address1'],
                $addressData['address2'],
                $addressData['city'],
                $addressData['postcode'],
                $addressData['id_country'],
                $index === (int) $value[BusinessEntityType::DEFAULT_BILLING_ADDRESS],
                $addressData['id_state'] ?? null,
            );
        }

        $shippingAddresses = [];
        foreach ($value[BusinessEntityType::SHIPPING_ADDRESS_TYPE] ?? [] as $index => $addressData) {
            $shippingAddresses[] = new BusinessEntityShippingAddress(
                $addressData['alias'],
                $addressData['address1'],
                $addressData['address2'],
                $addressData['city'],
                $addressData['postcode'],
                $addressData['id_country'],
                $index === (int) $value[BusinessEntityType::DEFAULT_SHIPPING_ADDRESS],
                $addressData['id_state'] ?? null,
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
