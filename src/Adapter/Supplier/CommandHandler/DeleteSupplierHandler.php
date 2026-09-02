<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler\DeleteSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;

/**
 * Class DeleteSupplierHandler is responsible for deleting the supplier.
 */
#[AsCommandHandler]
final class DeleteSupplierHandler extends AbstractDeleteSupplierHandler implements DeleteSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function handle(DeleteSupplierCommand $command)
    {
        $supplierId = $command->getSupplierId();
        try {
            $this->removeSupplier($supplierId);
        } catch (SupplierException $e) {
            if (SupplierException::class === $e::class) {
                throw new CannotDeleteSupplierException(sprintf('Cannot delete Supplier object with id "%s".', $supplierId->getValue()), CannotDeleteSupplierException::FAILED_DELETE);
            }

            throw $e;
        }
    }
}
