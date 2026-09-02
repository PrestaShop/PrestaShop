<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

/**
 * Toggles store status on bulk action
 */
class BulkUpdateStoreStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var array<int, StoreId>
     */
    private $storeIds;

    /**
     * @param bool $expectedStatus
     * @param array<int, int> $storeIds
     */
    public function __construct(bool $expectedStatus, array $storeIds)
    {
        $this->setStoreIds($storeIds);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return array<int, StoreId>
     */
    public function getStoreIds(): array
    {
        return $this->storeIds;
    }

    /**
     * @param array<int, int> $storeIds
     */
    private function setStoreIds(array $storeIds): void
    {
        foreach ($storeIds as $storeId) {
            $this->storeIds[] = new StoreId((int) $storeId);
        }
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }
}
