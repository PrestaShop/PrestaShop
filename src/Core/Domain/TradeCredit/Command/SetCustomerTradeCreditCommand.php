<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\Command;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\ValueObject\TradeCreditAmount;

/**
 * Sets the available trade credit of a customer (admin adjustment).
 *
 * The expected amount is the balance the admin saw when opening the form; if the balance changed
 * in the meantime (for example an order was placed) the command is rejected instead of overwriting it.
 */
class SetCustomerTradeCreditCommand
{
    private CustomerId $customerId;

    private TradeCreditAmount $amount;

    private DecimalNumber $expectedAmount;

    /**
     * @throws TradeCreditConstraintException
     */
    public function __construct(
        int $customerId,
        string $amount,
        string $expectedAmount,
        private readonly int $employeeId
    ) {
        $this->customerId = new CustomerId($customerId);
        $this->amount = new TradeCreditAmount($amount);
        $this->expectedAmount = new DecimalNumber($expectedAmount);
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    public function getAmount(): TradeCreditAmount
    {
        return $this->amount;
    }

    public function getExpectedAmount(): DecimalNumber
    {
        return $this->expectedAmount;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }
}
