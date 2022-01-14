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

namespace Tests\Unit\Adapter\Product\Stock\Repository;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockMovementFilter;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;

class StockMovementFilterTest extends TestCase
{
    public function testItReturnsStockIds(): void
    {
        $filter = (new StockMovementFilter())->setStockIds(
            new StockId(1),
            new StockId(2),
            new StockId(3)
        );
        Assert::assertSame(
            '1,2,3',
            $filter->getStockIdsAsString()
        );
        Assert::assertSame(
            '1|2|3',
            $filter->getStockIdsAsString('|')
        );
    }

    /**
     * @dataProvider getIsOrderValues
     */
    public function testItReturnsIsOrder(?bool $isOrder): void
    {
        $filter = (new StockMovementFilter())->setGroupedByOrderAssociation($isOrder);

        Assert::assertSame($isOrder, $filter->isGroupedByOrderAssociation());
    }

    public function getIsOrderValues(): Generator
    {
        yield 'isOrder is null' => [
            'isOrder' => null,
        ];
        yield 'isOrder is true' => [
            'isOrder' => true,
        ];
        yield 'isOrder is false' => [
            'isOrder' => false,
        ];
    }
}
