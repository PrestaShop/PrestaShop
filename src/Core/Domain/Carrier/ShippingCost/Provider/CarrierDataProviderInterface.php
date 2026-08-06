<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider;

use PrestaShop\Decimal\DecimalNumber;

interface CarrierDataProviderInterface extends ShippingCostProviderInterface
{
    public function getCarrierShippingData(int $carrierId): ?CarrierShippingData;

    public function getRangeCost(
        CarrierShippingData $carrierData,
        DecimalNumber $totalWeight,
        DecimalNumber $orderTotal,
        int $zoneId,
        int $currencyId,
    ): ?DecimalNumber;
}
