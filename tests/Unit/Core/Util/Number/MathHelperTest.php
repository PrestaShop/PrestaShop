<?php

namespace Tests\Unit\Core\Util\Number;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Util\Number\Math;
use PrestaShop\PrestaShop\Core\Util\Number\MathHelper;

class MathHelperTest extends TestCase
{
    /**
     * @dataProvider getRoundProvider
     */
    public function testRound(float $expectedResult, float $value, int $precision, int $mode): void
    {
        $mathHelper = new MathHelper(
            $configurationMock = $this->createMock(ConfigurationInterface::class)
        );

        $configurationMock->expects($this->once())
            ->method('get')
            ->with('PS_PRICE_ROUND_MODE')
            ->willReturn($mode)
        ;

        $this->assertSame($expectedResult, $mathHelper->round($value, $precision));
    }

    /**
     * @dataProvider getRoundProvider
     */
    public function testRoundWithModeAsThirdArgument(float $expectedResult, float $value, int $precision, int $mode): void
    {
        $mathHelper = new MathHelper(
            $configurationMock = $this->createMock(ConfigurationInterface::class)
        );

        // we should never call the configuration if the mode is set
        $configurationMock->expects($this->never())
            ->method('get')
        ;

        $this->assertSame($expectedResult, $mathHelper->round($value, $precision, $mode));
    }

    public function getRoundProvider(): array
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
}
