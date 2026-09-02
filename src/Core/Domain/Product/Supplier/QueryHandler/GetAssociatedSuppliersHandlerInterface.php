<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;

/**
 * Defines contract to handle @see GetAssociatedSuppliers
 */
interface GetAssociatedSuppliersHandlerInterface
{
    /**
     * @param GetAssociatedSuppliers $query
     *
     * @return AssociatedSuppliers
     */
    public function handle(GetAssociatedSuppliers $query): AssociatedSuppliers;
}
