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

namespace Tests\Unit\Core\Domain\Product\Stock\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\MovementReasonConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\MovementReasonId;

class MovementReasonIdTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     *
     * @param int $value
     */
    public function testItIsSuccessfullyConstructed(int $value): void
    {
        $specificPriceId = new MovementReasonId($value);

        Assert::assertSame($value, $specificPriceId->getValue());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param int $value
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $value): void
    {
        $this->expectException(MovementReasonConstraintException::class);

        new MovementReasonId($value);
    }

    public function getValidValues(): Generator
    {
        yield [1];
        yield [10];
        yield [5000000001];
    }

    public function getInvalidValues(): Generator
    {
        yield [0];
        yield [-1];
        yield [-999];
    }
}
