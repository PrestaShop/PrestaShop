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

namespace Tests\Unit\Core\Domain\Product\Stock\Query;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\GetStockMovementHistoriesConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetStockMovementHistories;

class GetStockMovementHistoriesTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     */
    public function testItIsSuccessfullyConstructed(int $productId, int $offset, int $limit): void
    {
        $query = new GetStockMovementHistories(
            $productId,
            $offset,
            $limit
        );
        Assert::assertSame($productId, $query->getProductId()->getValue());
        Assert::assertSame($offset, $query->getOffset());
        Assert::assertSame($limit, $query->getLimit());
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(int $productId, int $offset, int $limit): void
    {
        $this->expectException(GetStockMovementHistoriesConstraintException::class);

        new GetStockMovementHistories(
            $productId,
            $offset,
            $limit
        );
    }

    public function getValidValues(): Generator
    {
        yield 'nominal case' => [
            'productId' => 1,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'productId is 0' => [
            'productId' => 0,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'pagination case' => [
            'productId' => 10,
            'offset' => 10,
            'limit' => 10,
        ];
    }

    public function getInvalidValues(): Generator
    {
        yield 'productId is negative' => [
            'productId' => -1,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'offset is negative' => [
            'productId' => 1,
            'offset' => -1,
            'limit' => 0,
        ];
        yield 'limit is negative' => [
            'productId' => 1,
            'offset' => 0,
            'limit' => -1,
        ];
    }
}
