<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;

/**
 * Handles @see GetProductImage query
 */
interface GetProductImageHandlerInterface
{
    /**
     * @param GetProductImage $query
     *
     * @return ProductImage
     */
    public function handle(GetProductImage $query): ProductImage;
}
