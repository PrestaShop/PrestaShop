<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\EditBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class EditBusinessEntityCommandTest extends TestCase
{
    public function testItExposesAllConstructorParamsViaGetters(): void
    {
        $command = new EditBusinessEntityCommand(
            7,
            'My Business Entity',
            'Legal Name SAS',
            'EXT-007',
            true,
            BusinessEntityStatus::ACTIVE,
            5,
        );

        $this->assertSame(7, $command->getBusinessEntityId()->getValue());
        $this->assertSame('My Business Entity', $command->getName());
        $this->assertSame('Legal Name SAS', $command->getLegalName());
        $this->assertSame('EXT-007', $command->getExternalRef());
        $this->assertTrue($command->isDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $command->getStatus());
        $this->assertSame(5, $command->getCustomerGroupId());
    }

    public function testItAcceptsNullExternalRef(): void
    {
        $command = new EditBusinessEntityCommand(
            1,
            'Name',
            'Legal',
            null,
            false,
            BusinessEntityStatus::PENDING,
            3,
        );

        $this->assertNull($command->getExternalRef());
        $this->assertFalse($command->isDeliveryAuthorized());
    }

    public function testItRejectsNonPositiveId(): void
    {
        $this->expectException(BusinessEntityConstraintException::class);
        $this->expectExceptionCode(BusinessEntityConstraintException::INVALID_ID);

        new EditBusinessEntityCommand(0, 'Name', 'Legal', null, false, BusinessEntityStatus::PENDING, 3);
    }
}
