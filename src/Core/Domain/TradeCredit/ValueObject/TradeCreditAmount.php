<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditConstraintException;

/**
 * Non-negative trade credit amount.
 */
class TradeCreditAmount
{
    private DecimalNumber $value;

    /**
     * @throws TradeCreditConstraintException
     */
    public function __construct(string $value)
    {
        $amount = new DecimalNumber($value);

        if ($amount->isNegative()) {
            throw new TradeCreditConstraintException(sprintf('Trade credit amount cannot be negative, "%s" given.', $value), TradeCreditConstraintException::INVALID_AMOUNT);
        }

        $this->value = $amount;
    }

    public function getValue(): DecimalNumber
    {
        return $this->value;
    }
}
