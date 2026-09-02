<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationShopAssociationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;

/**
 * Handles @see GetCombinationForEditing query
 */
interface GetCombinationForEditingHandlerInterface
{
    /**
     * @param GetCombinationForEditing $query
     *
     * @return CombinationForEditing
     *
     * @throws CombinationNotFoundException
     * @throws CombinationShopAssociationNotFoundException
     */
    public function handle(GetCombinationForEditing $query): CombinationForEditing;
}
