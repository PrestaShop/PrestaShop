<?php

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\QueryHandler;

use Group as CustomerGroup;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryHandler\GetCustomerGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;

class GetCustomerGroupForEditingHandler implements GetCustomerGroupForEditingHandlerInterface
{
    /**
     * @var GroupRepository
     */
    private $customerGroupRepository;

    public function __construct(GroupRepository $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
    }

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
        $this->customerGroupRepository->assertGroupExists($customerGroupId);

        $customerGroup = new CustomerGroup($customerGroupId->getValue());

        return new EditableCustomerGroup(
            $customerGroupId->getValue(),
            $customerGroup->name,
            new DecimalNumber($customerGroup->reduction),
            (bool) $customerGroup->price_display_method,
            $customerGroup->show_prices
        );
    }
}
