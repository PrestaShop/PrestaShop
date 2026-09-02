<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
