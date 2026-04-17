<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;

/**
 * Defines contract for GetStockMovementsHistoryHandler
 */
interface GetCombinationStockMovementsHandlerInterface
{
    /**
     * @return StockMovement[]
     */
    public function handle(GetCombinationStockMovements $query): array;
}
