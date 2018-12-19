<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Filter;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

class HashMapWhitelistFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $subject
     * @param array $whitelist
     * @param array $expectedResult
     *
     * @dataProvider provideTestCases
     */
    public function testItOnlyKeepsWhitelistedKeysWithoutLosingValues($subject, $whitelist, $expectedResult)
    {
        $filter = new HashMapWhitelistFilter();
        $filter->whitelist($whitelist);

        $result = $filter->filter($subject);

        $this->assertSame($expectedResult, $result);
    }

    public function testKeysCanBeRemovedFromWhitelist()
    {
        $subject = [
            'foo' => 'something',
            'bar' => null,
            'baz' => [],
        ];

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'foo', 'bar',
        ]);

        $expected = [
            'foo' => 'something',
            'bar' => null,
        ];

        $this->assertSame($expected, $filter->filter($subject));

        // remove 'foo' from whitelist and filter again
        $filter->removeFromWhitelist('foo');
        $expected = [
            'bar' => null,
        ];

        $this->assertSame($expected, $filter->filter($subject));
    }

    public function testKeysCanBeAddedToWhitelist()
    {
        $subject = [
            'foo' => 'something',
            'bar' => null,
            'baz' => [],
        ];

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist([
            'foo',
        ]);

        $expected = [
            'foo' => 'something',
        ];

        $this->assertSame($expected, $filter->filter($subject));

        // add 'bar' to the whitelist and filter again
        $filter->whitelist(['bar']);
        $expected = [
            'foo' => 'something',
            'bar' => null,
        ];

        $this->assertSame($expected, $filter->filter($subject));
    }

    public function provideTestCases()
    {
        $basicArray = [
            'foo' => 'something',
            'bar' => null,
            'baz' => [],
        ];

        $nestedArray = [
            'foo' => 'something',
            'bar' => null,
            'baz' => $basicArray,
        ];

        return [
            'keep 1st'         => [
                'subject'   => $basicArray,
                'whitelist' => [
                    'foo',
                ],
                'expected'  => [
                    'foo' => 'something',
                ],
            ],
            'keep 2nd'         => [
                'subject'   => $basicArray,
                'whitelist' => [
                    'bar',
                ],
                'expected'  => [
                    'bar' => null,
                ],
            ],
            'keep 3rd'         => [
                'subject'   => $basicArray,
                'whitelist' => [
                    'baz',
                ],
                'expected'  => [
                    'baz' => array(),
                ],
            ],
            'keep 1st and 2nd' => [
                'subject'   => $basicArray,
                'whitelist' => [
                    'foo', 'bar',
                ],
                'expected'  => [
                    'foo' => 'something',
                    'bar' => null,
                ],
            ],
            'keep all'         => [
                'subject'   => $basicArray,
                'whitelist' => [
                    'foo', 'bar', 'baz',
                ],
                'expected'  => [
                    'foo' => 'something',
                    'bar' => null,
                    'baz' => [],
                ],
            ],
            'keep none'        => [
                'subject'   => $basicArray,
                'whitelist' => [],
                'expected'  => [],
            ],
            'nested filter' => [
                'subject'   => $nestedArray,
                'whitelist' => [
                    'foo',
                    'baz' => (new HashMapWhitelistFilter())->whitelist(['foo', 'baz']),
                ],
                'expected' => [
                    'foo' => 'something',
                    'baz' => [
                        'foo' => 'something',
                        'baz' => [],
                    ],
                ],
            ],
        ];
    }
}
