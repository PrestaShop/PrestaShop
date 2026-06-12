<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleDisablerService;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\BulkDeleteCustomerGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\BulkDeleteCustomerGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupException;

#[AsCommandHandler]
final class BulkDeleteCustomerGroupHandler implements BulkDeleteCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
        private readonly CartRuleDisablerService $cartRuleDisablerService,
    ) {
    }

    public function handle(BulkDeleteCustomerGroupCommand $command): void
    {
        $errors = [];

        foreach ($command->getCustomerGroupIds() as $groupId) {
            try {
                $this->cartRuleDisablerService->disableCartRulesThatHadOnlyGroup($groupId->getValue());
                $this->customerGroupRepository->delete($groupId);
            } catch (GroupException) {
                $errors[] = $groupId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteCustomerGroupException(
                $errors,
                'Failed to delete all selected customer groups'
            );
        }
    }
}
