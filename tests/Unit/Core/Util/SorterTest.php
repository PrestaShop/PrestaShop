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

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Sorter;

class SorterTest extends TestCase
{
    /**
     * @dataProvider dataProviderNatural
     *
     * @param array $expected
     * @param array $array
     * @param string $order
     * @param string $criteria1
     * @param string $criteria2
     *
     * @return void
     */
    public function testNatural(
        array $expected,
        array $array,
        string $order,
        string $criteria1 = '',
        string $criteria2 = ''
    ): void {
        $sorter = new Sorter();
        $this->assertEquals(
            $expected,
            $sorter->natural($array, $order, $criteria1, $criteria2)
        );
    }

    public function dataProviderNatural(): iterable
    {
        yield [
            [],
            [],
            Sorter::ORDER_ASC,
        ];

        // Array with one criteria
        yield [
            [
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            [
                ['keyA' => 'b', 'keyB' => 'b'],
                ['keyA' => 'a', 'keyB' => 'a'],
            ],
            Sorter::ORDER_DESC,
            'keyA',
        ];

        yield [
            [
                ['keyA' => 'b', 'keyB' => 'b'],
                ['keyA' => 'a', 'keyB' => 'a'],
            ],
            [
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            Sorter::ORDER_ASC,
            'keyA',
        ];

        // Array with one criteria which doesn't exist
        yield [
            [
                ['keyA' => 'b', 'keyB' => 'b'],
                ['keyA' => 'a', 'keyB' => 'a'],
            ],
            [
                ['keyA' => 'b', 'keyB' => 'b'],
                ['keyA' => 'a', 'keyB' => 'a'],
            ],
            Sorter::ORDER_DESC,
            'keyC',
        ];

        // Array with two criterias
        yield [
            [
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            [
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            Sorter::ORDER_DESC,
            'keyA',
            'keyB',
        ];

        yield [
            [
                ['keyA' => 'b', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'a', 'keyB' => 'a'],
            ],
            [
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            Sorter::ORDER_ASC,
            'keyA',
            'keyB',
        ];

        // Array with two criterias which the first doesn't exist
        yield [
            [
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            [
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            Sorter::ORDER_DESC,
            'keyC',
            'keyB',
        ];

        // Array with two criterias which the second doesn't exist
        yield [
            [
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            [
                ['keyA' => 'a', 'keyB' => 'b'],
                ['keyA' => 'b', 'keyB' => 'a'],
                ['keyA' => 'a', 'keyB' => 'a'],
                ['keyA' => 'b', 'keyB' => 'b'],
            ],
            Sorter::ORDER_DESC,
            'keyA',
            'keyC',
        ];
    }
}
