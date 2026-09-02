<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class MinimumAmount extends Money
{
    public function __construct(
        DecimalNumber $amount,
        CurrencyId $currencyId,
        bool $taxIncluded,
        private readonly bool $shippingIncluded,
    ) {
        parent::__construct($amount, $currencyId, $taxIncluded);
    }

    public function isShippingIncluded(): bool
    {
        return $this->shippingIncluded;
    }
}
