<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;

class CarrierDataCalculator implements ShippingCostCalculatorInterface
{
    public function __construct(
        private readonly CarrierDataProviderInterface $carrierDataProvider,
    ) {
    }

    public function compute(ShippingCostPriceInterface $context): void
    {
        $carrierData = $this->carrierDataProvider->getCarrierShippingData($context->getCarrierId());

        if ($carrierData === null) {
            $context->setAvailable(false);

            return;
        }

        $context->setCarrierData($carrierData);

        if ($carrierData->isFreeShippingMethod()) {
            $context->setFreeShipping(true);
        }
    }
}
