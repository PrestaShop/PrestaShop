<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Hook\CommandHandler;

use Hook;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\CommandHandler\UpdateHookStatusCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;

/**
 * @internal
 */
#[AsCommandHandler]
class UpdateHookStatusCommandHandler implements UpdateHookStatusCommandHandlerInterface
{
    /**
     * @param UpdateHookStatusCommand $command
     */
    public function handle(UpdateHookStatusCommand $command)
    {
        $hookId = $command->getHookId()->getValue();
        $hook = new Hook($hookId);

        if ($hook->id !== $hookId) {
            throw new HookNotFoundException(sprintf('Hook with id "%d" was not found', $hookId));
        }

        $hook->active = $command->isActive();
        if (!$hook->save()) {
            throw new CannotUpdateHookException(sprintf('Cannot update status for hook with id "%d"', $hookId));
        }
    }
}
