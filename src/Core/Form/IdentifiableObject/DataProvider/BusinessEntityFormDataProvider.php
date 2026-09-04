<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForEditing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\EditableBusinessEntity;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityAddressType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityGeneralInformationType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityType;

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
        private readonly CommandBusInterface $queryBus,
    ) {
        $this->defaultCountryId = $configuration->getInt('PS_COUNTRY_DEFAULT');
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, mixed>
     */
    public function getData($id): array
    {
        /** @var EditableBusinessEntity $editableBusinessEntity */
        $editableBusinessEntity = $this->queryBus->handle(new GetBusinessEntityForEditing((int) $id));

        return [
            BusinessEntityType::GENERAL_INFORMATION => [
                BusinessEntityGeneralInformationType::FIELD_NAME => $editableBusinessEntity->getName(),
                BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => $editableBusinessEntity->getLegalName() ?? '',
                BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => $editableBusinessEntity->getExternalRef() ?? '',
                BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => $editableBusinessEntity->isDeliveryAuthorized(),
                BusinessEntityGeneralInformationType::FIELD_STATUS => $editableBusinessEntity->getStatus(),
                BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => $editableBusinessEntity->getCustomerGroupId(),
            ],
            BusinessEntityType::BILLING_ADDRESS_TYPE => [],
            BusinessEntityType::SHIPPING_ADDRESS_TYPE => [],
            BusinessEntityType::BILLING_ADDRESS_AS_SHIPPING_ADDRESS => true,
            BusinessEntityType::DEFAULT_BILLING_ADDRESS => self::DEFAULT_BILLING_ADDRESS_INDEX,
            BusinessEntityType::DEFAULT_SHIPPING_ADDRESS => self::DEFAULT_SHIPPING_ADDRESS_INDEX,
            BusinessEntityType::SHOP_ID => $editableBusinessEntity->getShopId(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData(): array
    {
        return [
            BusinessEntityType::GENERAL_INFORMATION => [
                BusinessEntityGeneralInformationType::FIELD_NAME => '',
                BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME => '',
                BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF => '',
                BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED => false,
                BusinessEntityGeneralInformationType::FIELD_STATUS => BusinessEntityStatus::PENDING,
                BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID => self::DEFAULT_CUSTOMER_GROUP_ID,
            ],
            BusinessEntityType::BILLING_ADDRESS_TYPE => [
                self::DEFAULT_BILLING_ADDRESS_INDEX => [
                    BusinessEntityAddressType::FIELD_COUNTRY_ID => $this->defaultCountryId,
                ],
            ],
            BusinessEntityType::SHIPPING_ADDRESS_TYPE => [
            ],
            BusinessEntityType::BILLING_ADDRESS_AS_SHIPPING_ADDRESS => true,
            BusinessEntityType::DEFAULT_BILLING_ADDRESS => self::DEFAULT_BILLING_ADDRESS_INDEX,
            BusinessEntityType::DEFAULT_SHIPPING_ADDRESS => self::DEFAULT_SHIPPING_ADDRESS_INDEX,
            BusinessEntityType::SHOP_ID => $this->shopContext->isSingleShopContext() ? $this->shopContext->getId() : null,
        ];
    }
}
