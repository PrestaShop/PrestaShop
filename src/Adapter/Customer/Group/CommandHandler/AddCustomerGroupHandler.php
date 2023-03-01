<?php

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\AddCustomerGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class AddCustomerGroupHandler implements AddCustomerGroupHandlerInterface
{
    /**
     * @var GroupRepository
     */
    private $customerGroupRepository;

    public function __construct(GroupRepository $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
    }

    public function handle(AddCustomerGroupCommand $command): GroupId
    {
        $customerGroup = $this->customerGroupRepository->create(
            $command->getLocalizedNames(),
            $command->getReduction(),
            $command->getPriceDisplayMethod(),
            $command->isShowPrice()
        );

        return new GroupId((int) $customerGroup->id);
    }
}
