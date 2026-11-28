<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerThreadRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler\DeleteCustomerThreadHandlerInterface;

#[AsCommandHandler]
final class DeleteCustomerThreadHandler implements DeleteCustomerThreadHandlerInterface
{
    public function __construct(
        private readonly CustomerThreadRepository $customerThreadRepository,
    ) {
    }

    public function handle(DeleteCustomerThreadCommand $command): void
    {
        $this->customerThreadRepository->delete($command->getCustomerThreadId());
    }
}
