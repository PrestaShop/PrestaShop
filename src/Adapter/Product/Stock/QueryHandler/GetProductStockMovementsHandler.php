<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetProductStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryHandler\GetProductStockMovementsHandlerInterface;

/**
 * Handles @see GetProductStockMovements using the adapter repositories.
 */
#[AsQueryHandler]
class GetProductStockMovementsHandler extends AbstractGetStockMovementsHandler implements GetProductStockMovementsHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle(GetProductStockMovements $query): array
    {
        return $this->getStockMovements(
            $this->stockAvailableRepository->getStockIdByProduct(
                $query->getProductId(),
                $query->getShopId()
            ),
            $query->getOffset(),
            $query->getLimit()
        );
    }
}
