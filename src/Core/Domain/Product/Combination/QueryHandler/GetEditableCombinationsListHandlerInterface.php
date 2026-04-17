<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationListForEditing;

/**
 * Defines contract to handle @see GetEditableCombinationsList query
 */
interface GetEditableCombinationsListHandlerInterface
{
    /**
     * @param GetEditableCombinationsList $query
     *
     * @return CombinationListForEditing
     */
    public function handle(GetEditableCombinationsList $query): CombinationListForEditing;
}
