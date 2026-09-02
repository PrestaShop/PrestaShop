<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\DeleteStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\DeleteStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use State;

/**
 * Handles command that delete state
 */
#[AsCommandHandler]
class DeleteStateHandler implements DeleteStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteStateCommand $command): void
    {
        $state = new State($command->getStateId()->getValue());

        if (0 >= $state->id) {
            throw new StateNotFoundException(sprintf('Unable to find state with id "%d" for deletion', $command->getStateId()->getValue()));
        }

        if (!$state->delete()) {
            throw DeleteStateException::createDeleteFailure($command->getStateId());
        }
    }
}
