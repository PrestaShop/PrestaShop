<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Search\Query\GetIndexedProductsCount;
use PrestaShop\PrestaShop\Core\Domain\Search\QueryResult\IndexedProductsCount;

interface GetIndexedProductsCountHandlerInterface
{
    public function handle(GetIndexedProductsCount $query): IndexedProductsCount;
}
