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
