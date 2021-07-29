<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     *
     * @throws ProductConstraintException
     */
    public function testItSuccessfullyCreatesRedirectType(string $type, bool $isProductType, bool $isCategoryType, bool $isTypeNotFound): void
    {
        $redirectType = new RedirectType($type);

        Assert::assertSame($type, $redirectType->getValue());
        Assert::assertEquals($isProductType, $redirectType->isProductType());
        Assert::assertEquals($isCategoryType, $redirectType->isCategoryType());
        Assert::assertEquals($isTypeNotFound, $redirectType->isTypeNotFound());
    }

    /**
     * @return Generator
     */
    public function getValidDataForCreation(): Generator
    {
        yield ['404', false, false, true];
        yield ['301-category', false, true, false];
        yield ['302-category', false, true, false];
        yield ['301-product', true, false, false];
        yield ['302-product', true, false, false];
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
