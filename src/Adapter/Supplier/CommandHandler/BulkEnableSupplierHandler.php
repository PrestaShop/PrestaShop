<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkEnableSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\BulkEnableSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotUpdateSupplierStatusException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Supplier;

/**
 * Class BulkEnableSupplierHandler is responsible for enabling multiple suppliers.
 */
#[AsCommandHandler]
final class BulkEnableSupplierHandler implements BulkEnableSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(BulkEnableSupplierCommand $command)
    {
        try {
            foreach ($command->getSupplierIds() as $supplierId) {
                $entity = new Supplier($supplierId->getValue());

                if (0 >= $entity->id) {
                    throw new SupplierNotFoundException(sprintf('Supplier object with id "%s" has not been found for enabling status.', $supplierId->getValue()));
                }

                $entity->active = true;

                if (false === $entity->update()) {
                    throw new CannotUpdateSupplierStatusException(sprintf('Unable to enable supplier object with id "%s"', $supplierId->getValue()));
                }
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException('Unexpected error occurred when handling bulk enable supplier', 0, $e);
        }
    }
}
