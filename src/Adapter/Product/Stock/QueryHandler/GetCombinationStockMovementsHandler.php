<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryHandler\GetCombinationStockMovementsHandlerInterface;

/**
 * Handles @see GetCombinationStockMovements using the adapter repositories.
 */
#[AsQueryHandler]
class GetCombinationStockMovementsHandler extends AbstractGetStockMovementsHandler implements GetCombinationStockMovementsHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(GetCombinationStockMovements $query): array
    {
        return $this->getStockMovements(
            $this->stockAvailableRepository->getStockIdByCombination(
                $query->getCombinationId(),
                $query->getShopId()
            ),
            $query->getOffset(),
            $query->getLimit()
        );
    }
}
