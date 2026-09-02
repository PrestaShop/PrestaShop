<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;

/**
 * Thrown when a shop association is checked but is nonexistent for a combination.
 */
class ProductShopAssociationNotFoundException extends ShopAssociationNotFound
{
}
