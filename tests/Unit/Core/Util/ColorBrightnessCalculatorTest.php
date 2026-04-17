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
    }
}
