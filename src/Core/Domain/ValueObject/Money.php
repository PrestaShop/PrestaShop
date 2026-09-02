<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

class Money
{
    /**
     * @var DecimalNumber
     */
    private $amount;

    /**
     * @var CurrencyId
     */
    private $currencyId;

    /**
     * @var bool
     */
    private $taxIncluded;

    /**
     * @param DecimalNumber $amount
     * @param CurrencyId $currencyId
     * @param bool $taxIncluded
     *
     * @throws DomainConstraintException
     */
    public function __construct(
        DecimalNumber $amount,
        CurrencyId $currencyId,
        bool $taxIncluded
    ) {
        if (!$amount->isGreaterOrEqualThanZero()) {
            throw new DomainConstraintException(sprintf('Money amount cannot be lower than zero, %f given', (string) $amount), DomainConstraintException::INVALID_MONEY_AMOUNT);
        }

        $this->amount = $amount;
        $this->currencyId = $currencyId;
        $this->taxIncluded = $taxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    /**
     * @return CurrencyId
     */
    public function getCurrencyId(): CurrencyId
    {
        return $this->currencyId;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->taxIncluded;
    }
}
