<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\ToggleCarrierIsFreeCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\ToggleCarrierIsFreeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotToggleCarrierIsFreeStatusException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierException;
use PrestaShopException;

#[AsCommandHandler]
class ToggleCarrierIsFreeHandler implements ToggleCarrierIsFreeHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ToggleCarrierIsFreeCommand $command)
    {
        $carrier = $this->carrierRepository->get($command->getCarrierId());

        try {
            $carrier->setFieldsToUpdate(['is_free' => true]);
            $carrier->is_free = !(bool) $carrier->is_free;

            if (false === $carrier->update()) {
                throw new CannotToggleCarrierIsFreeStatusException(sprintf('Unable to toggle is-free status of carrier with id "%d"', $command->getCarrierId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CarrierException(sprintf('An error occurred when toggling is-free status of carrier with id "%d"', $command->getCarrierId()->getValue()), 0, $e);
        }
    }
}
