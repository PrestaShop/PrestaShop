<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Command;

use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

/**
 * Toggles store status
 */
class ToggleStoreStatusCommand
{
    /**
     * @var StoreId
     */
    private $storeId;

    /**
     * @param int $storeId
     */
    public function __construct(int $storeId)
    {
        $this->storeId = new StoreId($storeId);
    }

    /**
     * @return StoreId
     */
    public function getStoreId(): StoreId
    {
        return $this->storeId;
    }
}
