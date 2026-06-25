<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Provider;

use Carrier;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierShippingData;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

class CarrierDataProvider implements CarrierDataProviderInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
    ) {
    }

    public function getCarrierShippingData(int $carrierId): ?CarrierShippingData
    {
        try {
            $carrier = $this->carrierRepository->get(new CarrierId($carrierId));
        } catch (CarrierNotFoundException) {
            return null;
        }

        $shippingMethod = $carrier->getShippingMethod();

        return new CarrierShippingData(
            $carrier->id,
            $shippingMethod,
            (int) $carrier->range_behavior,
            (bool) $carrier->shipping_handling,
            $shippingMethod === Carrier::SHIPPING_METHOD_FREE,
        );
    }

    public function getRangeCost(
        CarrierShippingData $carrierData,
        DecimalNumber $totalWeight,
        DecimalNumber $orderTotal,
        int $zoneId,
        int $currencyId,
    ): ?DecimalNumber {
        $carrier = $this->carrierRepository->get(new CarrierId($carrierData->getCarrierId()));

        $weightFloat = (float) (string) $totalWeight;
        $orderTotalFloat = (float) (string) $orderTotal;
        $isWeightMethod = $carrierData->getShippingMethod() === Carrier::SHIPPING_METHOD_WEIGHT;

        if ($carrierData->getRangeBehavior()) {
            $isInRange = $isWeightMethod
                ? Carrier::checkDeliveryPriceByWeight($carrier->id, $weightFloat, $zoneId)
                : Carrier::checkDeliveryPriceByPrice($carrier->id, $orderTotalFloat, $zoneId, $currencyId);

            if ($isInRange === false) {
                return null;
            }
        }

        $cost = $isWeightMethod
            ? $carrier->getDeliveryPriceByWeight($weightFloat, $zoneId)
            : $carrier->getDeliveryPriceByPrice($orderTotalFloat, $zoneId, $currencyId);

        if ($cost === false) {
            return null;
        }

        return new DecimalNumber((string) $cost);
    }
}
