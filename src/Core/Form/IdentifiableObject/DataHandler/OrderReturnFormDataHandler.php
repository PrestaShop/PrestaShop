<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use OrderReturn;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Exception\NotImplementedException;

/**
 * Saves or updates order return data submitted in form
 */
class OrderReturnFormDataHandler implements FormDataHandlerInterface
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
    public function update($orderReturnId, array $data): void
    {
        $orderReturnStateId = (int) $data['order_return_state'];

        $this->commandBus->handle(new UpdateOrderReturnStateCommand($orderReturnId, $orderReturnStateId));
    }

    /**
     * Order Return doesn't have a create option
     *
     * @param array $data
     *
     * @throws NotImplementedException
     */
    public function create(array $data): void
    {
        throw new NotImplementedException(OrderReturn::class . ' is not created by form, this method should never be called');
    }
}
