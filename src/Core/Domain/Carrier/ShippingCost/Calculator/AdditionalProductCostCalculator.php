<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class AdditionalProductCostCalculator implements ShippingCostCalculatorInterface
{
    public function compute(ShippingCostPriceInterface $context): void
    {
        if (!$context->isAvailable() || $context->isFreeShipping()) {
            return;
        }

        $additionalCost = new DecimalNumber('0');
        foreach ($context->getPhysicalProducts() as $product) {
            $productShippingCost = (string) ($product['additional_shipping_cost'] ?? 0);
            if ((float) $productShippingCost > 0.0) {
                $productCost = (new DecimalNumber($productShippingCost))
                    ->times(new DecimalNumber((string) $product['quantity']));
                $additionalCost = $additionalCost->plus($productCost);
            }
        }

        if (!$additionalCost->equals(new DecimalNumber('0'))) {
            $context->setCost($context->getCost()->plus($additionalCost));
        }
    }
}
