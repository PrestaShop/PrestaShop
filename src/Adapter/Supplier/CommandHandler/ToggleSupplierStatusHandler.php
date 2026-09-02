<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\ToggleSupplierStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotToggleSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class ToggleSupplierStatusHandler is responsible for toggling supplier status.
 */
#[AsCommandHandler]
final class ToggleSupplierStatusHandler implements ToggleSupplierStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(ToggleSupplierStatusCommand $command)
    {
        try {
            $entity = new Supplier($command->getSupplierId()->getValue());

            if (0 >= $entity->id) {
                throw new SupplierNotFoundException(sprintf('Supplier object with id "%s" has not been found for status changing.', $command->getSupplierId()->getValue()));
            }

            if (false === $entity->toggleStatus()) {
                throw new CannotToggleSupplierStatusException(sprintf('Unable to toggle supplier with id "%s"', $command->getSupplierId()->getValue()));
            }
        } catch (PrestaShopException $exception) {
            throw new SupplierException(sprintf('An error occurred when toggling status for supplier object with id "%s"', $command->getSupplierId()->getValue()), 0, $exception);
        }
    }
}
