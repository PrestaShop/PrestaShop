<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\FreeShippingCriteriaProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class FreeShippingCalculator implements ShippingCostCalculatorInterface
{
    public function __construct(
        private readonly FreeShippingCriteriaProviderInterface $criteriaProvider,
    ) {
    }

    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->isFreeShipping()) {
            return;
        }

        $thresholds = $this->criteriaProvider->getCriteria($context);

        if ($thresholds->hasFreePrice() && $context->getShipmentTotal()->isGreaterOrEqualThan($thresholds->getFreePrice())) {
            $context->setFreeShipping(true);

            return;
        }

        if ($thresholds->hasFreeWeight() && $context->getTotalWeight()->isGreaterOrEqualThan($thresholds->getFreeWeight())) {
            $context->setFreeShipping(true);
        }
    }
}
