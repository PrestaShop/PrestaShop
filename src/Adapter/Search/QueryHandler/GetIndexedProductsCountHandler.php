<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\QueryHandler;

use Db;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Search\Query\GetIndexedProductsCount;
use PrestaShop\PrestaShop\Core\Domain\Search\QueryHandler\GetIndexedProductsCountHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Search\QueryResult\IndexedProductsCount;
use Shop;

#[AsQueryHandler]
final class GetIndexedProductsCountHandler implements GetIndexedProductsCountHandlerInterface
{
    public function handle(GetIndexedProductsCount $query): IndexedProductsCount
    {
        $row = Db::getInstance()->getRow(
            'SELECT COUNT(*) as total, SUM(product_shop.indexed) as indexed'
            . ' FROM `' . _DB_PREFIX_ . 'product` p'
            . Shop::addSqlAssociation('product', 'p')
            . ' WHERE product_shop.`visibility` IN ("both", "search")'
            . ' AND product_shop.`active` = 1'
        );

        return new IndexedProductsCount(
            (int) ($row['indexed'] ?? 0),
            (int) ($row['total'] ?? 0),
        );
    }
}
