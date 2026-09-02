<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\BulkToggleCarrierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\BulkToggleCarrierStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotToggleCarrierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShopException;

/**
 * Bulk toggles carriers status
 */
#[AsCommandHandler]
class BulkToggleCarrierStatusHandler implements BulkToggleCarrierStatusHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleCarrierStatusCommand $command)
    {
        foreach ($command->getCarrierIds() as $carrierId) {
            $carrier = $this->carrierRepository->get($carrierId);
            $carrier->active = $command->getExpectedStatus();

            try {
                if (!$carrier->save()) {
                    throw new CannotToggleCarrierStatusException(sprintf('Cannot toggle status of carrier with id "%d"', $carrierId->getValue()), CannotToggleCarrierStatusException::BULK_TOGGLE);
                }
            } catch (PrestaShopException) {
                throw new CarrierException(sprintf('An error occurred when updating status of carrier with id "%d"', $carrierId->getValue()));
            }
        }
    }
}
