<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Exception\InvalidTaxRateException;
use PrestaShop\PrestaShop\Core\Pricing\PricingConstants;

/**
 * Represents a tax rate percentage (e.g. 20 for 20% VAT).
 */
class TaxRate
{
    public function __construct(
        protected readonly DecimalNumber $rate,
    ) {
        if ($rate->isNegative()) {
            throw new InvalidTaxRateException('Tax rate must be greater than or equal to 0');
        }
    }

    public static function zero(): self
    {
        return new self(new DecimalNumber('0'));
    }

    public function getRate(): DecimalNumber
    {
        return $this->rate;
    }

    /**
     * Returns 1 + rate/100 (e.g. 1.2 for a 20% tax rate).
     */
    public function getMultiplier(): DecimalNumber
    {
        return (new DecimalNumber('1'))->plus(
            $this->rate->dividedBy(new DecimalNumber('100'), PricingConstants::INTERMEDIATE_PRECISION)
        );
    }

    /**
     * Checks whether this tax rate is equal to another.
     */
    public function equals(self $other): bool
    {
        return $this->rate->equals($other->rate);
    }
}
