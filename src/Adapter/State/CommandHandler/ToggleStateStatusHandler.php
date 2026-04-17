<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\ToggleStateStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\ToggleStateStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotToggleStateStatusException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShopException;
use State;

#[AsCommandHandler]
class ToggleStateStatusHandler implements ToggleStateStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws StateException
     */
    public function handle(ToggleStateStatusCommand $command): void
    {
        try {
            $state = new State($command->getStateId()->getValue());

            if (0 >= $state->id) {
                throw new StateNotFoundException(sprintf('State object with id "%d" has been not found for status changing', $command->getStateId()->getValue()));
            }

            if (false === $state->toggleStatus()) {
                throw new CannotToggleStateStatusException(sprintf('Unable to toggle status of state with id "%d"', $command->getStateId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new StateException(
                sprintf('An error occurred when toggling status for state with id "%d"', $command->getStateId()->getValue()),
                0,
                $e
            );
        }
    }
}
