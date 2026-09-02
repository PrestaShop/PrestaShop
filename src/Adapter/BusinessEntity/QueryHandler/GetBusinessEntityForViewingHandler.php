<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository\BusinessEntityAddressRepository;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository\BusinessEntityAddressRow;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
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
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
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
        private readonly GroupRepository $groupRepository,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
        private readonly AddressFormatterInterface $addressFormatter,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return AddressTypeEnum[]
     */
    private static function invoiceAddressTypes(): array
    {
        return [AddressTypeEnum::INVOICE, AddressTypeEnum::BOTH];
    }

    /**
     * @return AddressTypeEnum[]
     */
    private static function deliveryAddressTypes(): array
    {
        return [AddressTypeEnum::DELIVERY, AddressTypeEnum::BOTH];
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
        $addresses = $this->mapAddresses($this->businessEntityAddressRepository->getAddresses($businessEntityId));
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
            $businessEntity->getStatus(),
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
     * @param AddressTypeEnum[] $types
     *
     * @return AddressForViewing[]
     */
    private function filterAddressesByType(array $addresses, array $types): array
    {
        $hasOneOfTheTypes = static fn (AddressForViewing $address): bool => in_array($address->getAddressType(), $types, true);

        return array_values(array_filter($addresses, $hasOneOfTheTypes));
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
        // The schema has no foreign key and deleting a customer group never cleans up
        // business_entity, so an entity can outlive its group: fall back instead of failing.
        try {
            return $this->groupRepository->get(new GroupId($idGroup))->name[$idLang] ?? '';
        } catch (GroupNotFoundException) {
            return '';
        }
    }

    /**
     * @param BusinessEntityAddressRow[] $rows
     *
     * @return AddressForViewing[]
     */
    private function mapAddresses(array $rows): array
    {
        $addresses = [];
        foreach ($rows as $row) {
            $addresses[] = new AddressForViewing(
                $row->getAddressId(),
                $row->getAlias(),
                $this->addressFormatter->format(new AddressId($row->getAddressId())),
                $row->getAddressType(),
                $row->isDefault(),
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
