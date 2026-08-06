<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class ShippingCostCalculator implements ShippingCostCalculatorInterface
{
    public function __construct(
        private readonly iterable $calculators,
    ) {
    }

    public function compute(ShippingCostPriceInterface $context): void
    {
        foreach ($this->calculators as $calculator) {
            if (!$context->isAvailable()) {
                return;
            }

            $calculator->compute($context);
        }
    }
}
