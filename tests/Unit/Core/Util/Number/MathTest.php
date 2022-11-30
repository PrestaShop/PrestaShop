<?php

namespace Tests\Unit\Core\Util\Number;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Number\Math;

class MathTest extends TestCase
{
    /**
     * @dataProvider providerPsRoundHelper
     */
    public function testRound(float $expectedResult, float $value, int $precision, int $mode): void
    {
        $this->assertSame($expectedResult, Math::round($value, $precision, $mode));
    }

    public function providerPsRoundHelper(): array
    {
        return [
            // 0 precision
            [26, 25.32, 0, Math::PS_ROUND_UP],
            [26, 25.52, 0, Math::PS_ROUND_UP],
            [25, 25.32, 0, Math::PS_ROUND_HALF_DOWN],
            [25, 25.50, 0, Math::PS_ROUND_HALF_DOWN],
            [25, 25.32, 0, Math::PS_ROUND_HALF_EVEN],
            [26, 25.50, 0, Math::PS_ROUND_HALF_EVEN],
            [25, 25.32, 0, Math::PS_ROUND_HALF_ODD],
            [25, 25.50, 0, Math::PS_ROUND_HALF_ODD],
            [26, 25.51, 0, Math::PS_ROUND_HALF_ODD],
            [25, 25.49, 0, Math::PS_ROUND_HALF_ODD],
            // 2 precision
            [25.33, 25.321, 2, Math::PS_ROUND_UP],
            [25.53, 25.525, 2, Math::PS_ROUND_UP],
            [25.32, 25.325, 2, Math::PS_ROUND_HALF_DOWN],
            [25.5, 25.505, 2, Math::PS_ROUND_HALF_DOWN],
            [25.32, 25.325, 2, Math::PS_ROUND_HALF_EVEN],
            [25.5, 25.505, 2, Math::PS_ROUND_HALF_EVEN],
            [25.33, 25.325, 2, Math::PS_ROUND_HALF_ODD],
            [25.51, 25.505, 2, Math::PS_ROUND_HALF_ODD],
            [25.51, 25.515, 2, Math::PS_ROUND_HALF_ODD],
            [25.49, 25.495, 2, Math::PS_ROUND_HALF_ODD],

            // floor
            [25, 25.32, 0, Math::PS_ROUND_HALF_DOWN],
            [25.3, 25.32, 1, Math::PS_ROUND_HALF_DOWN],
            [25.32, 25.32, 2, Math::PS_ROUND_HALF_DOWN],

            // up
            [26, 25.32, 0, Math::PS_ROUND_UP],
            [25.4, 25.32, 1, Math::PS_ROUND_UP],
            [25.32, 25.32, 2, Math::PS_ROUND_UP],
            [25.33, 25.325, 2, Math::PS_ROUND_UP],
        ];
    }

    public function providerMathRound(): array
    {
        return [
            // 0 precision
            [25, 25.32, 0, Math::PS_ROUND_UP],
            [26, 25.52, 0, Math::PS_ROUND_UP],
            [25, 25.32, 0, Math::PS_ROUND_HALF_DOWN],
            [25, 25.50, 0, Math::PS_ROUND_HALF_DOWN],
            [25, 25.32, 0, Math::PS_ROUND_HALF_EVEN],
            [26, 25.50, 0, Math::PS_ROUND_HALF_EVEN],
            [25, 25.32, 0, Math::PS_ROUND_HALF_ODD],
            [25, 25.50, 0, Math::PS_ROUND_HALF_ODD],
            [26, 25.51, 0, Math::PS_ROUND_HALF_ODD],
            [25, 25.49, 0, Math::PS_ROUND_HALF_ODD],
            // 2 precision
            [25.32, 25.321, 2, Math::PS_ROUND_UP],
            [25.53, 25.525, 2, Math::PS_ROUND_UP],
            [25.32, 25.325, 2, Math::PS_ROUND_HALF_DOWN],
            [25.5, 25.505, 2, Math::PS_ROUND_HALF_DOWN],
            [25.32, 25.325, 2, Math::PS_ROUND_HALF_EVEN],
            [25.5, 25.505, 2, Math::PS_ROUND_HALF_EVEN],
            [25.33, 25.325, 2, Math::PS_ROUND_HALF_ODD],
            [25.51, 25.505, 2, Math::PS_ROUND_HALF_ODD],
            [25.51, 25.515, 2, Math::PS_ROUND_HALF_ODD],
            [25.49, 25.495, 2, Math::PS_ROUND_HALF_ODD],
        ];
    }

    /**
     * @dataProvider providerMathRound
     */
    public function testMathRound(float $expectedResult, float $value, int $precision, int $mode): void
    {
        $this->assertSame($expectedResult, Math::math_round($value, $precision, $mode));
    }

    public function providerFloorF(): array
    {
        return [
            [25, 25.32, 0],
            [25.3, 25.32, 1],
            [25.32, 25.32, 2],
        ];
    }

    /**
     * @dataProvider providerFloorF
     */
    public function testFloorf(float $expectedResult, float $value, int $precision): void
    {
        $this->assertSame($expectedResult, Math::floorf($value, $precision));
    }

    public function providerCeilF(): array
    {
        return [
            [26, 25.32, 0],
            [25.4, 25.32, 1],
            [25.32, 25.32, 2],
            [25.33, 25.325, 2],
        ];
    }

    /**
     * @dataProvider providerCeilF
     */
    public function testCeilf(float $expectedResult, float $value, int $precision): void
    {
        $this->assertSame($expectedResult, Math::ceilf($value, $precision));
    }
}
