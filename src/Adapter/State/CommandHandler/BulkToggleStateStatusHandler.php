<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\BulkToggleStateStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotToggleStateStatusException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShopException;
use State;

/**
 * Handles command that toggles states status in bulk action
 */
#[AsCommandHandler]
class BulkToggleStateStatusHandler implements BulkToggleStateStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleStateStatusCommand $command): void
    {
        foreach ($command->getStateIds() as $stateId) {
            $state = new State($stateId->getValue());

            if (0 >= $state->id) {
                throw new StateNotFoundException(sprintf('State object with id "%d" has not been found for status changing', $stateId->getValue()));
            }

            $state->active = $command->getExpectedStatus();

            try {
                if (!$state->save()) {
                    throw new CannotToggleStateStatusException(sprintf('Unable to toggle status for state with id "%d"', $stateId->getValue()));
                }
            } catch (PrestaShopException $e) {
                throw new StateException(
                    sprintf('An error occurred while updating state status with id "%d"', $stateId->getValue()),
                    0,
                    $e
                );
            }
        }
    }
}
