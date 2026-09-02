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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;

class RedirectOptionTest extends TestCase
{
    /**
     * @dataProvider getValidDataForCreation
     *
     * @param string $redirectType
     * @param int $redirectTarget
     */
    public function testItSuccessfullyCreatesRedirectOption(string $redirectType, int $redirectTarget): void
    {
        $redirectOption = new RedirectOption($redirectType, $redirectTarget);

        Assert::assertSame($redirectType, $redirectOption->getRedirectType()->getValue());
        Assert::assertSame($redirectTarget, $redirectOption->getRedirectTarget()->getValue());
    }

    public function testItForcesNoRedirectTargetWhenRedirectType404IsProvided(): void
    {
        $redirectOption = new RedirectOption('404', 5);

        Assert::assertEquals(RedirectTarget::NO_TARGET, $redirectOption->getRedirectTarget()->getValue());
        Assert::assertTrue($redirectOption->getRedirectTarget()->isNoTarget());
    }

    /**
     * @return Generator
     */
    public function getValidDataForCreation(): Generator
    {
        yield ['404', 0];
        yield ['301-category', 0];
        yield ['302-category', 0];
        yield ['301-product', 4];
        yield ['302-product', 50];
    }

    /**
     * @dataProvider getInvalidDataForCreation
     *
     * @param string $redirectType
     * @param int $redirectTarget
     */
    public function testItThrowsExceptionWhenRedirectTargetIsInvalidForSpecifiedRedirectType(string $redirectType, int $redirectTarget): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_REDIRECT_TARGET);

        new RedirectOption($redirectType, $redirectTarget);
    }

    /**
     * @return Generator
     */
    public function getInvalidDataForCreation(): Generator
    {
        yield ['301-product', 0];
        yield ['302-product', 0];
    }
}
