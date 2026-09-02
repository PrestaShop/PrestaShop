<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\BulkDeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\BulkDeleteSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;

/**
 * Class BulkDeleteSupplierHandler is responsible for deleting multiple suppliers.
 */
#[AsCommandHandler]
final class BulkDeleteSupplierHandler extends AbstractDeleteSupplierHandler implements BulkDeleteSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(BulkDeleteSupplierCommand $command)
    {
        foreach ($command->getSupplierIds() as $supplierId) {
            try {
                $this->removeSupplier($supplierId);
            } catch (SupplierException $e) {
                if (SupplierException::class === $e::class) {
                    throw new CannotDeleteSupplierException(sprintf('Cannot delete Supplier object with id "%s".', $supplierId->getValue()), CannotDeleteSupplierException::FAILED_BULK_DELETE);
                }

                throw $e;
            }
        }
    }
}
