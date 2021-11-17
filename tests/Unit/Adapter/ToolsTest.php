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

namespace Tests\Unit\Adapter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Tools;

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
    public function testBcAdd(string $leftOperand, string $rightOperand, int $scale, string $expectedResult): void
    {
        $this->assertSame($expectedResult, (new Tools())->bcadd($leftOperand, $rightOperand, $scale));
    }

    public function provideTestCasesForBcAdd(): iterable
    {
        yield ['1.234', '5', 4, '6.2340'];
        yield ['5', '1.234', 4, '6.2340'];
        yield ['10', '0.0000000', 6, '10.000000'];
        yield ['0.0000000', '10', 6, '10.000000'];
        yield ['0.0', '0.00000002', 2, '0.00'];
    }
}
