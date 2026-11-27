<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleDisablerService;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Validate\CustomerGroupValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\DeleteCustomerGroupHandlerInterface;

#[AsCommandHandler]
class DeleteCustomerGroupHandler implements DeleteCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
        private readonly CartRuleDisablerService $cartRuleDisablerService,
        private readonly CustomerGroupValidator $customerGroupValidator,
    ) {
    }

    public function handle(DeleteCustomerGroupCommand $command): void
    {
        $this->customerGroupRepository->assertGroupExists($command->getCustomerGroupId());
        $this->customerGroupValidator->assertIsNotDefaultGroup($command->getCustomerGroupId());

        // Disable affected cart rules before removing the group rows from cart_rule_group,
        // so the query that finds single-group rules can still run.
        $this->cartRuleDisablerService->disableCartRulesThatHadOnlyGroup(
            $command->getCustomerGroupId()->getValue()
        );
        $this->customerGroupRepository->delete($command->getCustomerGroupId());
    }
}
