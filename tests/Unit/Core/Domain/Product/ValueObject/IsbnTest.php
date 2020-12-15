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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;

class IsbnTest extends TestCase
{
    public function testItIsSuccessfullyConstructed(): void
    {
        $isbn = new Isbn('978-3-16-148410-0');
        Assert::assertSame('978-3-16-148410-0', $isbn->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param string $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(string $value): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_ISBN);

        new Isbn($value);
    }

    /**
     * @return Generator
     */
    public function getInvalidValues(): Generator
    {
        yield ['12345678901234567890123412345678901234-33'];
        yield ['what'];
        yield ['!'];
        yield ['@'];
        yield ['$'];
        yield ['%s'];
        yield ['^'];
        yield ['&'];
        yield ['*'];
        yield ['('];
        yield [')'];
        yield ['+'];
        yield ['='];
        yield ['{'];
        yield ['}'];
        yield ['['];
        yield ['['];
        yield ['<'];
        yield ['>'];
        yield ['?'];
        yield ['/'];
        yield ['\\'];
        yield ['\''];
        yield [';'];
        yield [':'];
        yield ['.'];
        yield [','];
    }
}
