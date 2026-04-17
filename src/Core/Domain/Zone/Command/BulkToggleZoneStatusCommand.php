<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\Command;

use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

/**
 * Toggles zones status on bulk action
 */
class BulkToggleZoneStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var array<int, ZoneId>
     */
    private $zoneIds;

    /**
     * @param bool $expectedStatus
     * @param array<int, int> $zoneIds
     */
    public function __construct(bool $expectedStatus, array $zoneIds)
    {
        $this->setZoneIds($zoneIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }

    /**
     * @return array<int, ZoneId>
     */
    public function getZoneIds(): array
    {
        return $this->zoneIds;
    }

    /**
     * @param array<int, int> $zoneIds
     */
    private function setZoneIds(array $zoneIds): void
    {
        foreach ($zoneIds as $zoneId) {
            $this->zoneIds[] = new ZoneId((int) $zoneId);
        }
    }
}
