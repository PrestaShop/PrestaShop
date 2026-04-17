<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkDeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\BulkDeleteStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\DeleteStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use State;

/**
 * Handles command that bulk delete states
 */
#[AsCommandHandler]
class BulkDeleteStateHandler implements BulkDeleteStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteStateCommand $command): void
    {
        foreach ($command->getStateIds() as $stateId) {
            $state = new State($stateId->getValue());

            if (0 >= $state->id) {
                throw new StateNotFoundException(sprintf('Unable to find state with id "%d" for deletion', $stateId->getValue()));
            }

            if (!$state->delete()) {
                throw DeleteStateException::createBulkDeleteFailure($stateId);
            }
        }
    }
}
