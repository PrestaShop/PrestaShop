<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Store\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

class StoreIdTest extends TestCase
{
    public function testItThrowsExceptionWhenStoreIdIsLowerThanZero(): void
    {
        $this->expectException(StoreConstraintException::class);
        $storeId = new StoreId(-3);
    }

    public function testItThrowsExceptionWhenStoreIdEqualsZero(): void
    {
        $this->expectException(StoreConstraintException::class);
        $storeId = new StoreId(0);
    }

    public function testItAffectsGoodValueIFStoreIdIsPositive(): void
    {
        $valueInitialized = 2;
        $storeId = new StoreId($valueInitialized);
        $this->assertEquals($storeId->getValue(), $valueInitialized);
    }
}
