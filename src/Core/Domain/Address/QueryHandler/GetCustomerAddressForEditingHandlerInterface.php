<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetCustomerAddressForEditing;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\EditableCustomerAddress;

/**
 * Interface for services that handles query which gets customer address for editing
 */
interface GetCustomerAddressForEditingHandlerInterface
{
    /**
     * @param GetCustomerAddressForEditing $query
     *
     * @return EditableCustomerAddress
     */
    public function handle(GetCustomerAddressForEditing $query): EditableCustomerAddress;
}
