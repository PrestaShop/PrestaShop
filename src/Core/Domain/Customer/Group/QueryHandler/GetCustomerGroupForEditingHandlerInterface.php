<?php

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
