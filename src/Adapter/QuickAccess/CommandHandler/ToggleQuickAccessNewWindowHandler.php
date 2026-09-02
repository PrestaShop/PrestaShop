<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\CommandHandler;

use PrestaShop\PrestaShop\Adapter\QuickAccess\Repository\QuickAccessRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\ToggleQuickAccessNewWindowCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\CommandHandler\ToggleQuickAccessNewWindowHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotUpdateQuickAccessException;
use PrestaShopException;

#[AsCommandHandler]
class ToggleQuickAccessNewWindowHandler implements ToggleQuickAccessNewWindowHandlerInterface
{
    public function __construct(private readonly QuickAccessRepository $repository)
    {
    }

    public function handle(ToggleQuickAccessNewWindowCommand $command): void
    {
        $quickAccess = $this->repository->get($command->getQuickAccessId());

        try {
            // Partial update — only the new_window column is written, preserving legacy setFieldsToUpdate semantics
            if (!$quickAccess->toggleNewWindow()) {
                throw new CannotUpdateQuickAccessException(
                    sprintf('Failed to toggle new_window for quick access "%d"', $command->getQuickAccessId()->getValue())
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateQuickAccessException(
                sprintf('An error occurred when toggling new_window for quick access "%d"', $command->getQuickAccessId()->getValue()),
                0,
                $e
            );
        }
    }
}
