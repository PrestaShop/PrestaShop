<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;

/**
 * Defines contract to handle @see GetProductFeatureValues
 */
interface GetProductFeatureValuesHandlerInterface
{
    /**
     * @param GetProductFeatureValues $query
     *
     * @return ProductFeatureValue[]
     */
    public function handle(GetProductFeatureValues $query): array;
}
