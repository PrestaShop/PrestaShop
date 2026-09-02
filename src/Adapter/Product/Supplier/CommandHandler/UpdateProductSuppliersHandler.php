<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSuppliersHandlerInterface;

/**
 * Handles @see UpdateProductSuppliersCommand using legacy object model
 */
#[AsCommandHandler]
final class UpdateProductSuppliersHandler extends AbstractProductSupplierHandler implements UpdateProductSuppliersHandlerInterface
{
    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param ProductSupplierUpdater $productSupplierUpdater
     * @param ProductSupplierRepository $productSupplierRepository
     */
    public function __construct(
        ProductSupplierUpdater $productSupplierUpdater,
        ProductSupplierRepository $productSupplierRepository
    ) {
        parent::__construct($productSupplierRepository);
        $this->productSupplierUpdater = $productSupplierUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSuppliersCommand $command): array
    {
        $productId = $command->getProductId();
        $productSuppliers = [];

        foreach ($command->getProductSuppliers() as $productSupplierDTO) {
            $productSuppliers[] = $this->loadEntityFromDTO($productSupplierDTO);
        }

        return $this->productSupplierUpdater->updateSuppliersForProduct(
            $productId,
            $productSuppliers
        );
    }
}
