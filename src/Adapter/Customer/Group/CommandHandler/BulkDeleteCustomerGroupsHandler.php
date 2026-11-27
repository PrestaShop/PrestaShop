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
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\BulkDeleteCustomerGroupsCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\BulkDeleteCustomerGroupsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\BulkCustomerGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkDeleteCustomerGroupsHandler extends AbstractBulkCommandHandler implements BulkDeleteCustomerGroupsHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
        private readonly CartRuleDisablerService $cartRuleDisablerService,
        private readonly CustomerGroupValidator $customerGroupValidator,
    ) {
    }

    /**
     * @throws BulkCustomerGroupException
     */
    public function handle(BulkDeleteCustomerGroupsCommand $command): void
    {
        $this->handleBulkAction($command->getCustomerGroupIds(), GroupException::class);
    }

    /**
     * @param GroupId $id
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->customerGroupRepository->assertGroupExists($id);
        $this->customerGroupValidator->assertIsNotDefaultGroup($id);
        $this->cartRuleDisablerService->disableCartRulesThatHadOnlyGroup($id->getValue());
        $this->customerGroupRepository->delete($id);
    }

    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkCustomerGroupException(
            $caughtExceptions,
            'Errors occurred during customer group bulk delete action',
            BulkCustomerGroupException::FAILED_BULK_DELETE
        );
    }

    protected function supports($id): bool
    {
        return $id instanceof GroupId;
    }
}
