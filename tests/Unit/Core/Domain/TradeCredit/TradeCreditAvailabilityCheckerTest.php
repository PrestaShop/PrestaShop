<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\TradeCredit;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailability;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailabilityChecker;

class TradeCreditAvailabilityCheckerTest extends TestCase
{
    private TradeCreditAvailabilityChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new TradeCreditAvailabilityChecker();
    }

    public function testExactAmountIsSufficient(): void
    {
        $availability = $this->check('50000', false, '50000', '50000.00');

        $this->assertTrue($availability->isSufficient());
        $this->assertNull($availability->getReason());
    }

    public function testOneCentOverIsInsufficient(): void
    {
        $availability = $this->check('50000', false, '50000', '50000.01');

        $this->assertFalse($availability->isSufficient());
        $this->assertSame(TradeCreditAvailability::REASON_INSUFFICIENT, $availability->getReason());
    }

    public function testGuestIsRejected(): void
    {
        $availability = $this->check('50000', true, '50000', '10.00');

        $this->assertFalse($availability->isSufficient());
        $this->assertSame(TradeCreditAvailability::REASON_GUEST, $availability->getReason());
    }

    public function testNotGrantedUsesDefault(): void
    {
        $availability = $this->check(null, false, '50000', '49999.99');

        $this->assertTrue($availability->isSufficient());
        $this->assertTrue($availability->getAvailable()->equals(new DecimalNumber('50000')));
    }

    public function testAdminBalanceTakesPrecedenceOverDefault(): void
    {
        $availability = $this->check('100', false, '50000', '150');

        $this->assertSame(TradeCreditAvailability::REASON_INSUFFICIENT, $availability->getReason());
        $this->assertTrue($availability->getAvailable()->equals(new DecimalNumber('100')));
    }

    public function testZeroBalanceRejectsAnyOrder(): void
    {
        $availability = $this->check('0', false, '50000', '0.01');

        $this->assertSame(TradeCreditAvailability::REASON_INSUFFICIENT, $availability->getReason());
    }

    public function testRequiredAmountIsRoundedBeforeComparison(): void
    {
        $availability = $this->check('100', false, '50000', '100.004');

        $this->assertTrue($availability->isSufficient());
        $this->assertTrue($availability->getRequired()->equals(new DecimalNumber('100')));
    }

    private function check(?string $balance, bool $isGuest, string $default, string $required): TradeCreditAvailability
    {
        return $this->checker->check(
            null === $balance ? null : new DecimalNumber($balance),
            $isGuest,
            new DecimalNumber($default),
            new DecimalNumber($required)
        );
    }
}
