<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;

interface GetCustomerGroupForEditingHandlerInterface
{
    /**
     * @param GetCustomerGroupForEditing $customerForEditingQuery
     *
     * @return EditableCustomerGroup
     */
    public function handle(GetCustomerGroupForEditing $customerForEditingQuery): EditableCustomerGroup;
}
