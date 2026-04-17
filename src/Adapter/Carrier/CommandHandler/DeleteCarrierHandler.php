<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\DeleteCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\DeleteCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotDeleteCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShopException;

/**
 * Handles command that deletes carrier
 */
#[AsCommandHandler]
class DeleteCarrierHandler implements DeleteCarrierHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCarrierCommand $command)
    {
        $carrier = $this->carrierRepository->get($command->getCarrierId());

        try {
            if (!$carrier->delete()) {
                throw new CannotDeleteCarrierException(sprintf('Cannot delete carrier object with id "%d"', $command->getCarrierId()->getValue()), CannotDeleteCarrierException::SINGLE_DELETE);
            }
        } catch (PrestaShopException) {
            throw new CarrierException(sprintf('An error occurred when deleting carrier with id "%d"', $command->getCarrierId()->getValue()));
        }
    }
}
