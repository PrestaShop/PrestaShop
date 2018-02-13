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

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Tools;

class ToolsCoreTest extends TestCase
{
    protected function setUp() {
        $_POST = array();
        $_GET = array();
        Tools::resetRequest();
    }

    private function setPostAndGet(array $post = array(), array $get = array())
    {
        $_POST = $post;
        $_GET = $get;

        return $this;
    }

    public function testGetValueBaseCase()
    {
        $this->setPostAndGet(array('hello' => 'world'));
        $this->assertEquals('world', Tools::getValue('hello'));
    }

    public function testGetValueDefaultValueIsFalse()
    {
        $this->setPostAndGet();
        $this->assertEquals(false, Tools::getValue('hello'));
    }

    public function testGetValueUsesDefaultValue()
    {
        $this->setPostAndGet();
        $this->assertEquals('I AM DEFAULT', Tools::getValue('hello', 'I AM DEFAULT'));
    }

    public function testGetValuePrefersPost()
    {
        $this->setPostAndGet(array('hello' => 'world'), array('hello' => 'cruel world'));
        $this->assertEquals('world', Tools::getValue('hello'));
    }

    public function testGetValueAcceptsOnlyTruthyStringsAsKeys()
    {
        $this->setPostAndGet(array(
            '' => true,
            ' ' => true,
            null => true
        ));

        $this->assertEquals(false, Tools::getValue('', true));
        $this->assertEquals(true, Tools::getValue(' '));
        $this->assertEquals(false, Tools::getValue(null, true));
    }

    public function testGetValueStripsNullCharsFromReturnedStringsExamples()
    {
        return array(
            array("\0", ''),
            array("haxx\0r", 'haxxr'),
            array("haxx\0\0\0r", 'haxxr'),
        );
    }

    /**
     * @dataProvider testGetValueStripsNullCharsFromReturnedStringsExamples
     */
    public function testGetValueStripsNullCharsFromReturnedStrings($rawString, $cleanedString)
    {
        /**
         * Check it cleans values stored in POST
         */
        $this->setPostAndGet(array('rawString' => $rawString));
        $this->assertEquals($cleanedString, Tools::getValue('rawString'));

        /**
         * Check it cleans values stored in GET
         */
        $this->setPostAndGet(array(), array('rawString' => $rawString));
        $this->assertEquals($cleanedString, Tools::getValue('rawString'));

        /**
         * Check it cleans default values too
         */
        $this->setPostAndGet();
        $this->assertEquals($cleanedString, Tools::getValue('NON EXISTING KEY', $rawString));
    }

    public function testSpreadAmountExamples()
    {
        return array(
            array(
                // base case
                array(array('a' => 2), array('a' => 1)), // expected result
                1, 0,                                     // amount and precision
                array(array('a' => 1), array('a' => 1)), // source rows
                'a'                                         // sort column
            ),
            array(
                // check with 1 decimal
                array(array('a' => 1.5), array('a' => 1.5)),
                1, 1,
                array(array('a' => 1), array('a' => 1)),
                'a'
            ),
            array(
                // 2 decimals, but only one really needed
                array(array('a' => 1.5), array('a' => 1.5)),
                1, 2,
                array(array('a' => 1), array('a' => 1)),
                'a'
            ),
            array(
                // check that the biggest "a" gets the adjustment
                array(array('a' => 3), array('a' => 1)),
                1, 0,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // check it works with amount > count($rows)
                array(array('a' => 4), array('a' => 2)),
                3, 0,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // 2 decimals
                array(array('a' => 2.01), array('a' => 1)),
                0.01, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // 2 decimals, equal level of adjustment
                array(array('a' => 2.01), array('a' => 1.01)),
                0.02, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // 2 decimals, different levels of adjustmnt
                array(array('a' => 2.02), array('a' => 1.01)),
                0.03, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                // check associative arrays are OK too
                array(array('a' => 2.01), array('a' => 1.01)),
                0.02, 2,
                array('z' => array('a' => 1), 'x' => array('a' => 2)),
                'a'
            ),
            array(
                // check amount is rounded if it needs more precision than asked for
                array(array('a' => 2.02), array('a' => 1.01)),
                0.025, 2,
                array(array('a' => 1), array('a' => 2)),
                'a'
            ),
            array(
                array(array('a' => 7.69), array('a' => 4.09), array('a' => 1.8)),
                -0.32, 2,
                array(array('a' => 7.8), array('a' => 4.2), array('a' => 1.9)),
                'a'
            )
        );
    }

    /**
     * @dataProvider testSpreadAmountExamples
     */
    public function testSpreadAmount($expectedRows, $amount, $precision, $rows, $column)
    {
        Tools::spreadAmount($amount, $precision, $rows, $column);
        $this->assertEquals(array_values($expectedRows), array_values($rows));
    }

    public static function tearDownAfterClass() {
        $_POST = array();
        $_GET = array();
    }
}
