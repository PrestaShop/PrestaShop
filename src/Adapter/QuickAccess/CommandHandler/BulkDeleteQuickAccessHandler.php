<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\CommandHandler;

use PrestaShop\PrestaShop\Adapter\QuickAccess\Repository\QuickAccessRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\BulkDeleteQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\CommandHandler\BulkDeleteQuickAccessHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\BulkQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

#[AsCommandHandler]
class BulkDeleteQuickAccessHandler extends AbstractBulkCommandHandler implements BulkDeleteQuickAccessHandlerInterface
{
    public function __construct(private readonly QuickAccessRepository $repository)
    {
    }

    public function handle(BulkDeleteQuickAccessCommand $command): void
    {
        $this->handleBulkAction($command->getQuickAccessIds(), QuickAccessException::class);
    }

    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->repository->delete($id);
    }

    protected function supports($id): bool
    {
        return $id instanceof QuickAccessId;
    }

    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkQuickAccessException(
            $caughtExceptions,
            'Errors occurred during Quick Access bulk delete action'
        );
    }
}
