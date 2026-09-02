<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;

/**
 * Defines contract for GetSupplierForEditingHandler
 */
interface GetSupplierForEditingHandlerInterface
{
    /**
     * @param GetSupplierForEditing $query
     *
     * @return EditableSupplier
     */
    public function handle(GetSupplierForEditing $query);
}
