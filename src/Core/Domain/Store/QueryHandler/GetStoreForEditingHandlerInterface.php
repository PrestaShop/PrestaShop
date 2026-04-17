<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Store\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;

interface GetStoreForEditingHandlerInterface
{
    /**
     * @param GetStoreForEditing $query
     *
     * @return StoreForEditing
     */
    public function handle(GetStoreForEditing $query): StoreForEditing;
}
