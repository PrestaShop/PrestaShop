<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Toggles states status on bulk action
 */
class BulkToggleStateStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var array<int, StateId>
     */
    private $stateIds;

    /**
     * @param bool $expectedStatus
     * @param array<int, int> $stateIds
     */
    public function __construct(bool $expectedStatus, array $stateIds)
    {
        $this->setStateIds($stateIds);
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
            $this->stateIds[] = new StateId((int) $stateId);
        }
    }
}
