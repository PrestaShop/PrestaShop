<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;

/**
 * Handles @see GetProductImages query
 */
interface GetProductImagesHandlerInterface
{
    /**
     * @param GetProductImages $query
     *
     * @return ProductImage[]
     */
    public function handle(GetProductImages $query): array;
}
