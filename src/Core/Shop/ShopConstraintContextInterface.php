<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Shop;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

interface ShopConstraintContextInterface
{
    /**
     * Returns the shop constraint for the current context.
     *
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint;
}
