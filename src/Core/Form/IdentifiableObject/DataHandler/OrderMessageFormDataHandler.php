<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\AddOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\EditOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Handles data that was submitted with order message form
 */
final class OrderMessageFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        /** @var OrderMessageId $orderMessageId */
        $orderMessageId = $this->commandBus->handle(new AddOrderMessageCommand($data['name'], $data['message']));

        return $orderMessageId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($orderMessageId, array $data)
    {
        $this->commandBus->handle(new EditOrderMessageCommand($orderMessageId, $data['name'], $data['message']));
    }
}
