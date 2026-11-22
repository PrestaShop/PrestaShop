<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Resolves the parent Multistore configuration scope for email settings.
 */
final class EmailConfigurationInheritance
{
    public static function getParentShopConstraint(ShopContext $shopContext): ?ShopConstraint
    {
        if ($shopContext->isSingleShopContext()) {
            return ShopConstraint::shopGroup($shopContext->getShopGroupId());
        }

        if (!$shopContext->isAllShopContext()) {
            return ShopConstraint::allShops();
        }

        return null;
    }
}
