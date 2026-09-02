<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Deletes states on bulk action
 */
class BulkDeleteStateCommand
{
    /**
     * @var array<int, StateId>
     */
    private $stateIds;

    /**
     * @param array<int, int> $stateIds
     */
    public function __construct(array $stateIds)
    {
        $this->setStateIds($stateIds);
    }

    /**
     * @return array<int, StateId>
     */
    public function getStateIds(): array
    {
        return $this->stateIds;
    }

    /**
     * @param array<int, int> $stateIds
     */
    private function setStateIds(array $stateIds): void
    {
        foreach ($stateIds as $stateId) {
            $this->stateIds[] = $stateId !== NoStateId::NO_STATE_ID_VALUE ? new StateId((int) $stateId) : new NoStateId();
        }
    }
}
