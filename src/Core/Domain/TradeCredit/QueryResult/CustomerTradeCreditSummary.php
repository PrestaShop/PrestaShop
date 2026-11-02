<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Trade credit of a customer: balance (null when not granted yet), default amount and latest operations.
 */
class CustomerTradeCreditSummary
{
    /**
     * @param TradeCreditOperation[] $operations
     */
    public function __construct(
        private readonly ?DecimalNumber $balance,
        private readonly DecimalNumber $defaultAmount,
        private readonly string $currencyIso,
        private readonly array $operations,
        private readonly bool $currencyInstalled = true
    ) {
    }

    /**
     * False when the trade credit currency is not installed in the shop (amounts can't be formatted with it).
     */
    public function isCurrencyInstalled(): bool
    {
        return $this->currencyInstalled;
    }

    public function getBalance(): ?DecimalNumber
    {
        return $this->balance;
    }

    public function getDefaultAmount(): DecimalNumber
    {
        return $this->defaultAmount;
    }

    public function getCurrencyIso(): string
    {
        return $this->currencyIso;
    }

    public function isGranted(): bool
    {
        return null !== $this->balance;
    }

    /**
     * Balance when granted, otherwise the amount the customer will receive with the first order.
     */
    public function getAvailableAmount(): DecimalNumber
    {
        return $this->balance ?? $this->defaultAmount;
    }

    /**
     * @return TradeCreditOperation[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }
}
