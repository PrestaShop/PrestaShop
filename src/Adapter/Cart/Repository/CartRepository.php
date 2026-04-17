<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\Repository;

use Cart;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class CartRepository extends AbstractObjectModelRepository
{
    /**
     * Retrieve Cart by CartId.
     *
     * @param CartId $cartId
     *
     * @return Cart
     *
     * @throws CartException
     * @throws CoreException
     */
    public function get(CartId $cartId): Cart
    {
        /** @var Cart $cart */
        $cart = $this->getObjectModel(
            $cartId->getValue(),
            Cart::class,
            CartNotFoundException::class
        );

        return $cart;
    }

    /**
     * Delete Cart by CartId.
     *
     * @param CartId $cartId
     *
     * @return void
     *
     * @throws CartException
     * @throws CoreException
     */
    public function delete(CartId $cartId): void
    {
        $this->deleteObjectModel($this->get($cartId), CannotDeleteCartException::class);
    }
}
