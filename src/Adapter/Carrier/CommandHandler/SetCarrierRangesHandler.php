<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRangeRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\SetCarrierRangesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

/**
 * Handles query which gets carrier range
 */
#[AsCommandHandler]
final class SetCarrierRangesHandler implements SetCarrierRangesHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly CarrierRangeRepository $carrierRangeRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetCarrierRangesCommand $command): CarrierId
    {
        // Get new version of carrier if needed
        $newCarrier = $this->carrierRepository->getEditableOrNewVersion($command->getCarrierId());
        $newCarrierId = new CarrierId($newCarrier->id);

        // Set carrier ranges
        $this->carrierRangeRepository->set(
            $newCarrierId,
            $command->getRanges(),
            $command->getShopConstraint()
        );

        return $newCarrierId;
    }
}
