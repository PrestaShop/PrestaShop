<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryHandler\GetStateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use State;

/**
 * Handles command that gets state for editing
 *
 * @internal
 */
#[AsQueryHandler]
class GetStateForEditingHandler implements GetStateForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetStateForEditing $query): EditableState
    {
        $stateId = $query->getStateId();
        $state = new State($stateId->getValue());

        if ($state->id !== $stateId->getValue()) {
            throw new StateNotFoundException(sprintf('State with id "%d" not found', $stateId->getValue()));
        }

        return new EditableState(
            $stateId,
            new CountryId((int) $state->id_country),
            new ZoneId((int) $state->id_zone),
            (string) $state->name,
            $state->iso_code,
            (bool) $state->active,
            $state->getAssociatedShops()
        );
    }
}
