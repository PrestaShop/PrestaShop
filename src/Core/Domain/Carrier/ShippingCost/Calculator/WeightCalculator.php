<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class WeightCalculator implements ShippingCostCalculatorInterface
{
    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->isFreeShipping()) {
            return;
        }

        $totalWeight = new DecimalNumber('0');
        foreach ($context->getPhysicalProducts() as $product) {
            $rawWeight = $product['weight_attribute'] ?? $product['weight'] ?? 0;
            $rawQuantity = $product['quantity'] ?? 0;
            $weight = new DecimalNumber(is_numeric($rawWeight) ? (string) $rawWeight : '0');
            $quantity = new DecimalNumber(is_numeric($rawQuantity) ? (string) $rawQuantity : '0');
            $totalWeight = $totalWeight->plus($weight->times($quantity));
        }

        $context->setTotalWeight($totalWeight);
    }
}
