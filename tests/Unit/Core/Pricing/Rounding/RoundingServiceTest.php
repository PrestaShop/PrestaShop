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
    public function testRoundHalfUp(): void
    {
        $service = new RoundingService(0); // PS_PRICE_ROUND_MODE = 0 → ROUND_HALF_UP
        $result = $service->round(new DecimalNumber('29.5'));

        $this->assertTrue($result->equals(new DecimalNumber('30')));
    }

    public function testRoundHalfDown(): void
    {
        $service = new RoundingService(1); // PS_PRICE_ROUND_MODE = 1 → ROUND_HALF_DOWN
        $result = $service->round(new DecimalNumber('29.5'));

        $this->assertTrue($result->equals(new DecimalNumber('29')));
    }

    public function testRoundHalfEven(): void
    {
        $service = new RoundingService(2); // PS_PRICE_ROUND_MODE = 2 → ROUND_HALF_EVEN
        // 29.5 → rounds to 30 (nearest even)
        $result = $service->round(new DecimalNumber('29.5'));
        $this->assertTrue($result->equals(new DecimalNumber('30')));

        // 30.5 → rounds to 30 (nearest even)
        $result2 = $service->round(new DecimalNumber('30.5'));
        $this->assertTrue($result2->equals(new DecimalNumber('30')));
    }

    public function testRoundCeil(): void
    {
        $service = new RoundingService(3); // PS_PRICE_ROUND_MODE = 3 → ROUND_CEIL
        $result = $service->round(new DecimalNumber('29.01'));

        $this->assertTrue($result->equals(new DecimalNumber('30')));
    }

    public function testRoundFloor(): void
    {
        $service = new RoundingService(4); // PS_PRICE_ROUND_MODE = 4 → ROUND_FLOOR
        $result = $service->round(new DecimalNumber('29.99'));

        $this->assertTrue($result->equals(new DecimalNumber('29')));
    }

    public function testRoundTruncate(): void
    {
        $service = new RoundingService(5); // PS_PRICE_ROUND_MODE = 5 → ROUND_TRUNCATE
        $result = $service->round(new DecimalNumber('29.99'));

        $this->assertTrue($result->equals(new DecimalNumber('29')));
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
