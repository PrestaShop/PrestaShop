<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\BulkDeleteCustomerThreadCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Repository\CustomerThreadRepository;

/**
 * Handles command for customer thread bulk deletion
 */
#[AsCommandHandler]
class BulkDeleteCustomerThreadHandler implements BulkDeleteCustomerThreadHandlerInterface
{
    /**
     * @var CustomerThreadRepository
     */
    private $customerThreadRepository;

    public function __construct(CustomerThreadRepository $customerThreadRepository)
    {
        $this->customerThreadRepository = $customerThreadRepository;
    }

    public function handle(BulkDeleteCustomerThreadCommand $command): void
    {
        foreach ($command->getCustomerThreadIds() as $customerThreadId) {
            $this->customerThreadRepository->delete($customerThreadId);
        }
    }
}
