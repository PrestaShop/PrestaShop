<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\DefaultCustomerGroupsRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Validate\CustomerGroupValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\UpdateDefaultCustomerGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\UpdateDefaultCustomerGroupsHandlerInterface;

#[AsCommandHandler]
final class UpdateDefaultCustomerGroupsHandler implements UpdateDefaultCustomerGroupsHandlerInterface
{
    public function __construct(
        private readonly DefaultCustomerGroupsRepository $defaultCustomerGroupsRepository,
        private readonly CustomerGroupValidator $customerGroupValidator,
    ) {
    }

    public function handle(UpdateDefaultCustomerGroupsCommand $command): void
    {
        foreach ([$command->getVisitorsGroupId(), $command->getGuestsGroupId(), $command->getCustomersGroupId()] as $groupId) {
            $this->customerGroupValidator->assertCanBeDefaultGroup($groupId, $command->getShopConstraint());
        }

        $this->defaultCustomerGroupsRepository->update(
            $command->getVisitorsGroupId(),
            $command->getGuestsGroupId(),
            $command->getCustomersGroupId(),
            $command->getShopConstraint()
        );
    }
}
