<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Adapter\BusinessEntity\CommandHandler\AddBusinessEntityHandler;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\AddBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\AbstractBusinessEntityAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityBillingAddress;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityShippingAddress;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityAddressType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityGeneralInformationType;
use PrestaShopBundle\Form\Admin\Sell\BusinessEntity\BusinessEntityType;

final class BusinessEntityFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @see AddBusinessEntityHandler::handle
     */
    public function create(array $data): int
    {
        $generalInformationData = $data[BusinessEntityType::GENERAL_INFORMATION];
        $generalInformation = new BusinessEntityGeneralInformation(
            $generalInformationData[BusinessEntityGeneralInformationType::FIELD_NAME],
            $generalInformationData[BusinessEntityGeneralInformationType::FIELD_LEGAL_NAME],
            $generalInformationData[BusinessEntityGeneralInformationType::FIELD_EXTERNAL_REF],
            $generalInformationData[BusinessEntityGeneralInformationType::FIELD_DELIVERY_AUTHORIZED],
            $generalInformationData[BusinessEntityGeneralInformationType::FIELD_STATUS],
            (int) $data[BusinessEntityType::SHOP_ID],
            (int) $generalInformationData[BusinessEntityGeneralInformationType::FIELD_CUSTOMER_GROUP_ID],
        );

        $command = new AddBusinessEntityCommand(
            $generalInformation->getName(),
            $generalInformation->getLegalName(),
            $generalInformation->getExternalRef(),
            $generalInformation->isDeliveryAuthorized(),
            $generalInformation->getStatus(),
            $this->resolveShopId($generalInformation->getShopId()),
            $generalInformation->getCustomerGroupId(),
            (bool) $data[BusinessEntityType::BILLING_ADDRESS_AS_SHIPPING_ADDRESS],
            $this->buildAddresses(
                BusinessEntityBillingAddress::class,
                $data[BusinessEntityType::BILLING_ADDRESS_TYPE] ?? [],
                (int) ($data[BusinessEntityType::DEFAULT_BILLING_ADDRESS] ?? 0)
            ),
            $this->buildAddresses(
                BusinessEntityShippingAddress::class,
                $data[BusinessEntityType::SHIPPING_ADDRESS_TYPE] ?? [],
                (int) ($data[BusinessEntityType::DEFAULT_SHIPPING_ADDRESS] ?? 0)
            ),
        );

        return $this->commandBus->handle($command)->getValue();
    }

    /**
     * @template T of AbstractBusinessEntityAddress
     *
     * @param class-string<T> $addressClass
     * @param array<int|string, array<string, mixed>> $addressesData
     *
     * @return T[]
     */
    private function buildAddresses(string $addressClass, array $addressesData, int $defaultIndex): array
    {
        $addresses = [];
        foreach ($addressesData as $index => $addressData) {
            $addresses[] = new $addressClass(
                $addressData[BusinessEntityAddressType::FIELD_ALIAS] ?? '',
                $addressData[BusinessEntityAddressType::FIELD_ADDRESS_1] ?? '',
                $addressData[BusinessEntityAddressType::FIELD_ADDRESS_2] ?? null,
                $addressData[BusinessEntityAddressType::FIELD_CITY] ?? '',
                $addressData[BusinessEntityAddressType::FIELD_POSTCODE] ?? '',
                (int) ($addressData[BusinessEntityAddressType::FIELD_COUNTRY_ID] ?? 0),
                (int) $index === $defaultIndex,
                !empty($addressData[BusinessEntityAddressType::FIELD_STATE_ID])
                    ? (int) $addressData[BusinessEntityAddressType::FIELD_STATE_ID]
                    : null,
                $addressData[BusinessEntityAddressType::FIELD_PHONE] ?? null,
                $addressData[BusinessEntityAddressType::FIELD_PHONE_MOBILE] ?? null,
            );
        }

        return $addresses;
    }

    private function resolveShopId(int $submittedShopId): int
    {
        if ($this->shopContext->isSingleShopContext()) {
            return $this->shopContext->getId();
        }

        return $submittedShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $data)
    {
        // TODO: US2.1.3
    }
}
