<?php

namespace PrestaShop\PrestaShop\tests\Unit\Adapter;

use PrestaShop\PrestaShop\Adapter\Tools;

class ToolsTest extends \PHPUnit_Framework_TestCase
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
        return [
            ['1.234', '5', 4, '6.2340'],
            ['5', '1.234', 4, '6.2340'],
            ['10', '0.0000000', 6, '10.000000'],
            ['0.0000000', '10', 6, '10.000000'],
        ];
    }
}
