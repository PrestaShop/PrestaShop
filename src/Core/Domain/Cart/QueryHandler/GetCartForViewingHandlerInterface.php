<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartView;

/**
 * Interface for service that gets cart for viewing
 */
interface GetCartForViewingHandlerInterface
{
    /**
     * @param GetCartForViewing $query
     *
     * @return CartView
     */
    public function handle(GetCartForViewing $query);
}
