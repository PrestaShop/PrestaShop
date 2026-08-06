<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Country\AddressFormat;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatFieldsProvider;
use RuntimeException;

class AddressFormatFieldsProviderTest extends TestCase
{
    public function testGetPickerClassesReturnsExpectedOrderedList(): void
    {
        $provider = new AddressFormatFieldsProvider($this->fakeQueryBus([]));

        $this->assertSame(
            ['Address', 'Country', 'State', 'Customer', 'Warehouse'],
            $provider->getPickerClasses()
        );
    }

    public function testGetFieldsForKnownClassReturnsHardcodedList(): void
    {
        $provider = new AddressFormatFieldsProvider($this->fakeQueryBus([]));

        $this->assertSame(
            ['firstname', 'lastname', 'company', 'vat_number', 'address1', 'address2', 'postcode', 'city', 'other', 'phone', 'phone_mobile', 'dni'],
            $provider->getFieldsForClass('Address')
        );
        $this->assertSame(['name', 'iso_code'], $provider->getFieldsForClass('Country'));
        $this->assertSame(['name', 'iso_code'], $provider->getFieldsForClass('State'));
        $this->assertSame(
            ['firstname', 'lastname', 'company', 'vat_number', 'email', 'birthday', 'website', 'siret'],
            $provider->getFieldsForClass('Customer')
        );
        $this->assertSame(
            ['name', 'reference', 'management_type'],
            $provider->getFieldsForClass('Warehouse')
        );
    }

    public function testGetFieldsForUnknownClassReturnsEmptyList(): void
    {
        $provider = new AddressFormatFieldsProvider($this->fakeQueryBus([]));

        $this->assertSame([], $provider->getFieldsForClass('Manufacturer'));
        $this->assertSame([], $provider->getFieldsForClass('Supplier'));
        $this->assertSame([], $provider->getFieldsForClass('NotAThing'));
    }

    public function testGetRequiredFieldsMergesStaticDefaultsWithDbManagedList(): void
    {
        $provider = new AddressFormatFieldsProvider(
            $this->fakeQueryBus(['phone_mobile', 'company'])
        );

        $this->assertSame(
            ['firstname', 'lastname', 'address1', 'city', 'Country:name', 'phone_mobile', 'company'],
            $provider->getRequiredFields()
        );
    }

    public function testGetRequiredFieldsDeduplicatesOverlappingEntries(): void
    {
        // Merchant flagged `firstname` (which is already in the static defaults) as
        // required in BO > Customers > Addresses — must appear exactly once.
        $provider = new AddressFormatFieldsProvider(
            $this->fakeQueryBus(['firstname', 'phone_mobile'])
        );

        $this->assertSame(
            ['firstname', 'lastname', 'address1', 'city', 'Country:name', 'phone_mobile'],
            $provider->getRequiredFields()
        );
    }

    public function testGetRequiredFieldsWithEmptyDbListReturnsOnlyStaticDefaults(): void
    {
        $provider = new AddressFormatFieldsProvider($this->fakeQueryBus([]));

        $this->assertSame(
            ['firstname', 'lastname', 'address1', 'city', 'Country:name'],
            $provider->getRequiredFields()
        );
    }

    /**
     * @param list<string> $dbManagedFields
     */
    private function fakeQueryBus(array $dbManagedFields): CommandBusInterface
    {
        return new class($dbManagedFields) implements CommandBusInterface {
            public function __construct(private readonly array $dbManagedFields)
            {
            }

            public function handle($command): mixed
            {
                if ($command instanceof GetRequiredFieldsForAddress) {
                    return $this->dbManagedFields;
                }
                throw new RuntimeException('Unexpected query: ' . get_class($command));
            }
        };
    }
}
