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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
