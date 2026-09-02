<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductShopAssociationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;

/**
 * Defines contract for GetProductForEditingHandler
 */
interface GetProductForEditingHandlerInterface
{
    /**
     * @param GetProductForEditing $query
     *
     * @return ProductForEditing
     *
     * @throws ProductNotFoundException
     * @throws ProductShopAssociationNotFoundException
     */
    public function handle(GetProductForEditing $query): ProductForEditing;
}
