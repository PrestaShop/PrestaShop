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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;

class RedirectTypeTest extends TestCase
{
    /**
     * @dataProvider getValidDataForCreation
     *
     * @param string $type
     * @param bool $isProductType
     * @param bool $isCategoryType
     * @param bool $isTypeNotFound
     * @param bool $isTypeGone
     *
     * @throws ProductConstraintException
     */
    public function testItSuccessfullyCreatesRedirectType(
        string $type,
        bool $isProductType,
        bool $isCategoryType,
        bool $isTypeNotFound,
        bool $isTypeGone
    ): void {
        $redirectType = new RedirectType($type);

        Assert::assertSame($type, $redirectType->getValue());
        Assert::assertEquals($isProductType, $redirectType->isProductType());
        Assert::assertEquals($isCategoryType, $redirectType->isCategoryType());
        Assert::assertEquals($isTypeNotFound, $redirectType->isTypeNotFound());
        Assert::assertEquals($isTypeGone, $redirectType->isTypeGone());
    }

    /**
     * @return Generator
     */
    public function getValidDataForCreation(): Generator
    {
        yield ['404', false, false, true, false];
        yield ['410', false, false, false, true];
        yield ['301-category', false, true, false, false];
        yield ['302-category', false, true, false, false];
        yield ['301-product', true, false, false, false];
        yield ['302-product', true, false, false, false];
    }

    /**
     * @dataProvider getInvalidDataForCreation
     *
     * @param string $type
     *
     * @throws ProductConstraintException
     */
    public function testItThrowsExceptionWhenInvalidTypeIsProvided(string $type): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_REDIRECT_TYPE);

        new RedirectType($type);
    }

    public function getInvalidDataForCreation(): Generator
    {
        yield ['500'];
        yield ['303-category'];
        yield ['301-pro'];
        yield [''];
    }
}
