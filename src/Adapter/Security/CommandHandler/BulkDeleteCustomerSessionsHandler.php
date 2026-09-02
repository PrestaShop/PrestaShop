<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Session\Repository\CustomerSessionRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteCustomerSessionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler\BulkDeleteCustomerSessionsHandlerInterface;

/**
 * Handles command that deletes customers sessions in bulk action.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDeleteCustomerSessionsHandler implements BulkDeleteCustomerSessionsHandlerInterface
{
    /**
     * @var CustomerSessionRepository
     */
    private $repository;

    /**
     * @param CustomerSessionRepository $repository
     */
    public function __construct(CustomerSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteCustomerSessionsCommand $command): void
    {
        $this->repository->bulkDelete($command->getCustomerSessionIds());
    }
}
