<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;

/**
 * Updates zone for given states.
 */
class BulkUpdateStateZoneCommand
{
    /**
     * @var int[]
     */
    private $stateIds;

    /**
     * @var int
     */
    private $newZoneId;

    /**
     * @param int[] $stateIds
     * @param int $newZoneId
     */
    public function __construct(array $stateIds, int $newZoneId)
    {
        if ($newZoneId <= 0) {
            throw new StateException(sprintf('Zone Id must be integer greater than 0, but %s given.', var_export($newZoneId, true)));
        }

        $this->newZoneId = $newZoneId;
        $this->setStateIds($stateIds);
    }

    /**
     * @return int[]
     */
    public function getStateIds(): array
    {
        return $this->stateIds;
    }

    /**
     * @return int
     */
    public function getNewZoneId(): int
    {
        return $this->newZoneId;
    }

    /**
     * @param int[] $stateIds
     */
    private function setStateIds(array $stateIds): void
    {
        if (empty($stateIds)) {
            throw new StateException('You must select at least one state.');
        }

        foreach ($stateIds as $stateId) {
            if (!is_int($stateId) || $stateId <= 0) {
                throw new StateException(sprintf('Invalid state ID: %s', var_export($stateId, true)));
            }
        }

        $this->stateIds = $stateIds;
    }
}
