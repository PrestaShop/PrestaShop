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

namespace Tests\Unit\Core\Domain\Product\Stock\Query;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class GetCombinationStockMovementsTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     */
    public function testItIsSuccessfullyConstructed(
        int $combinationId,
        int $shopId,
        int $offset,
        int $limit
    ): void {
        $query = new GetCombinationStockMovements(
            $combinationId,
            $shopId,
            $offset,
            $limit
        );
        Assert::assertSame($combinationId, $query->getCombinationId()->getValue());
        Assert::assertSame($offset, $query->getOffset());
        Assert::assertSame($limit, $query->getLimit());
    }

    public function getValidValues(): Generator
    {
        yield 'nominal case' => [
            'combinationId' => 1,
            'shopId' => 1,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'pagination case' => [
            'combinationId' => 10,
            'shopId' => 10,
            'offset' => 10,
            'limit' => 10,
        ];
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testItThrowsExceptionWhenInvalidValueIsProvided(
        string $exceptionClass,
        int $combinationId,
        int $shopId,
        int $offset,
        int $limit
    ): void {
        $this->expectException($exceptionClass);

        new GetCombinationStockMovements(
            $combinationId,
            $shopId,
            $offset,
            $limit
        );
    }

    public function getInvalidValues(): Generator
    {
        yield 'combinationId is zero' => [
            'exceptionClass' => CombinationConstraintException::class,
            'combinationId' => 0,
            'shopId' => 1,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'combinationId is negative' => [
            'exceptionClass' => CombinationConstraintException::class,
            'combinationId' => -1,
            'shopId' => 1,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'shopId is zero' => [
            'exceptionClass' => ShopException::class,
            'combinationId' => 1,
            'shopId' => 0,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'shopId is negative' => [
            'exceptionClass' => ShopException::class,
            'combinationId' => 1,
            'shopId' => -1,
            'offset' => 0,
            'limit' => 0,
        ];
        yield 'offset is negative' => [
            'exceptionClass' => InvalidArgumentException::class,
            'combinationId' => 1,
            'shopId' => 1,
            'offset' => -1,
            'limit' => 0,
        ];
        yield 'limit is negative' => [
            'exceptionClass' => InvalidArgumentException::class,
            'combinationId' => 1,
            'shopId' => 1,
            'offset' => 0,
            'limit' => -1,
        ];
    }
}
