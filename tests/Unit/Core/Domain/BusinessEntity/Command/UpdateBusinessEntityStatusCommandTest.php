<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\UpdateBusinessEntityStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShopBundle\Entity\Enum\BusinessEntityStatus;

class UpdateBusinessEntityStatusCommandTest extends TestCase
{
    public function testItExposesTheBusinessEntityIdAndTheNewStatus(): void
    {
        $command = new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE);

        $this->assertSame(7, $command->getBusinessEntityId()->getValue());
        $this->assertSame(BusinessEntityStatus::ACTIVE, $command->getStatus());
    }

    public function testItExposesTheCommentWhenOneIsGiven(): void
    {
        $command = new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::REJECTED, 'Expired registration document');

        $this->assertSame('Expired registration document', $command->getComment());
    }

    public function testTheCommentIsOptional(): void
    {
        $command = new UpdateBusinessEntityStatusCommand(7, BusinessEntityStatus::ACTIVE);

        $this->assertNull($command->getComment());
    }

    public function testItRejectsNonPositiveId(): void
    {
        $this->expectException(BusinessEntityConstraintException::class);
        $this->expectExceptionCode(BusinessEntityConstraintException::INVALID_ID);

        new UpdateBusinessEntityStatusCommand(0, BusinessEntityStatus::ACTIVE);
    }
}
