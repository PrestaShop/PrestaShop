<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace Tests\Unit\Adapter;

use PrestaShop\PrestaShop\Adapter\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{
    /**
     * Given two numbers with arbitrary precision
     * When calling Tools:bcAdd with those numbers and a specific precision
     * Then the method should return the sum of those numbers, rounded to the specified precision
     *
     * @param string $leftOperand
     * @param string $rightOperand
     * @param int $scale
     * @param string $expectedResult
     *
     * @dataProvider provideTestCasesForBcAdd
     */
    public function testBcAdd($leftOperand, $rightOperand, $scale, $expectedResult)
    {
        $result = (new Tools())->bcadd($leftOperand, $rightOperand, $scale);
        $this->assertSame($expectedResult, $result);
    }

    public function provideTestCasesForBcAdd()
    {
        return array(
            array('1.234', '5', 4, '6.2340'),
            array('5', '1.234', 4, '6.2340'),
            array('10', '0.0000000', 6, '10.000000'),
            array('0.0000000', '10', 6, '10.000000'),
            array('0.0', '0.00000002', 2, '0.00'),
        );
    }
}
