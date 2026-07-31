<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository\BusinessEntityAddressRepository;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\Address\AddressFormatterInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryHandler\GetBusinessEntityForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\AddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\BusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\IdentifierForViewing;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsQueryHandler]
final class GetBusinessEntityForViewingHandler implements GetBusinessEntityForViewingHandlerInterface
{
    public function __construct(
        private readonly BusinessEntityRepository $businessEntityRepository,
        private readonly BusinessEntityAddressRepository $businessEntityAddressRepository,
        private readonly GroupDataProvider $groupDataProvider,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
        private readonly AddressFormatterInterface $addressFormatter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return string[]
     */
    private static function invoiceAddressTypes(): array
    {
        return [AddressTypeEnum::INVOICE->value, AddressTypeEnum::BOTH->value];
    }

    /**
     * @return string[]
     */
    private static function deliveryAddressTypes(): array
    {
        return [AddressTypeEnum::DELIVERY->value, AddressTypeEnum::BOTH->value];
    }

    /**
     * @throws BusinessEntityNotFoundException
     */
    public function handle(GetBusinessEntityForViewing $query): BusinessEntityForViewing
    {
        $businessEntityId = $query->getBusinessEntityId()->getValue();

        $shopIds = $this->shopContext->isAllShopContext() ? null : $this->shopContext->getAssociatedShopIds();
        $businessEntity = $this->businessEntityRepository->findById($businessEntityId, $shopIds);

        if (null === $businessEntity) {
            throw new BusinessEntityNotFoundException(sprintf('Business entity with id %d was not found.', $businessEntityId));
        }

        $languageId = $this->languageContext->getId();
        $addresses = $this->mapAddresses($this->businessEntityAddressRepository->getAddresses($businessEntityId, AddressTypeEnum::values()));
        $invoiceAddresses = $this->filterAddressesByType($addresses, self::invoiceAddressTypes());
        $deliveryAddresses = $this->filterAddressesByType($addresses, self::deliveryAddressTypes());
        $identifiers = $this->mapIdentifiers($businessEntity);
        $linkedCustomersCount = $this->businessEntityRepository->getLinkedCustomersCount($businessEntityId);
        $addressesCount = $this->countUniqueAddresses($addresses);
        $customerGroupName = $this->getCustomerGroupName($businessEntity->getIdCustomerGroup(), $languageId);

        return new BusinessEntityForViewing(
            $businessEntity->getId(),
            $businessEntity->getExternalRef(),
            $businessEntity->getName(),
            $businessEntity->getLegalName(),
            $businessEntity->isDeliveryAuthorized(),
            $businessEntity->getStatus()->value,
            $businessEntity->getStatus()->trans($this->translator),
            DateTimeImmutable::createFromInterface($businessEntity->getCreatedAt()),
            DateTimeImmutable::createFromInterface($businessEntity->getUpdatedAt()),
            $linkedCustomersCount,
            $addressesCount,
            $businessEntity->getIdCustomerGroup(),
            $customerGroupName,
            $invoiceAddresses,
            $deliveryAddresses,
            $identifiers,
        );
    }

    /**
     * @param AddressForViewing[] $addresses
     * @param string[] $types
     *
     * @return AddressForViewing[]
     */
    private function filterAddressesByType(array $addresses, array $types): array
    {
        return array_values(array_filter(
            $addresses,
            static fn (AddressForViewing $address): bool => in_array($address->getAddressType(), $types, true)
        ));
    }

    /**
     * @param AddressForViewing[] $addresses
     */
    private function countUniqueAddresses(array $addresses): int
    {
        $uniqueAddressIds = [];
        foreach ($addresses as $address) {
            $uniqueAddressIds[$address->getAddressId()] = true;
        }

        return count($uniqueAddressIds);
    }

    private function getCustomerGroupName(int $idGroup, int $idLang): string
    {
        foreach ($this->groupDataProvider->getGroups($idLang) as $group) {
            if ((int) $group['id_group'] === $idGroup) {
                return (string) $group['name'];
            }
        }

        return '';
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     *
     * @return AddressForViewing[]
     */
    private function mapAddresses(array $rows): array
    {
        $addresses = [];
        foreach ($rows as $row) {
            $addressId = (int) $row['id_address'];
            $addresses[] = new AddressForViewing(
                $addressId,
                (string) $row['alias'],
                $this->addressFormatter->format(new AddressId($addressId)),
                (string) $row['address_type'],
                (bool) $row['is_default'],
            );
        }

        return $addresses;
    }

    /**
     * @return IdentifierForViewing[]
     */
    private function mapIdentifiers(BusinessEntity $businessEntity): array
    {
        $identifiers = [];
        foreach ($businessEntity->getBusinessEntityIdentifiers() as $businessEntityIdentifier) {
            $identifier = $businessEntityIdentifier->getBusinessIdentifier();
            if ($identifier->isDeleted()) {
                continue;
            }

            $identifiers[$identifier->getId()] = new IdentifierForViewing(
                $identifier->getId(),
                $identifier->getLabel(),
                $businessEntityIdentifier->getValue(),
            );
        }

        ksort($identifiers);

        return array_values($identifiers);
    }
}
