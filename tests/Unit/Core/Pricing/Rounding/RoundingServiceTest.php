<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Rounding;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingService;

class RoundingServiceTest extends TestCase
{
    /**
     * The keys of the map are the PS_ROUND_* constants from config/defines.inc.php, which is what
     * the Shop Parameters form stores in PS_PRICE_ROUND_MODE. Each expectation below is the value
     * Tools::ps_round() produces for the same input and mode.
     *
     * @dataProvider getLegacyRoundModes
     */
    public function testModeMatchesTheLegacyConstant(int $legacyRoundMode, string $input, string $expected): void
    {
        $service = new RoundingService($legacyRoundMode);

        $this->assertTrue(
            $service->round(new DecimalNumber($input))->equals(new DecimalNumber($expected)),
            sprintf('mode %d applied to %s', $legacyRoundMode, $input)
        );
    }

    public static function getLegacyRoundModes(): iterable
    {
        // PS_ROUND_UP = 0, away from zero
        yield 'PS_ROUND_UP 29.01' => [PS_ROUND_UP, '29.01', '30'];
        yield 'PS_ROUND_UP 29.5' => [PS_ROUND_UP, '29.5', '30'];
        // PS_ROUND_DOWN = 1, towards zero
        yield 'PS_ROUND_DOWN 29.99' => [PS_ROUND_DOWN, '29.99', '29'];
        yield 'PS_ROUND_DOWN 29.5' => [PS_ROUND_DOWN, '29.5', '29'];
        // PS_ROUND_HALF_UP = 2
        yield 'PS_ROUND_HALF_UP 29.5' => [PS_ROUND_HALF_UP, '29.5', '30'];
        yield 'PS_ROUND_HALF_UP 29.4' => [PS_ROUND_HALF_UP, '29.4', '29'];
        // PS_ROUND_HALF_DOWN = 3
        yield 'PS_ROUND_HALF_DOWN 29.5' => [PS_ROUND_HALF_DOWN, '29.5', '29'];
        yield 'PS_ROUND_HALF_DOWN 29.6' => [PS_ROUND_HALF_DOWN, '29.6', '30'];
        // PS_ROUND_HALF_EVEN = 4
        yield 'PS_ROUND_HALF_EVEN 29.5' => [PS_ROUND_HALF_EVEN, '29.5', '30'];
        yield 'PS_ROUND_HALF_EVEN 30.5' => [PS_ROUND_HALF_EVEN, '30.5', '30'];
    }

    /**
     * PS_ROUND_HALF_ODD has no equivalent in the Decimal library, so it falls through to the
     * constructor default instead of throwing. See issue #30441 for the open question of what it
     * should do; this only pins that it does not blow up.
     */
    public function testHalfOddFallsBackInsteadOfFailing(): void
    {
        $service = new RoundingService(PS_ROUND_HALF_ODD);

        $this->assertTrue($service->round(new DecimalNumber('29.5'))->equals(new DecimalNumber('30')));
    }

    public function testRoundWithCustomPrecision(): void
    {
        $service = new RoundingService(0);
        $result = $service->round(new DecimalNumber('29.995'), 2);

        $this->assertTrue($result->equals(new DecimalNumber('30.00')));
    }

    public function testDefaultPrecisionIsZero(): void
    {
        $service = new RoundingService(0);
        $result = $service->round(new DecimalNumber('29.99'));

        // Default precision 0 → rounds to integer
        $this->assertTrue($result->equals(new DecimalNumber('30')));
    }

    public function testUnknownModeDefaultsToHalfUp(): void
    {
        $service = new RoundingService(99);
        $result = $service->round(new DecimalNumber('29.5'));

        $this->assertTrue($result->equals(new DecimalNumber('30')));
    }
}
