<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
