<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Combination\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class UpdateCombinationStockAvailableCommandTest extends TestCase
{
    public function testItThrowsExceptionWhenTryingToSetFixedQuantityWhenDeltaQuantityIsAlreadySet(): void
    {
        $command = new UpdateCombinationStockAvailableCommand(1, ShopConstraint::allShops());
        $this->expectException(ProductStockConstraintException::class);
        $this->expectExceptionCode(ProductStockConstraintException::FIXED_AND_DELTA_QUANTITY_PROVIDED);

        $command->setDeltaQuantity(10);
        $command->setFixedQuantity(1);
    }

    public function testItThrowsExceptionWhenTryingToSetDeltaQuantityWhenFixedQuantityIsAlreadySet(): void
    {
        $command = new UpdateCombinationStockAvailableCommand(1, ShopConstraint::allShops());
        $this->expectException(ProductStockConstraintException::class);
        $this->expectExceptionCode(ProductStockConstraintException::FIXED_AND_DELTA_QUANTITY_PROVIDED);

        $command->setFixedQuantity(1);
        $command->setDeltaQuantity(10);
    }
}
