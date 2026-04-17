<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Adapter\OrderReturnState\Repository\OrderReturnStateRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\UpdateOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;

#[AsCommandHandler]
class UpdateOrderReturnStateHandler implements UpdateOrderReturnStateHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    /**
     * @var OrderReturnStateRepository
     */
    private $orderReturnStateRepository;

    /**
     * UpdateOrderReturnStateHandler constructor.
     *
     * @param OrderReturnRepository $orderReturnRepository
     * @param OrderReturnStateRepository $orderReturnStateRepository
     */
    public function __construct(
        OrderReturnRepository $orderReturnRepository,
        OrderReturnStateRepository $orderReturnStateRepository
    ) {
        $this->orderReturnRepository = $orderReturnRepository;
        $this->orderReturnStateRepository = $orderReturnStateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateOrderReturnStateCommand $command): void
    {
        $orderReturn = $this->orderReturnRepository->get($command->getOrderReturnId());
        $orderReturn = $this->updateOrderReturnWithCommandData($orderReturn, $command);

        $this->orderReturnRepository->update($orderReturn);
    }

    /**
     * @param OrderReturn $orderReturn
     * @param UpdateOrderReturnStateCommand $command
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     */
    private function updateOrderReturnWithCommandData(OrderReturn $orderReturn, UpdateOrderReturnStateCommand $command): OrderReturn
    {
        $orderReturnState = $this->orderReturnStateRepository->get($command->getOrderReturnStateId());
        $orderReturn->state = $orderReturnState->id;

        return $orderReturn;
    }
}
