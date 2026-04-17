<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\DeleteCustomerGroupHandlerInterface;

#[AsCommandHandler]
class DeleteCustomerGroupHandler implements DeleteCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
    ) {
    }

    public function handle(DeleteCustomerGroupCommand $command): void
    {
        $this->customerGroupRepository->delete($command->getCustomerGroupId());
    }
}
