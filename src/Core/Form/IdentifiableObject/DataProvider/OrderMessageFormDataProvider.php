<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Query\GetOrderMessageForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\QueryResult\EditableOrderMessage;

/**
 * Provides add/edit data for order message form
 */
final class OrderMessageFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderMessageId)
    {
        /** @var EditableOrderMessage $editableOrderMessage */
        $editableOrderMessage = $this->queryBus->handle(new GetOrderMessageForEditing((int) $orderMessageId));

        return [
            'name' => $editableOrderMessage->getLocalizedName(),
            'message' => $editableOrderMessage->getLocalizedMessage(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [];
    }
}
