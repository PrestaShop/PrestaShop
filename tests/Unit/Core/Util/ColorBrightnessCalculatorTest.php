<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;

class ColorBrightnessCalculatorTest extends TestCase
{
    /**
     * @var ColorBrightnessCalculator
     */
    private $colorBrightnessCalculator;

    public function setUp(): void
    {
        $this->colorBrightnessCalculator = new ColorBrightnessCalculator();
    }

    /**
     * @dataProvider getColors
     */
    public function testColorBrightness($hexColor, $isBright)
    {
        $this->assertEquals($isBright, $this->colorBrightnessCalculator->isBright($hexColor));
    }

    public function getColors()
    {
        yield ['#8B0000', false];
        yield ['#FFD700', true];
        yield ['#FFFFE0', true];
        yield ['#6B8E23', false];
        yield ['#E0FFFF', true];
        yield ['#E0FFFF', true];
        yield ['#00008B', false];
        yield ['#00F', false];
        yield ['#0F1', true];
        yield ['transparent', true];

        // A value that is not a six digit hexadecimal colour has no brightness to derive. Before
        // these were guarded, hexdec() raised a deprecation per invalid character and produced a
        // result from whichever letters happened to be hex digits, so 'red' came out bright while
        // 'orange' and 'LimeGreen' did not. See #30997.
        yield ['red', true];
        yield ['orange', true];
        yield ['DarkOrange', true];
        yield ['LimeGreen', true];
        yield ['rgb(255, 0, 0)', true];
        yield ['', true];
        yield ['#GGGGGG', true];
        yield ['#12345', true];
        yield ['#1234567', true];
    }

    /**
     * A non hexadecimal value must not raise a PHP diagnostic, which is what merchants actually
     * reported seeing on the order status pages.
     *
     * @dataProvider getNonHexColors
     */
    public function testNonHexColorRaisesNoDiagnostic($color)
    {
        $raised = [];
        set_error_handler(function ($level, $message) use (&$raised) {
            $raised[] = $message;

            return true;
        });

        try {
            $this->colorBrightnessCalculator->isBright($color);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $raised);
    }

    public static function getNonHexColors()
    {
        yield ['red'];
        yield ['orange'];
        yield ['DarkOrange'];
        yield ['LimeGreen'];
        yield ['rgb(255, 0, 0)'];
        yield [''];
        yield ['#GGGGGG'];
    }
}
