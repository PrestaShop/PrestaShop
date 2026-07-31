<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\BusinessEntity\QueryHandler;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\QueryHandler\GetBusinessEntityForViewingHandler;
use PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository\BusinessEntityAddressRepository;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Core\Address\AddressFormatterInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Query\GetBusinessEntityForViewing;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\B2B\BusinessEntityIdentifier;
use PrestaShopBundle\Entity\B2B\BusinessIdentifier;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class GetBusinessEntityForViewingHandlerTest extends TestCase
{
    public function testItThrowsNotFoundScopedToTheCurrentShopContext(): void
    {
        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with(999, [2, 3])
            ->willReturn(null);

        $handler = new GetBusinessEntityForViewingHandler(
            $repository,
            $this->createMock(BusinessEntityAddressRepository::class),
            $this->createMock(GroupDataProvider::class),
            $this->getMockLanguageContext(),
            $this->getMockShopContext(false, [2, 3]),
            $this->createMock(AddressFormatterInterface::class),
            $this->getMockTranslator()
        );

        $this->expectException(BusinessEntityNotFoundException::class);
        $handler->handle(new GetBusinessEntityForViewing(999));
    }

    public function testItFallsBackToAnEmptyCustomerGroupNameWhenTheGroupIsNotFound(): void
    {
        $entity = $this->getMockBusinessEntity();
        $entity->method('getBusinessEntityIdentifiers')->willReturn(new ArrayCollection());

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($entity);
        $repository->method('getLinkedCustomersCount')->willReturn(4);

        $addressRepository = $this->createMock(BusinessEntityAddressRepository::class);
        $addressRepository->method('getAddresses')->willReturn([]);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        // Customer group 99 is absent from the returned groups -> the handler must fall back to ''.
        $groupDataProvider->method('getGroups')->willReturn([['id_group' => 1, 'name' => 'Visitor']]);

        $handler = new GetBusinessEntityForViewingHandler(
            $repository,
            $addressRepository,
            $groupDataProvider,
            $this->getMockLanguageContext(),
            $this->getMockShopContext(true),
            $this->createMock(AddressFormatterInterface::class),
            $this->getMockTranslator()
        );

        $result = $handler->handle(new GetBusinessEntityForViewing(5));

        $this->assertSame('', $result->getCustomerGroupName());
        $this->assertSame('Acme', $result->getName());
        $this->assertSame('active', $result->getStatus());
        $this->assertSame('Active', $result->getStatusLabel());
        $this->assertSame('2026-01-01 10:00:00', $result->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(0, $result->getAddressesCount());
        $this->assertSame(4, $result->getLinkedCustomersCount(), 'AC11 summary count must reach the DTO');
    }

    public function testItMapsTheDefaultFlagAndSplitsAddressesByTypeWithoutCountingTheSharedOneTwice(): void
    {
        // AC10 asks for the billing address "with default indicator if applicable": the is_default
        // column must reach AddressForViewing::isDefault(), which is what the Default badge of
        // _address_card.html.twig renders. A "both" address belongs to the two lists but counts once.
        $entity = $this->getMockBusinessEntity();
        $entity->method('getBusinessEntityIdentifiers')->willReturn(new ArrayCollection());

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($entity);
        $repository->method('getLinkedCustomersCount')->willReturn(0);

        // Keys and order mirror what BusinessEntityAddressRepository::getAddresses() selects.
        $addressRepository = $this->createMock(BusinessEntityAddressRepository::class);
        $addressRepository->method('getAddresses')->willReturn([
            ['id_address' => 10, 'alias' => 'HQ', 'address_type' => 'both', 'is_default' => '1'],
            ['id_address' => 11, 'alias' => 'Warehouse', 'address_type' => 'delivery', 'is_default' => '0'],
        ]);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider->method('getGroups')->willReturn([['id_group' => 99, 'name' => 'Customers B2B']]);

        $handler = new GetBusinessEntityForViewingHandler(
            $repository,
            $addressRepository,
            $groupDataProvider,
            $this->getMockLanguageContext(),
            $this->getMockShopContext(true),
            $this->createMock(AddressFormatterInterface::class),
            $this->getMockTranslator()
        );

        $result = $handler->handle(new GetBusinessEntityForViewing(5));

        $invoiceAddresses = $result->getInvoiceAddresses();
        $this->assertCount(1, $invoiceAddresses, 'a "both" address is a billing address too');
        $this->assertSame(10, $invoiceAddresses[0]->getAddressId());
        $this->assertSame('HQ', $invoiceAddresses[0]->getAlias());
        $this->assertTrue($invoiceAddresses[0]->isDefault(), 'AC10 default indicator must reach the DTO');

        $deliveryAddresses = $result->getDeliveryAddresses();
        $this->assertCount(2, $deliveryAddresses, 'the "both" address is also a shipping address');
        $this->assertSame([10, 11], array_map(
            static fn ($address): int => $address->getAddressId(),
            $deliveryAddresses
        ));
        $this->assertFalse($deliveryAddresses[1]->isDefault(), 'a non-default address must not be flagged');

        $this->assertSame(2, $result->getAddressesCount(), 'AC11 counts distinct addresses, not links');
    }

    public function testItSkipsDeletedIdentifierTypesAndOrdersTheRemainingOnesById(): void
    {
        // mapIdentifiers() feeds the "Company information" section: a deleted identifier type must
        // never surface, and the display order must be deterministic (ksort) whatever the
        // collection order.
        $entity = $this->getMockBusinessEntity();
        $entity->method('getBusinessEntityIdentifiers')->willReturn(new ArrayCollection([
            $this->buildIdentifier(3, 'DUNS', '123456789', false),
            $this->buildIdentifier(1, 'VAT number', 'FR123', false),
            $this->buildIdentifier(2, 'Retired scheme', 'X', true),
        ]));

        $repository = $this->createMock(BusinessEntityRepository::class);
        $repository->method('findById')->willReturn($entity);
        $repository->method('getLinkedCustomersCount')->willReturn(0);

        $addressRepository = $this->createMock(BusinessEntityAddressRepository::class);
        $addressRepository->method('getAddresses')->willReturn([]);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider->method('getGroups')->willReturn([['id_group' => 99, 'name' => 'Customers B2B']]);

        $handler = new GetBusinessEntityForViewingHandler(
            $repository,
            $addressRepository,
            $groupDataProvider,
            $this->getMockLanguageContext(),
            $this->getMockShopContext(true),
            $this->createMock(AddressFormatterInterface::class),
            $this->getMockTranslator()
        );

        $identifiers = $handler->handle(new GetBusinessEntityForViewing(5))->getIdentifiers();

        $this->assertCount(2, $identifiers, 'the deleted identifier type must be skipped');
        $this->assertSame([1, 3], array_map(
            static fn ($identifier): int => $identifier->getBusinessIdentifierId(),
            $identifiers
        ));
        $this->assertSame('VAT number', $identifiers[0]->getLabel());
        $this->assertSame('FR123', $identifiers[0]->getValue());
    }

    /**
     * @return BusinessEntity&MockObject
     */
    private function getMockBusinessEntity(): BusinessEntity
    {
        $entity = $this->createMock(BusinessEntity::class);
        $entity->method('getId')->willReturn(5);
        $entity->method('getExternalRef')->willReturn(null);
        $entity->method('getName')->willReturn('Acme');
        $entity->method('getLegalName')->willReturn(null);
        $entity->method('isDeliveryAuthorized')->willReturn(true);
        $entity->method('getStatus')->willReturn(BusinessEntityStatus::ACTIVE);
        $entity->method('getCreatedAt')->willReturn(new DateTime('2026-01-01 10:00:00'));
        $entity->method('getUpdatedAt')->willReturn(new DateTime('2026-01-02 11:00:00'));
        $entity->method('getIdCustomerGroup')->willReturn(99);

        return $entity;
    }

    private function buildIdentifier(int $id, string $label, string $value, bool $isDeleted): BusinessEntityIdentifier
    {
        $businessIdentifier = $this->createMock(BusinessIdentifier::class);
        $businessIdentifier->method('getId')->willReturn($id);
        $businessIdentifier->method('getLabel')->willReturn($label);
        $businessIdentifier->method('isDeleted')->willReturn($isDeleted);

        $businessEntityIdentifier = $this->createMock(BusinessEntityIdentifier::class);
        $businessEntityIdentifier->method('getBusinessIdentifier')->willReturn($businessIdentifier);
        $businessEntityIdentifier->method('getValue')->willReturn($value);

        return $businessEntityIdentifier;
    }

    private function getMockTranslator(): TranslatorInterface
    {
        $mock = $this->createMock(TranslatorInterface::class);
        $mock->method('trans')->willReturnCallback(static fn (string $id): string => $id);

        return $mock;
    }

    private function getMockLanguageContext(): LanguageContext
    {
        $mock = $this->createMock(LanguageContext::class);
        $mock->method('getId')->willReturn(1);

        return $mock;
    }

    private function getMockShopContext(bool $isAllShop, array $associatedShopIds = []): ShopContext
    {
        $mock = $this->createMock(ShopContext::class);
        $mock->method('isAllShopContext')->willReturn($isAllShop);
        $mock->method('getAssociatedShopIds')->willReturn($associatedShopIds);

        return $mock;
    }
}
