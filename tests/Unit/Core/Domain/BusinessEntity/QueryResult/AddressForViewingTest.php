<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult\AddressForViewing;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;

class AddressForViewingTest extends TestCase
{
    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $address = new AddressForViewing(
            12,
            'Warehouse',
            "Tan Emporium SAS\n1 Place des Ternes\n75017 Paris\nFrance",
            AddressTypeEnum::BOTH,
            true,
        );

        $this->assertSame(12, $address->getAddressId());
        $this->assertSame('Warehouse', $address->getAlias());
        $this->assertSame("Tan Emporium SAS\n1 Place des Ternes\n75017 Paris\nFrance", $address->getFormattedAddress());
        $this->assertSame(AddressTypeEnum::BOTH, $address->getAddressType());
        $this->assertTrue($address->isDefault());
    }

    public function testItKeepsTheFormattedAddressLineBreaksForRendering(): void
    {
        $address = new AddressForViewing(
            1,
            'Billing',
            "123 Main St\nNew York, New York 10001\nUnited States",
            AddressTypeEnum::INVOICE,
            false,
        );

        $this->assertSame(
            ['123 Main St', 'New York, New York 10001', 'United States'],
            explode("\n", $address->getFormattedAddress())
        );
        $this->assertSame(AddressTypeEnum::INVOICE, $address->getAddressType());
        $this->assertFalse($address->isDefault());
    }
}
