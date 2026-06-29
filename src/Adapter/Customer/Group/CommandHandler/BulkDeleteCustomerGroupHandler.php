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
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\BulkDeleteCustomerGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\BulkDeleteCustomerGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkDeleteCustomerGroupHandler extends AbstractBulkCommandHandler implements BulkDeleteCustomerGroupHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
        private readonly CartRuleDisablerService $cartRuleDisablerService,
    ) {
    }

    public function handle(BulkDeleteCustomerGroupCommand $command): void
    {
        $this->handleBulkAction(
            $command->getCustomerGroupIds(),
            CannotDeleteGroupException::class,
            $command
        );
    }

    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->cartRuleDisablerService->disableCartRulesThatHadOnlyGroup($id->getValue());
        $this->customerGroupRepository->delete($id);
    }

    protected function supports($id): bool
    {
        return $id instanceof CustomerGroupId;
    }

    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDeleteCustomerGroupException(
            $caughtExceptions,
            'Failed to delete all selected customer groups'
        );
    }
}
