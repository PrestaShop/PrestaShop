<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Carrier Range Zone
 */
class CarrierRangeZone
{
    /** @var CarrierRangePrice[] */
    private array $ranges;

    public function __construct(
        private int $zoneId,

        /* @var array{
         *     range_from: float,
         *     range_to: float,
         *     range_price: string,
         * }[] $ranges,
         */
        array $ranges,
    ) {
        // Validate zone id to avoid overlapping ranges
        $this->assertZoneId($zoneId);
        $this->assertRanges($ranges);

        // Create CarrierRangePrice objects
        $this->ranges = [];
        foreach ($ranges as $range) {
            $this->ranges[] = new CarrierRangePrice(
                (string) $range['range_from'],
                (string) $range['range_to'],
                (string) $range['range_price']
            );
        }
    }

    public function getZoneId(): int
    {
        return $this->zoneId;
    }

    /**
     * @return CarrierRangePrice[]
     */
    public function getRanges(): array
    {
        return $this->ranges;
    }

    /**
     * @param int $zoneId
     *
     * @throws CarrierConstraintException
     */
    private function assertZoneId(int $zoneId)
    {
        if (0 >= $zoneId) {
            throw new CarrierConstraintException(
                sprintf('Invalid zone id %d supplied. Zone id must be a positive integer.', $zoneId),
                CarrierConstraintException::INVALID_ZONE_ID
            );
        }
    }

    /**
     * @param array $ranges
     *
     * @throws CarrierConstraintException
     */
    private function assertRanges(array $ranges)
    {
        // Initialize min value
        $min = 0;

        // First, we need to sort by range from
        usort($ranges, function ($a, $b) {
            return $a['range_from'] <=> $b['range_from'];
        });

        // Then, we can check if ranges are overlapping or not
        foreach ($ranges as $range) {
            if ($range['range_from'] < 0 || $range['range_to'] < 0) {
                throw new CarrierConstraintException(
                    'Carrier range cannot be less than zero.',
                    CarrierConstraintException::INVALID_RANGE_NEGATIVE
                );
            }
            if ($min > $range['range_from']) {
                throw new CarrierConstraintException(
                    'Carrier ranges are overlapping',
                    CarrierConstraintException::INVALID_RANGES_OVERLAPPING
                );
            }
            $min = $range['range_to'];
        }
    }
}
