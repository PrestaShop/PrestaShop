<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class TotalShipmentPriceCalculator implements ShippingCostCalculatorInterface
{
    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->isFreeShipping()) {
            return;
        }

        $shipmentTotal = new DecimalNumber('0');
        foreach ($context->getPhysicalProducts() as $product) {
            $rawPrice = $product['price_wt'] ?? 0;
            $rawQuantity = $product['quantity'] ?? 0;
            $price = new DecimalNumber(is_numeric($rawPrice) ? (string) $rawPrice : '0');
            $quantity = new DecimalNumber(is_numeric($rawQuantity) ? (string) $rawQuantity : '0');
            $shipmentTotal = $shipmentTotal->plus($price->times($quantity));
        }

        $context->setShipmentTotal($shipmentTotal);
    }
}
