<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\QueryResult;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\AddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\BusinessEntityForViewing;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\IdentifierForViewing;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class BusinessEntityForViewingTest extends TestCase
{
    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $invoice = [$this->buildAddress(1, AddressTypeEnum::INVOICE, true)];
        $delivery = [$this->buildAddress(2, AddressTypeEnum::DELIVERY, false)];
        $identifiers = [new IdentifierForViewing(7, 'VAT number', 'FR123')];

        $businessEntity = new BusinessEntityForViewing(
            10,
            'EXT-1',
            'Tan Emporium',
            'Tan Emporium SAS',
            true,
            BusinessEntityStatus::ACTIVE,
            'Active',
            new DateTimeImmutable('2026-01-01 10:00:00'),
            new DateTimeImmutable('2026-02-02 11:00:00'),
            3,
            2,
            5,
            'Customers B2B',
            $invoice,
            $delivery,
            $identifiers,
        );

        $this->assertSame(10, $businessEntity->getBusinessEntityId());
        $this->assertSame('EXT-1', $businessEntity->getExternalRef());
        $this->assertSame('Tan Emporium', $businessEntity->getName());
        $this->assertSame('Tan Emporium SAS', $businessEntity->getLegalName());
        $this->assertTrue($businessEntity->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $businessEntity->getStatus());
        $this->assertSame('Active', $businessEntity->getStatusLabel());
        $this->assertSame('2026-01-01 10:00:00', $businessEntity->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame('2026-02-02 11:00:00', $businessEntity->getUpdatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame(3, $businessEntity->getLinkedCustomersCount());
        $this->assertSame(2, $businessEntity->getAddressesCount());
        $this->assertSame(5, $businessEntity->getCustomerGroupId());
        $this->assertSame('Customers B2B', $businessEntity->getCustomerGroupName());
        $this->assertSame($invoice, $businessEntity->getInvoiceAddresses());
        $this->assertSame($delivery, $businessEntity->getDeliveryAddresses());
        $this->assertSame($identifiers, $businessEntity->getIdentifiers());
    }

    public function testItAcceptsNullableExternalRefAndLegalName(): void
    {
        $businessEntity = new BusinessEntityForViewing(
            10,
            null,
            'Tan Emporium',
            null,
            true,
            BusinessEntityStatus::ACTIVE,
            'Active',
            new DateTimeImmutable('2026-01-01 10:00:00'),
            new DateTimeImmutable('2026-02-02 11:00:00'),
            3,
            2,
            5,
            'Customers B2B',
            [],
            [],
            [],
        );

        $this->assertNull($businessEntity->getExternalRef());
        $this->assertNull($businessEntity->getLegalName());
    }

    /**
     * @dataProvider provideNamesAndInitials
     */
    public function testItDerivesUpToTwoUppercaseInitialsFromTheName(string $name, string $expected): void
    {
        $businessEntity = $this->buildBusinessEntity($name);

        $this->assertSame($expected, $businessEntity->getInitials());
    }

    public function provideNamesAndInitials(): iterable
    {
        yield 'two words' => ['Tan Emporium', 'TE'];
        yield 'single word' => ['Acme', 'A'];
        yield 'more than two words keeps the first two' => ['Tan Emporium And Co', 'TE'];
        yield 'extra spaces are ignored' => ['  Tan   Emporium  ', 'TE'];
        yield 'already uppercase' => ['ACME CORP', 'AC'];
        yield 'multibyte name is uppercased safely' => ['éclair ölsen', 'ÉÖ'];
    }

    private function buildBusinessEntity(string $name): BusinessEntityForViewing
    {
        return new BusinessEntityForViewing(
            10,
            null,
            $name,
            null,
            true,
            BusinessEntityStatus::ACTIVE,
            'Active',
            new DateTimeImmutable('2026-01-01 10:00:00'),
            new DateTimeImmutable('2026-02-02 11:00:00'),
            0,
            0,
            5,
            'Customers B2B',
            [],
            [],
            [],
        );
    }

    private function buildAddress(int $id, AddressTypeEnum $type, bool $isDefault): AddressForViewing
    {
        return new AddressForViewing(
            $id,
            'Alias',
            "Company\n1 Street\n75000 Paris\nFrance",
            $type,
            $isDefault,
        );
    }
}
