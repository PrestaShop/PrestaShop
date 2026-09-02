<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\DeleteProductFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\DeleteProductFromOrderReturnHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\CannotDeleteLastProductFromOrderReturnException;

#[AsCommandHandler]
class DeleteProductFromOrderReturnHandler implements DeleteProductFromOrderReturnHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    public function __construct(OrderReturnRepository $orderReturnRepository)
    {
        $this->orderReturnRepository = $orderReturnRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductFromOrderReturnCommand $command): void
    {
        // Mirrors AdminReturnController::postProcess: the operation must leave at least one row.
        if ($this->orderReturnRepository->countProductLines($command->getOrderReturnId()) <= 1) {
            throw new CannotDeleteLastProductFromOrderReturnException(
                'A merchandise return must contain at least one product.'
            );
        }

        $this->orderReturnRepository->deleteProductLine($command->getOrderReturnId(), $command->getProductId());
    }
}
