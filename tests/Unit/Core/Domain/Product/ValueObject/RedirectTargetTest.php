<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;

class RedirectTargetTest extends TestCase
{
    /**
     * @dataProvider getValidDataForCreation
     *
     * @param int $targetValue
     * @param bool $isNoTarget
     *
     * @throws ProductConstraintException
     */
    public function testItSuccessfullyCreatesRedirectTarget(int $targetValue, bool $isNoTarget): void
    {
        $redirectTarget = new RedirectTarget($targetValue);

        Assert::assertSame($targetValue, $redirectTarget->getValue());
        Assert::assertSame($isNoTarget, $redirectTarget->isNoTarget());
    }

    /**
     * @return Generator
     */
    public function getValidDataForCreation(): Generator
    {
        yield [1, false];
        yield [0, true];
    }

    /**
     * @dataProvider getInvalidDataForCreation
     *
     * @param int $targetValue
     *
     * @throws ProductConstraintException
     */
    public function testItThrowsExceptionWhenInvalidRedirectTargetIsProvided(int $targetValue): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_REDIRECT_TARGET);

        new RedirectTarget($targetValue);
    }

    /**
     * @return Generator
     */
    public function getInvalidDataForCreation(): Generator
    {
        yield [-1];
        yield [-500];
    }
}
