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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Filter;

use PrestaShop\PrestaShop\Core\Filter\CollectionFilter;
use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

class CollectionFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param array $subject
     * @param array $queue
     * @param array $expectedResult
     *
     * @dataProvider provideTestCases
     *
     * @throws \PrestaShop\PrestaShop\Core\Filter\FilterException
     */
    public function testItProcessesAllItems($subject, $queue, $expectedResult)
    {
        $filter = new CollectionFilter();
        $filter->queue($queue);

        $result = $filter->filter($subject);

        $this->assertSame($expectedResult, $result);
    }

    public function provideTestCases()
    {
        $subject = [
            [
                'foo' => 'something',
                'bar' => null,
                'baz' => []
            ],
            [
                'foo' => 'something',
            ],
            [
                'bar' => null,
                'baz' => [],
            ],
            []
        ];

        return [
            [
                'subject' => $subject,
                'queue' => [
                    (new HashMapWhitelistFilter())
                        ->whitelist(['foo'])
                ],
                'expectedResult' => [
                    [
                        'foo' => 'something',
                    ],
                    [
                        'foo' => 'something',
                    ],
                    [],
                    [],
                ]
            ]
        ];
    }

}
