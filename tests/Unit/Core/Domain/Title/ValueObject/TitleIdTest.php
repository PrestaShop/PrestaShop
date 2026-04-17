<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Title\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;

class TitleIdTest extends TestCase
{
    public function testItThrowsExceptionWhenStoreIdIsLowerThanZero(): void
    {
        $this->expectException(TitleConstraintException::class);
        $storeId = new TitleId(-3);
    }

    public function testItThrowsExceptionWhenStoreIdEqualsZero(): void
    {
        $this->expectException(TitleConstraintException::class);
        $storeId = new TitleId(0);
    }

    public function testItAffectsGoodValueIFStoreIdIsPositive(): void
    {
        $valueInitialized = 2;
        $storeId = new TitleId($valueInitialized);
        $this->assertEquals($storeId->getValue(), $valueInitialized);
    }
}
