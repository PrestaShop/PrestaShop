<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\AddStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\AddStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotAddStateException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopException;
use State;

/**
 * Handles creation of state
 */
#[AsCommandHandler]
class AddStateHandler implements AddStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotAddStateException
     * @throws StateConstraintException
     * @throws StateException
     */
    public function handle(AddStateCommand $command): StateId
    {
        try {
            $state = new State();

            $state->name = $command->getName();
            $state->iso_code = $command->getIsoCode();
            $state->id_country = $command->getCountryId()->getValue();
            $state->id_zone = $command->getZoneId()->getValue();
            $state->active = $command->isActive();

            if ($state->validateFields(false, true) !== true) {
                throw new StateException('State contains invalid field values');
            }

            if (false === $state->add()) {
                throw new CannotAddStateException('Failed to add state');
            }
        } catch (PrestaShopException $e) {
            throw new StateException('An unexpected error occurred when adding state', 0, $e);
        }

        return new StateId((int) $state->id);
    }
}
