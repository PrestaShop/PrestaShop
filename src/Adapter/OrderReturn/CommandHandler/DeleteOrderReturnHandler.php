<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\DeleteOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\DeleteOrderReturnHandlerInterface;

#[AsCommandHandler]
class DeleteOrderReturnHandler implements DeleteOrderReturnHandlerInterface
{
    private OrderReturnRepository $orderReturnRepository;

    public function __construct(OrderReturnRepository $orderReturnRepository)
    {
        $this->orderReturnRepository = $orderReturnRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteOrderReturnCommand $command): void
    {
        $this->orderReturnRepository->delete($command->getOrderReturnId());
    }
}
