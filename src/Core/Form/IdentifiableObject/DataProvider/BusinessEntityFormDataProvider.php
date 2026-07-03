<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityAddressType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityGeneralInformationType;

final class BusinessEntityFormDataProvider implements FormDataProviderInterface
{
    public const DEFAULT_BILLING_ADDRESS_INDEX = 1;
    public const DEFAULT_SHIPPING_ADDRESS_INDEX = 0;

    /**
     * @todo Hardcoded default customer group. The configurable default (wired through Configuration)
     *       is delivered in a separate branch; replace this constant with that setting once merged.
     */
    private const DEFAULT_CUSTOMER_GROUP_ID = 3;

    private readonly int $defaultCountryId;

    public function __construct(
        Configuration $configuration,
        private readonly ShopContext $shopContext,
    ) {
        $this->defaultCountryId = $configuration->getInt('PS_COUNTRY_DEFAULT');
    }

    /**
     * {@inheritDoc}
     */
    public function getData($id)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData()
    {
        return [
            'general_information' => [
                BusinessEntityGeneralInformationType::FIELD_NAME => '',
                BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => '',
                BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => '',
                BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => false,
                BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::PENDING,
                BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => self::DEFAULT_CUSTOMER_GROUP_ID,
            ],
            'billing_address' => [
                self::DEFAULT_BILLING_ADDRESS_INDEX => [
                    BusinessEntityAddressType::FIELD_COUNTRY_ID => $this->defaultCountryId,
                ],
            ],
            'shipping_address' => [
            ],
            'billing_address_as_shipping_address' => true,
            'default_billing_address' => self::DEFAULT_BILLING_ADDRESS_INDEX,
            'default_shipping_address' => self::DEFAULT_SHIPPING_ADDRESS_INDEX,
            'shop_id' => $this->shopContext->isSingleShopContext() ? $this->shopContext->getId() : null,
        ];
    }
}
