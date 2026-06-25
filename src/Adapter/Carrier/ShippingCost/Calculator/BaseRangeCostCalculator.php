<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class BaseRangeCostCalculator implements ShippingCostCalculatorInterface
{
    public function __construct(
        private readonly CarrierDataProviderInterface $carrierDataProvider,
    ) {
    }

    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->isFreeShipping()) {
            return;
        }

        $carrierData = $context->getCarrierData();
        $zoneId = $context->getResolvedZoneId();

        if ($carrierData === null || $zoneId === null) {
            $context->setAvailable(false);

            return;
        }

        $cost = $this->carrierDataProvider->getRangeCost(
            $carrierData,
            $context->getTotalWeight(),
            $context->getOrderTotal(),
            $zoneId,
            $context->getCurrencyId(),
        );

        if ($cost === null) {
            $context->setAvailable(false);

            return;
        }

        if ($cost->equals(new DecimalNumber('0'))) {
            $context->setFreeShipping(true);

            return;
        }

        $context->setCost($cost);
    }
}
