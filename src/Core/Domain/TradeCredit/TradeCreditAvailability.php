<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Result of checking whether an amount can be charged to a customer trade credit.
 */
final class TradeCreditAvailability
{
    public const REASON_GUEST = 'guest';
    public const REASON_INSUFFICIENT = 'insufficient';
    public const REASON_CURRENCY_UNAVAILABLE = 'currency_unavailable';

    public function __construct(
        private readonly DecimalNumber $available,
        private readonly DecimalNumber $required,
        private readonly ?string $reason
    ) {
    }

    public function isSufficient(): bool
    {
        return null === $this->reason;
    }

    public function getAvailable(): DecimalNumber
    {
        return $this->available;
    }

    public function getRequired(): DecimalNumber
    {
        return $this->required;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}
