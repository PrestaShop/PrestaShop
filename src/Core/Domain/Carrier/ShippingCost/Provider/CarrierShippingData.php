<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider;

/**
 * Value object holding the carrier configuration data needed for shipping cost calculation.
 */
final class CarrierShippingData
{
    public function __construct(
        private readonly int $carrierId,
        private readonly int $shippingMethod,
        private readonly int $rangeBehavior,
        private readonly bool $hasShippingHandling,
        private readonly bool $isFreeShippingMethod,
    ) {
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getShippingMethod(): int
    {
        return $this->shippingMethod;
    }

    public function getRangeBehavior(): int
    {
        return $this->rangeBehavior;
    }

    public function hasShippingHandling(): bool
    {
        return $this->hasShippingHandling;
    }

    public function isFreeShippingMethod(): bool
    {
        return $this->isFreeShippingMethod;
    }
}
