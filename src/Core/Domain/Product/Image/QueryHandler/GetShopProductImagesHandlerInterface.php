<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetShopProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopProductImagesCollection;

/**
 * Handles @see GetShopProductImages query
 */
interface GetShopProductImagesHandlerInterface
{
    public function handle(GetShopProductImages $query): ShopProductImagesCollection;
}
