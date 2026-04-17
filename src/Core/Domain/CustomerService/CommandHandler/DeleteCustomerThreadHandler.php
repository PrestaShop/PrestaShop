<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\DeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Repository\CustomerThreadRepository;

/**
 * Handles command for customer thread deletion
 */
#[AsCommandHandler]
class DeleteCustomerThreadHandler implements DeleteCustomerThreadHandlerInterface
{
    /**
     * @var CustomerThreadRepository
     */
    private $customerThreadRepository;

    public function __construct(CustomerThreadRepository $customerThreadRepository)
    {
        $this->customerThreadRepository = $customerThreadRepository;
    }

    /**
     * @param DeleteCustomerThreadCommand $command
     *
     * @return void
     */
    public function handle(DeleteCustomerThreadCommand $command): void
    {
        $this->customerThreadRepository->delete($command->getCustomerThreadId());
    }
}
