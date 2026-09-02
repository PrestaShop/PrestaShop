<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop;

use PrestaShop\PrestaShop\Core\Data\ImmutableCollection;

/**
 * @template-extends ImmutableCollection<ShopImageAssociation>
 */
class ShopImageAssociationCollection extends ImmutableCollection
{
    public static function from(ShopImageAssociation ...$productImages)
    {
        return new static($productImages);
    }
}
