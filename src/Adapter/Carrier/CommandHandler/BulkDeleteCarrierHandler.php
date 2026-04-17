<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\BulkDeleteCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\BulkDeleteCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotDeleteCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShopException;

/**
 * Bulk deletes carriers
 */
#[AsCommandHandler]
class BulkDeleteCarrierHandler implements BulkDeleteCarrierHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteCarrierCommand $command)
    {
        foreach ($command->getCarrierIds() as $carrierId) {
            $carrier = $this->carrierRepository->get($carrierId);

            try {
                if (!$carrier->delete()) {
                    throw new CannotDeleteCarrierException(sprintf('Cannot delete carrier with id "%d"', $carrierId->getValue()), CannotDeleteCarrierException::BULK_DELETE);
                }
            } catch (PrestaShopException) {
                throw new CarrierException(sprintf('An error occurred when deleting carrier with id "%d"', $carrierId->getValue()));
            }
        }
    }
}
