<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDisableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\BulkDisableSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class BulkDisableSupplierHandler is responsible for disabling multiple suppliers.
 */
#[AsCommandHandler]
final class BulkDisableSupplierHandler implements BulkDisableSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(BulkDisableSupplierCommand $command)
    {
        try {
            foreach ($command->getSupplierIds() as $supplierId) {
                $entity = new Supplier($supplierId->getValue());

                if (0 >= $entity->id) {
                    throw new SupplierNotFoundException(sprintf('Supplier object with id "%s" has not been found for disabling status.', $supplierId->getValue()));
                }

                $entity->active = false;

                if (false === $entity->update()) {
                    throw new CannotUpdateSupplierStatusException(sprintf('Unable to disable supplier object with id "%s"', $supplierId->getValue()));
                }
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException('Unexpected error occurred when handling bulk disable supplier', 0, $e);
        }
    }
}
