<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Session\Repository\EmployeeSessionRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteEmployeeSessionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler\BulkDeleteEmployeeSessionsHandlerInterface;

/**
 * Handles command that deletes employees sessions in bulk action.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDeleteEmployeeSessionsHandler implements BulkDeleteEmployeeSessionsHandlerInterface
{
    /**
     * @var EmployeeSessionRepository
     */
    private $repository;

    /**
     * @param EmployeeSessionRepository $repository
     */
    public function __construct(EmployeeSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteEmployeeSessionsCommand $command): void
    {
        $this->repository->bulkDelete($command->getEmployeeSessionIds());
    }
}
