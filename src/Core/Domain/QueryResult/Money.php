<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

/**
 * This generic query result is used to represent an amount of money for query results.
 */
class Money
{
    public function __construct(
        private readonly DecimalNumber $amount,
        private readonly int $currencyId,
        private readonly bool $taxIncluded
    ) {
    }

    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function isTaxIncluded(): bool
    {
        return $this->taxIncluded;
    }
}
