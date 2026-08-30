<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityGeneralInformation;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class BusinessEntityGeneralInformationTest extends TestCase
{
    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $generalInformation = new BusinessEntityGeneralInformation(
            'My Business Entity',
            'Legal Name SAS',
            'EXT-007',
            true,
            BusinessEntityStatus::ACTIVE,
            42,
            5,
        );

        $this->assertSame('My Business Entity', $generalInformation->getName());
        $this->assertSame('Legal Name SAS', $generalInformation->getLegalName());
        $this->assertSame('EXT-007', $generalInformation->getExternalRef());
        $this->assertTrue($generalInformation->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $generalInformation->getStatus());
        $this->assertSame(42, $generalInformation->getShopId());
        $this->assertSame(5, $generalInformation->getCustomerGroupId());
    }

    public function testItAcceptsNullableExternalRef(): void
    {
        $generalInformation = new BusinessEntityGeneralInformation(
            'Name',
            'Legal',
            null,
            false,
            BusinessEntityStatus::PENDING,
            1,
            3,
        );

        $this->assertNull($generalInformation->getExternalRef());
        $this->assertFalse($generalInformation->isDeliveryAuthorized());
    }
}
