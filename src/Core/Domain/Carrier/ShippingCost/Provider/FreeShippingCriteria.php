<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider;

use PrestaShop\Decimal\DecimalNumber;

final class FreeShippingCriteria
{
    private static DecimalNumber $zero;

    public function __construct(
        private readonly ?DecimalNumber $freePrice,
        private readonly ?DecimalNumber $freeWeight,
    ) {
        self::$zero ??= new DecimalNumber('0');
    }

    public function getFreePrice(): ?DecimalNumber
    {
        return $this->freePrice;
    }

    public function getFreeWeight(): ?DecimalNumber
    {
        return $this->freeWeight;
    }

    public function hasFreePrice(): bool
    {
        return $this->freePrice !== null && $this->freePrice->isGreaterThan(self::$zero);
    }

    public function hasFreeWeight(): bool
    {
        return $this->freeWeight !== null && $this->freeWeight->isGreaterThan(self::$zero);
    }
}
