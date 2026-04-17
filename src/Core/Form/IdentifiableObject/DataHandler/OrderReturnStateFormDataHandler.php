<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\AddOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\EditOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Saves or updates order return state data submitted in form
 */
final class OrderReturnStateFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    public function __construct(
        CommandBusInterface $bus
    ) {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = $this->buildOrderReturnStateAddCommandFromFormData($data);

        /** @var OrderReturnStateId $orderReturnStateId */
        $orderReturnStateId = $this->bus->handle($command);

        return $orderReturnStateId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($orderReturnStateId, array $data)
    {
        $command = $this->buildOrderReturnStateEditCommand($orderReturnStateId, $data);

        $this->bus->handle($command);
    }

    /**
     * @return AddOrderReturnStateCommand
     */
    private function buildOrderReturnStateAddCommandFromFormData(array $data)
    {
        $command = new AddOrderReturnStateCommand(
            $data['name'],
            $data['color']
        );

        return $command;
    }

    /**
     * @param int $orderReturnStateId
     *
     * @return EditOrderReturnStateCommand
     */
    private function buildOrderReturnStateEditCommand($orderReturnStateId, array $data)
    {
        $command = (new EditOrderReturnStateCommand($orderReturnStateId))
            ->setName($data['name'])
            ->setColor($data['color'])
        ;

        return $command;
    }
}
