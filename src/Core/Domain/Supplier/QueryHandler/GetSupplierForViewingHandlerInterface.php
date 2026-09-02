<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\ViewableSupplier;

/**
 * Interface for service that handles query to get supplier for viewing
 */
interface GetSupplierForViewingHandlerInterface
{
    /**
     * @param GetSupplierForViewing $query
     *
     * @return ViewableSupplier
     */
    public function handle(GetSupplierForViewing $query);
}
