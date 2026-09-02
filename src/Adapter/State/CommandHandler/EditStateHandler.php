<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\EditStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\EditStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;
use State;

/**
 * Handles state editing
 */
#[AsCommandHandler]
class EditStateHandler implements EditStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateStateException
     * @throws StateConstraintException
     * @throws StateException
     * @throws StateNotFoundException
     */
    public function handle(EditStateCommand $command): void
    {
        $state = $this->getState($command->getStateId());

        try {
            if (null !== $command->getZoneId()) {
                $state->id_zone = $command->getZoneId()->getValue();
            }

            if (null !== $command->getCountryId()) {
                $state->id_country = $command->getCountryId()->getValue();
            }

            if (null !== $command->getIsoCode()) {
                $state->iso_code = $command->getIsoCode();
            }

            if (null !== $command->getName()) {
                $state->name = $command->getName();
            }

            if (null !== $command->getActive()) {
                $state->active = $command->getActive();
            }

            $isValid = $state->validateFields(true);
            if ($isValid !== true) {
                throw new StateConstraintException('State contains invalid field values: ' . $isValid);
            }

            if (false === $state->update()) {
                throw new CannotUpdateStateException('Failed to update state');
            }
        } catch (PrestaShopException $e) {
            throw new StateException('An unexpected error occurred when updating state', 0, $e);
        }
    }

    /**
     * @param StateId $stateId
     *
     * @return State
     *
     * @throws StateNotFoundException
     * @throws StateException
     */
    private function getState(StateId $stateId): State
    {
        $stateIdValue = $stateId->getValue();

        try {
            $state = new State($stateIdValue);
        } catch (PrestaShopException $e) {
            throw new StateException(sprintf('Failed to get state with id: "%s"', $stateIdValue), 0, $e);
        }

        if ($state->id !== $stateIdValue) {
            throw new StateNotFoundException(sprintf('State with id "%s" was not found.', $stateIdValue));
        }

        return $state;
    }
}
