<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartTotalForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartTotalForViewing;

/**
 * Interface for service that gets only a cart's displayed total.
 */
interface GetCartTotalForViewingHandlerInterface
{
    /**
     * @param GetCartTotalForViewing $query
     *
     * @return CartTotalForViewing
     */
    public function handle(GetCartTotalForViewing $query);
}
