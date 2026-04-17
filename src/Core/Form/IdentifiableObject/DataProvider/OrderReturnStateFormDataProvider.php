<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Query\GetOrderReturnStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryResult\EditableOrderReturnState;

/**
 * Provides data for order return state forms
 */
final class OrderReturnStateFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    public function __construct(
        CommandBusInterface $queryBus
    ) {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderStateId)
    {
        /** @var EditableOrderReturnState $editableOrderReturnState */
        $editableOrderReturnState = $this->queryBus->handle(new GetOrderReturnStateForEditing((int) $orderStateId));

        return [
            'name' => $editableOrderReturnState->getLocalizedNames(),
            'color' => $editableOrderReturnState->getColor(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'color' => '#ffffff',
        ];
    }
}
