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
    public function testItExposesEverySetValueViaGetters(): void
    {
        $command = (new EditBusinessEntityCommand(7))
            ->setName('My Business Entity')
            ->setLegalName('Legal Name SAS')
            ->setExternalRef('EXT-007')
            ->setDeliveryAuthorized(true)
            ->setStatus(BusinessEntityStatus::ACTIVE)
            ->setCustomerGroupId(5);

        $this->assertSame(7, $command->getBusinessEntityId()->getValue());
        $this->assertSame('My Business Entity', $command->getName());
        $this->assertSame('Legal Name SAS', $command->getLegalName());
        $this->assertSame('EXT-007', $command->getExternalRef());
        $this->assertTrue($command->getDeliveryAuthorized());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $command->getStatus());
        $this->assertSame(5, $command->getCustomerGroupId());
    }

    public function testAFreshCommandChangesNothing(): void
    {
        $command = new EditBusinessEntityCommand(7);

        $this->assertNull($command->getName());
        $this->assertNull($command->getLegalName());
        $this->assertNull($command->getExternalRef());
        $this->assertNull($command->getDeliveryAuthorized());
        $this->assertNull($command->getStatus());
        $this->assertNull($command->getCustomerGroupId());
        $this->assertFalse($command->hasExternalRef());
    }

    public function testSettersAreChainableAndIndependent(): void
    {
        $command = (new EditBusinessEntityCommand(7))->setStatus(BusinessEntityStatus::PENDING);

        $this->assertSame(BusinessEntityStatus::PENDING, $command->getStatus());
        $this->assertNull($command->getName());
        $this->assertNull($command->getCustomerGroupId());
    }

    public function testItTellsAClearedExternalRefApartFromAnUntouchedOne(): void
    {
        $untouched = new EditBusinessEntityCommand(7);
        $this->assertNull($untouched->getExternalRef());
        $this->assertFalse($untouched->hasExternalRef());

        $cleared = (new EditBusinessEntityCommand(7))->setExternalRef(null);
        $this->assertNull($cleared->getExternalRef());
        $this->assertTrue($cleared->hasExternalRef());
    }

    public function testItRejectsNonPositiveId(): void
    {
        $this->expectException(BusinessEntityConstraintException::class);
        $this->expectExceptionCode(BusinessEntityConstraintException::INVALID_ID);

        new EditBusinessEntityCommand(0);
    }
}
