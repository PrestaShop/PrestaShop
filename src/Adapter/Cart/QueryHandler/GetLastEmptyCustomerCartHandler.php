<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetLastEmptyCustomerCart;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetLastEmptyCustomerCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShopException;

/**
 * Gets last empty cart for customer using legacy object model
 */
#[AsQueryHandler]
final class GetLastEmptyCustomerCartHandler implements GetLastEmptyCustomerCartHandlerInterface
{
    /**
     * @param GetLastEmptyCustomerCart $query
     *
     * @return CartId
     *
     * @throws CartException
     * @throws CartNotFoundException
     * @throws CustomerNotFoundException
     * @throws CartConstraintException
     */
    public function handle(GetLastEmptyCustomerCart $query): CartId
    {
        $customerId = $query->getCustomerId()->getValue();

        try {
            $customer = new Customer($customerId);

            if ($customer->id !== $customerId) {
                throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId));
            }

            $cartId = $customer->getLastEmptyCart(false);

            if (false === $cartId) {
                throw new CartNotFoundException(sprintf('Empty cart not found for customer with id "%s"', $customerId));
            }
        } catch (PrestaShopException) {
            throw new CartException(sprintf('An error occurred while trying to find empty cart for customer with id "%s"', $customerId));
        }

        return new CartId($cartId);
    }
}
