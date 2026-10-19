<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\BulkDeleteBusinessEntityCommand;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;

class BulkDeleteBusinessEntityCommandTest extends TestCase
{
    public function testItWrapsEveryIdInTheDomainValueObject(): void
    {
        $command = new BulkDeleteBusinessEntityCommand([4, 8, 15]);

        $ids = $command->getBusinessEntityIds();
        $this->assertCount(3, $ids);
        $this->assertContainsOnlyInstancesOf(BusinessEntityId::class, $ids);
        $this->assertSame([4, 8, 15], array_map(static fn (BusinessEntityId $id): int => $id->getValue(), $ids));
    }

    public function testItRejectsANonPositiveIdJustLikeTheSingleDeleteCommand(): void
    {
        $this->expectException(BusinessEntityConstraintException::class);
        $this->expectExceptionCode(BusinessEntityConstraintException::INVALID_ID);

        new BulkDeleteBusinessEntityCommand([4, 0, 15]);
    }

    public function testAnEmptySelectionYieldsNoIds(): void
    {
        $this->assertSame([], (new BulkDeleteBusinessEntityCommand([]))->getBusinessEntityIds());
    }
}
