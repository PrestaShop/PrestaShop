<?php

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\QueryHandler;


use Group as CustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler\GetCustomerGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;

class GetCustomerGroupForEditingHandler implements GetCustomerGroupForEditingHandlerInterface
{
    /**
     * @param GetCustomerGroupForEditing $query
     *
     * @throws GroupNotFoundException
     *
     * @return EditableCustomerGroup
     */
    public function handle(GetCustomerGroupForEditing $query): EditableCustomerGroup
    {
        $customerGroupId = $query->getCustomerGroupId();
        $customerGroup = new CustomerGroup($customerGroupId->getValue());

        if ($customerGroup->id !== $customerGroupId->getValue()) {
            throw new GroupNotFoundException(
                sprintf('Customer Group with id "%d" was not found', $customerGroupId->getValue())
            );
        }

        return new EditableCustomerGroup(
            $customerGroupId,
            $customerGroup->name,
            (float) $customerGroup->reduction,
            $customerGroup->price_display_method,
            $customerGroup->show_prices
        );
    }
}
