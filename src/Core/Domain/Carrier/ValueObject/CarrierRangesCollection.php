<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

class CarrierRangesCollection
{
    /** @var CarrierRangeZone[] */
    private array $zones;

    public function __construct(
        /* @var array{
         *     id_zone: int,
         *     range_from: float,
         *     range_to: float,
         *     range_price: string,
         * }[] $carrierRanges,
         */
        array $carrierRanges,
    ) {
        // First we need to sort carrier ranges by range_from then by range_to.
        usort($carrierRanges, function ($a, $b) {
            if ($a['range_from'] === $b['range_from']) {
                return $a['range_to'] <=> $b['range_to'];
            }

            return $a['range_from'] <=> $b['range_from'];
        });

        // Then, we need to group carrier ranges by zone and create CarrierRangePrice objects for each.
        $rangesByZones = [];
        foreach ($carrierRanges as $carrierRange) {
            $zoneId = (int) $carrierRange['id_zone'];

            if (!isset($rangesByZones[$zoneId])) {
                $rangesByZones[$zoneId] = [];
            }

            $rangesByZones[$zoneId][] = $carrierRange;
        }

        // Finally, we create CarrierRangeZone objects for each zone with its ranges.
        $this->zones = [];
        foreach ($rangesByZones as $zoneId => $ranges) {
            $this->zones[] = new CarrierRangeZone($zoneId, $ranges);
        }
    }

    /**
     * @return CarrierRangeZone[]
     */
    public function getZones(): array
    {
        return $this->zones;
    }
}
