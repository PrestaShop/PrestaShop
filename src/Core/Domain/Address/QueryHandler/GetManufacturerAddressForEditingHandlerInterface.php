<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetManufacturerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableManufacturerAddress;

/**
 * Interface for services that handles query which gets manufacturer address for editing
 */
interface GetManufacturerAddressForEditingHandlerInterface
{
    /**
     * @param GetManufacturerAddressForEditing $query
     *
     * @return EditableManufacturerAddress
     */
    public function handle(GetManufacturerAddressForEditing $query);
}
