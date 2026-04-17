<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\QueryResult;

/**
 * Store information for editing
 */
class StoreForEditing
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var bool
     */
    private $active;

    /**
     * @param int $storeId
     * @param bool $isActive
     */
    public function __construct(
        int $storeId,
        bool $isActive
    ) {
        $this->storeId = $storeId;
        $this->active = $isActive;
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
