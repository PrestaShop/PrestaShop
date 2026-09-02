<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult;

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
}
