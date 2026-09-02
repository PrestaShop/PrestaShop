<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\CommandHandler;

use PrestaShop\PrestaShop\Adapter\QuickAccess\Repository\QuickAccessRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\DeleteQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\CommandHandler\DeleteQuickAccessHandlerInterface;

#[AsCommandHandler]
class DeleteQuickAccessHandler implements DeleteQuickAccessHandlerInterface
{
    public function __construct(private readonly QuickAccessRepository $repository)
    {
    }

    public function handle(DeleteQuickAccessCommand $command): void
    {
        $this->repository->delete($command->getQuickAccessId());
    }
}
