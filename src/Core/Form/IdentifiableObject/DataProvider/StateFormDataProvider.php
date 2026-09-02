<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Query\GetStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\State\QueryResult\EditableState;

/**
 * Provides data for state add/edit form
 */
class StateFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $defaultCountryId;

    /**
     * @param CommandBusInterface $queryBus
     * @param int $defaultCountryId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        int $defaultCountryId
    ) {
        $this->queryBus = $queryBus;
        $this->defaultCountryId = $defaultCountryId;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($stateId)
    {
        /** @var EditableState $editableState */
        $editableState = $this->queryBus->handle(new GetStateForEditing((int) $stateId));

        return [
            'id_state' => $editableState->getStateId(),
            'id_zone' => $editableState->getZoneId()->getValue(),
            'id_country' => $editableState->getCountryId()->getValue(),
            'name' => $editableState->getName(),
            'iso_code' => $editableState->getIsoCode(),
            'active' => $editableState->isEnabled(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'id_country' => $this->defaultCountryId,
            'active' => true,
        ];
    }
}
