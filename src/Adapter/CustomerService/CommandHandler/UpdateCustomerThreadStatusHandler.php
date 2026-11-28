<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerThreadRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\UpdateCustomerThreadStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerServiceException;

/**
 * @internal
 */
#[AsCommandHandler]
final class UpdateCustomerThreadStatusHandler implements UpdateCustomerThreadStatusHandlerInterface
{
    public function __construct(
        private readonly CustomerThreadRepository $customerThreadRepository,
    ) {
    }

    public function handle(UpdateCustomerThreadStatusCommand $command): void
    {
        if (!$this->customerThreadRepository->updateStatus($command->getCustomerThreadId(), $command->getCustomerThreadStatus())) {
            throw new CustomerServiceException('Failed to update customer thread status.', CustomerServiceException::FAILED_TO_UPDATE_STATUS);
        }
    }
}
