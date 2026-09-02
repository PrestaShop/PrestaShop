<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;

class ComputingPrecisionTest extends TestCase
{
    /**
     * @var ComputingPrecision
     */
    private $computingPrecision;

    /**
     * Setup tested dependency
     */
    public function setUp(): void
    {
        $this->computingPrecision = new ComputingPrecision();
    }

    /**
     * @dataProvider provider
     */
    public function testGetPrecision($input, $expected)
    {
        $result = $this->computingPrecision->getPrecision($input);
        $this->assertEquals($expected, $result);
    }

    public function provider()
    {
        return [
            [1, 1],
            [3, 3],
            [0, 0],
        ];
    }
}
