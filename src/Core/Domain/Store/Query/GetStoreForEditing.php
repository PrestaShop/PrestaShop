<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Query;

use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;

class GetStoreForEditing
{
    /** @var StoreId */
    private $storeId;

    /**
     * @param int $storeId
     *
     * @throws StoreException
     */
    public function __construct($storeId)
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
