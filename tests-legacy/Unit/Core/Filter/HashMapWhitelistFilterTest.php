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

class HashMapWhitelistFilterTest extends \PHPUnit_Framework_TestCase
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
        $subject = array(
            'foo' => 'something',
            'bar' => null,
            'baz' => array(),
        );

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist(array(
            'foo', 'bar'
        ));

        $expected = array(
            'foo' => 'something',
            'bar' => null,
        );

        $this->assertSame($expected, $filter->filter($subject));

        // remove 'foo' from whitelist and filter again
        $filter->removeFromWhitelist('foo');
        $expected = array(
            'bar' => null,
        );

        $this->assertSame($expected, $filter->filter($subject));
    }

    public function testKeysCanBeAddedToWhitelist()
    {
        $subject = array(
            'foo' => 'something',
            'bar' => null,
            'baz' => array(),
        );

        $filter = new HashMapWhitelistFilter();
        $filter->whitelist(array(
            'foo'
        ));

        $expected = array(
            'foo' => 'something',
        );

        $this->assertSame($expected, $filter->filter($subject));

        // add 'bar' to the whitelist and filter again
        $filter->whitelist(array('bar'));
        $expected = array(
            'foo' => 'something',
            'bar' => null,
        );

        $this->assertSame($expected, $filter->filter($subject));
    }

    public function provideTestCases()
    {
        $basicArray = array(
            'foo' => 'something',
            'bar' => null,
            'baz' => array(),
        );

        $nestedArray = array(
            'foo' => 'something',
            'bar' => null,
            'baz' => $basicArray,
        );

        return array(
            'keep 1st' => array(
                'subject' => $basicArray,
                'whitelist' => array(
                    'foo',
                ),
                'expected' => array(
                    'foo' => 'something',
                ),
            ),
            'keep 2nd' => array(
                'subject' => $basicArray,
                'whitelist' => array(
                    'bar',
                ),
                'expected' => array(
                    'bar' => null,
                ),
            ),
            'keep 3rd' => array(
                'subject' => $basicArray,
                'whitelist' => array(
                    'baz',
                ),
                'expected' => array(
                    'baz' => array(),
                ),
            ),
            'keep 1st and 2nd' => array(
                'subject' => $basicArray,
                'whitelist' => array(
                    'foo', 'bar',
                ),
                'expected' => array(
                    'foo' => 'something',
                    'bar' => null,
                ),
            ),
            'keep all' => array(
                'subject' => $basicArray,
                'whitelist' => array(
                    'foo', 'bar', 'baz',
                ),
                'expected' => array(
                    'foo' => 'something',
                    'bar' => null,
                    'baz' => array(),
                ),
            ),
            'keep none' => array(
                'subject' => $basicArray,
                'whitelist' => array(),
                'expected' => array(),
            ),
            'nested filter' => array(
                'subject' => $nestedArray,
                'whitelist' => array(
                    'foo',
                    'baz' => (new HashMapWhitelistFilter())->whitelist(array('foo', 'baz'))
                ),
                'expected' => array(
                    'foo' => 'something',
                    'baz' => array(
                        'foo' => 'something',
                        'baz' => array()
                    ),
                )
            )
        );
    }

}
