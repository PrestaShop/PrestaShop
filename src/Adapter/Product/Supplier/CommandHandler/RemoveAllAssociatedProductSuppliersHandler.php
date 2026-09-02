<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\RemoveAllAssociatedProductSuppliersHandlerInterface;

/**
 * Handles @see RemoveAllAssociatedProductSuppliersCommand using legacy object model
 */
#[AsCommandHandler]
final class RemoveAllAssociatedProductSuppliersHandler implements RemoveAllAssociatedProductSuppliersHandlerInterface
{
    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param ProductSupplierUpdater $productSupplierUpdater
     */
    public function __construct(
        ProductSupplierUpdater $productSupplierUpdater
    ) {
        $this->productSupplierUpdater = $productSupplierUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveAllAssociatedProductSuppliersCommand $command): void
    {
        $this->productSupplierUpdater->removeAllForProduct($command->getProductId());
    }
}
