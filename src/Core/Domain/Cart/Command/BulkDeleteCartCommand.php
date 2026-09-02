<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Deletes cart in bulk action
 */
class BulkDeleteCartCommand
{
    /**
     * @var CartId[]
     */
    private $cartIds;

    /**
     * @param int[] $cartIds
     *
     * @throws CartConstraintException
     */
    public function __construct(array $cartIds)
    {
        $this->setCartIds($cartIds);
    }

    /**
     * @return CartId[]
     */
    public function getCartIds(): array
    {
        return $this->cartIds;
    }

    /**
     * @param int[] $cartIds
     *
     * @throws CartConstraintException
     */
    private function setCartIds(array $cartIds): void
    {
        foreach ($cartIds as $cartId) {
            $this->cartIds[] = new CartId($cartId);
        }
    }
}
