<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerCarts;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\CartSummary;

/**
 * Interface for handling GetCustomerCarts query
 */
interface GetCustomerCartsHandlerInterface
{
    /**
     * @param GetCustomerCarts $query
     *
     * @return CartSummary[]
     */
    public function handle(GetCustomerCarts $query): array;
}
