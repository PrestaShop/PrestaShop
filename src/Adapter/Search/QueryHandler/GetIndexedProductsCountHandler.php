<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\QueryHandler;

use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Adapter\Search\Repository\IndexedProductsRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Search\Query\GetIndexedProductsCount;
use PrestaShop\PrestaShop\Core\Domain\Search\QueryHandler\GetIndexedProductsCountHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Search\QueryResult\IndexedProductsCount;
use Shop;

#[AsQueryHandler]
final class GetIndexedProductsCountHandler implements GetIndexedProductsCountHandlerInterface
{
    public function __construct(
        private readonly IndexedProductsRepository $indexedProductsRepository,
    ) {
    }

    /**
     * @throws DBALException
     */
    public function handle(GetIndexedProductsCount $query): IndexedProductsCount
    {
        $counts = $this->indexedProductsRepository->getIndexedProductsCount(Shop::getContextListShopID());

        return new IndexedProductsCount(
            $counts['indexed'],
            $counts['total'],
        );
    }
}
