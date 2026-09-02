<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\ToggleCarrierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\ToggleCarrierStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotToggleCarrierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShopException;

/**
 * Handles command that toggle carrier status
 */
#[AsCommandHandler]
class ToggleCarrierStatusHandler implements ToggleCarrierStatusHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ToggleCarrierStatusCommand $command)
    {
        $carrier = $this->carrierRepository->get($command->getCarrierId());

        try {
            if (false === $carrier->toggleStatus()) {
                throw new CannotToggleCarrierStatusException(sprintf('Unable to toggle status of carrier with id "%d"', $command->getCarrierId()->getValue()), CannotToggleCarrierStatusException::SINGLE_TOGGLE);
            }
        } catch (PrestaShopException $e) {
            throw new CarrierException(sprintf('An error occurred when toggling status of carrier with id "%d"', $command->getCarrierId()->getValue()), 0, $e);
        }
    }
}
