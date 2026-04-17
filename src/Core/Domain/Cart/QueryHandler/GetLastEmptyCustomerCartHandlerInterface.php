<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetLastEmptyCustomerCart;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Interface for handling GetLastEmptyCustomerCart query
 */
interface GetLastEmptyCustomerCartHandlerInterface
{
    /**
     * @param GetLastEmptyCustomerCart $query
     *
     * @return CartId
     */
    public function handle(GetLastEmptyCustomerCart $query): CartId;
}
