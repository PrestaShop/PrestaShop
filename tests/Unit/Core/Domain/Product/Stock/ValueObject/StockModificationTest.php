<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\Stock\ValueObject;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

class StockModificationTest extends TestCase
{
    /**
     * @dataProvider getValidDeltaQuantityValues
     *
     * @param int $deltaQuantity
     */
    public function testItIsSuccessfullyConstructedUsingDeltaQuantity(int $deltaQuantity): void
    {
        $stockModification = StockModification::buildDeltaQuantity($deltaQuantity);

        Assert::assertSame($deltaQuantity, $stockModification->getDeltaQuantity());
        Assert::assertNull($stockModification->getFixedQuantity());
    }

    /**
     * @dataProvider getValidFixedQuantityValues
     *
     * @param int $fixedQuantity
     */
    public function testItIsSuccessfullyConstructedUsingFixedQuantity(int $fixedQuantity): void
    {
        $stockModification = StockModification::buildFixedQuantity($fixedQuantity);

        Assert::assertSame($fixedQuantity, $stockModification->getFixedQuantity());
        Assert::assertNull($stockModification->getDeltaQuantity());
    }

    /**
     * @dataProvider getInvalidDeltaQuantityValues
     *
     * @param int $deltaQuantity
     */
    public function testItThrowsExceptionWhenInvalidDeltaQuantityIsProvided(int $deltaQuantity): void
    {
        $this->expectException(ProductStockConstraintException::class);
        $this->expectExceptionCode(ProductStockConstraintException::INVALID_DELTA_QUANTITY);

        StockModification::buildDeltaQuantity($deltaQuantity);
    }

    public function getValidDeltaQuantityValues(): iterable
    {
        yield [1];
        yield [10];
        yield [5000000001];
        yield [-1];
        yield [-500];
    }

    public function getValidFixedQuantityValues(): iterable
    {
        yield [1];
        yield [10];
        yield [5000000001];
        yield [-1];
        yield [-500];
        yield [0];
    }

    public function getInvalidDeltaQuantityValues(): iterable
    {
        yield [0];
    }
}
