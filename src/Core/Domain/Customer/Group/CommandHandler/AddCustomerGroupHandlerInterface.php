<?php

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

interface AddCustomerGroupHandlerInterface
{
    /**
     * @param AddCustomerGroupCommand $command
     *
     * @return GroupId
     */
    public function handle(AddCustomerGroupCommand $command): GroupId;
}
