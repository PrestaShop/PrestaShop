<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Query\GetCombinationFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\QueryResult\CombinationFeatureValue;

/**
 * Defines contract to handle @see GetCombinationFeatureValues
 */
interface GetCombinationFeatureValuesHandlerInterface
{
    /**
     * @param GetCombinationFeatureValues $query
     *
     * @return CombinationFeatureValue[]
     */
    public function handle(GetCombinationFeatureValues $query): array;
}
