<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

/**
 * Deletes stores on bulk action
 */
class BulkDeleteStoreCommand
{
    /**
     * @var array<int, StoreId>
     */
    private $storeIds;

    /**
     * @param array<int, int> $storeIds
     */
    public function __construct(array $storeIds)
    {
        $this->setStoreIds($storeIds);
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
        $this->storeIds = [];
        foreach ($storeIds as $storeId) {
            $this->storeIds[] = new StoreId((int) $storeId);
        }
    }
}
